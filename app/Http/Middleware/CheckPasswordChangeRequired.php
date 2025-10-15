<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChangeRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (auth()->check()) {
            $user = auth()->user();
            
            // If user needs to change password and they're not already on the change password page
            if ($user->password_change_required && !$request->is('change-password') && !$request->is('logout')) {
                return redirect()->route('password.change.first-login');
            }
        }
        
        return $next($request);
    }
}
