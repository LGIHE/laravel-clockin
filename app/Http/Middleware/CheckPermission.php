<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!$request->user()) {
            return $this->unauthorized($request, 'Unauthenticated');
        }

        // Check if user has any of the required permissions
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if ($request->user()->hasPermission($permission)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            Log::channel('security')->warning('Unauthorized access attempt - insufficient permissions', [
                'user_id' => $request->user()->id,
                'user_role' => $request->user()->role,
                'required_permissions' => $permissions,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            return $this->unauthorized($request, 'Unauthorized action. Insufficient permissions.');
        }

        return $next($request);
    }

    private function unauthorized(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 403);
        }

        abort(403, $message);
    }
}
