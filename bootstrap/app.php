<?php

use App\Exceptions\BusinessLogicException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'password.change.required' => \App\Http\Middleware\CheckPasswordChangeRequired::class,
        ]);
        
        // Apply password change check to web routes
        $middleware->web(append: [
            \App\Http\Middleware\CheckPasswordChangeRequired::class,
        ]);
        
        // Configure rate limiting for API routes
        $middleware->throttleApi();
        
        // Configure CSRF protection
        $middleware->validateCsrfTokens(except: [
            'api/*', // API routes use token authentication
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception rendering for API requests
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $status = 500;
                $code = 'INTERNAL_ERROR';
                $message = 'An error occurred';
                $errors = null;

                // Handle validation exceptions
                if ($e instanceof ValidationException) {
                    $status = 422;
                    $code = 'VALIDATION_ERROR';
                    $message = 'The given data was invalid.';
                    $errors = $e->errors();
                }
                // Handle authentication exceptions
                elseif ($e instanceof AuthenticationException) {
                    $status = 401;
                    $code = 'AUTHENTICATION_ERROR';
                    $message = 'Unauthenticated.';
                    
                    // Log authentication failure
                    Log::channel('auth')->warning('Authentication failed', [
                        'ip' => $request->ip(),
                        'url' => $request->fullUrl(),
                        'user_agent' => $request->userAgent(),
                    ]);
                }
                // Handle authorization exceptions
                elseif ($e instanceof AuthorizationException) {
                    $status = 403;
                    $code = 'AUTHORIZATION_ERROR';
                    $message = 'This action is unauthorized.';
                    
                    // Log unauthorized access attempt
                    Log::channel('security')->warning('Unauthorized access attempt', [
                        'user_id' => $request->user()?->id,
                        'ip' => $request->ip(),
                        'url' => $request->fullUrl(),
                        'message' => $e->getMessage(),
                    ]);
                }
                // Handle model not found exceptions
                elseif ($e instanceof ModelNotFoundException) {
                    $status = 404;
                    $code = 'RESOURCE_NOT_FOUND';
                    $message = 'Resource not found.';
                }
                // Handle not found HTTP exceptions
                elseif ($e instanceof NotFoundHttpException) {
                    $status = 404;
                    $code = 'NOT_FOUND';
                    $message = 'The requested resource was not found.';
                }
                // Handle custom business logic exceptions
                elseif ($e instanceof BusinessLogicException) {
                    $status = $e->getStatusCode();
                    $code = $e->getErrorCode();
                    $message = $e->getMessage();
                }
                // Handle custom resource not found exceptions
                elseif ($e instanceof ResourceNotFoundException) {
                    $status = $e->getStatusCode();
                    $code = $e->getErrorCode();
                    $message = $e->getMessage();
                }
                // Handle other HTTP exceptions
                elseif ($e instanceof HttpException) {
                    $status = $e->getStatusCode();
                    $code = 'HTTP_ERROR';
                    $message = $e->getMessage() ?: 'An HTTP error occurred.';
                }
                // Handle all other exceptions
                else {
                    $status = 500;
                    $code = 'INTERNAL_ERROR';
                    $message = config('app.debug') ? $e->getMessage() : 'An internal error occurred.';
                    
                    // Log internal errors
                    Log::channel('daily')->error('Internal server error', [
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => config('app.debug') ? $e->getTraceAsString() : null,
                    ]);
                }

                $response = [
                    'success' => false,
                    'error' => [
                        'code' => $code,
                        'message' => $message,
                    ],
                ];

                // Add validation errors if present
                if ($errors !== null) {
                    $response['error']['errors'] = $errors;
                }

                // Add debug information in debug mode
                if (config('app.debug') && !($e instanceof ValidationException)) {
                    $response['error']['debug'] = [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => collect($e->getTrace())->take(5)->toArray(),
                    ];
                }

                return response()->json($response, $status);
            }
        });
    })->create();
