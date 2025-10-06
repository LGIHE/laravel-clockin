<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $userRole = $request->user()->role;

        if (!$userRole || !in_array(strtolower($userRole), array_map('strtolower', $roles))) {
            // Log unauthorized access attempt
            Log::channel('security')->warning('Unauthorized access attempt', [
                'user_id' => $request->user()->id,
                'user_role' => $userRole,
                'required_roles' => $roles,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action. Insufficient permissions.',
            ], 403);
        }

        return $next($request);
    }
}
