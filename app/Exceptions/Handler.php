<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Handle API requests with JSON responses
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions and return consistent JSON responses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleApiException($request, Throwable $exception): JsonResponse
    {
        $status = 500;
        $code = 'INTERNAL_ERROR';
        $message = 'An error occurred';
        $errors = null;

        // Handle validation exceptions
        if ($exception instanceof ValidationException) {
            $status = 422;
            $code = 'VALIDATION_ERROR';
            $message = 'The given data was invalid.';
            $errors = $this->formatValidationErrors($exception);
        }
        // Handle authentication exceptions
        elseif ($exception instanceof AuthenticationException) {
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
        elseif ($exception instanceof AuthorizationException) {
            $status = 403;
            $code = 'AUTHORIZATION_ERROR';
            $message = 'This action is unauthorized.';
            
            // Log unauthorized access attempt
            Log::channel('security')->warning('Unauthorized access attempt', [
                'user_id' => $request->user()?->id,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'message' => $exception->getMessage(),
            ]);
        }
        // Handle model not found exceptions
        elseif ($exception instanceof ModelNotFoundException) {
            $status = 404;
            $code = 'RESOURCE_NOT_FOUND';
            $message = 'Resource not found.';
        }
        // Handle not found HTTP exceptions
        elseif ($exception instanceof NotFoundHttpException) {
            $status = 404;
            $code = 'NOT_FOUND';
            $message = 'The requested resource was not found.';
        }
        // Handle custom business logic exceptions
        elseif ($exception instanceof BusinessLogicException) {
            $status = $exception->getStatusCode();
            $code = $exception->getErrorCode();
            $message = $exception->getMessage();
        }
        // Handle custom resource not found exceptions
        elseif ($exception instanceof ResourceNotFoundException) {
            $status = $exception->getStatusCode();
            $code = $exception->getErrorCode();
            $message = $exception->getMessage();
        }
        // Handle other HTTP exceptions
        elseif ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
            $code = 'HTTP_ERROR';
            $message = $exception->getMessage() ?: 'An HTTP error occurred.';
        }
        // Handle all other exceptions
        else {
            $status = 500;
            $code = 'INTERNAL_ERROR';
            $message = config('app.debug') ? $exception->getMessage() : 'An internal error occurred.';
            
            // Log internal errors
            Log::channel('daily')->error('Internal server error', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => config('app.debug') ? $exception->getTraceAsString() : null,
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
        if (config('app.debug') && !($exception instanceof ValidationException)) {
            $response['error']['debug'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => collect($exception->getTrace())->take(5)->toArray(),
            ];
        }

        return response()->json($response, $status);
    }

    /**
     * Format validation errors for consistent API response.
     *
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return array
     */
    private function formatValidationErrors(ValidationException $exception): array
    {
        $errors = [];
        
        foreach ($exception->errors() as $field => $messages) {
            $errors[$field] = $messages;
        }

        return $errors;
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $e)
    {
        // Log security-related exceptions to security channel
        if ($e instanceof AuthorizationException) {
            Log::channel('security')->warning('Authorization exception', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
        }

        // Log authentication exceptions to auth channel
        if ($e instanceof AuthenticationException) {
            Log::channel('auth')->warning('Authentication exception', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
        }

        parent::report($e);
    }
}
