<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogsSecurityEvents
{
    /**
     * Log a failed login attempt.
     *
     * @param string $email
     * @param string $ip
     * @param string|null $userAgent
     * @return void
     */
    protected function logFailedLogin(string $email, string $ip, ?string $userAgent = null): void
    {
        Log::channel('auth')->warning('Failed login attempt', [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Log a successful login.
     *
     * @param int|string $userId
     * @param string $email
     * @param string $ip
     * @param string|null $userAgent
     * @return void
     */
    protected function logSuccessfulLogin($userId, string $email, string $ip, ?string $userAgent = null): void
    {
        Log::channel('auth')->info('Successful login', [
            'user_id' => $userId,
            'email' => $email,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Log an unauthorized access attempt.
     *
     * @param int|string|null $userId
     * @param string $action
     * @param string $ip
     * @param string|null $resource
     * @return void
     */
    protected function logUnauthorizedAccess($userId, string $action, string $ip, ?string $resource = null): void
    {
        Log::channel('security')->warning('Unauthorized access attempt', [
            'user_id' => $userId,
            'action' => $action,
            'resource' => $resource,
            'ip' => $ip,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Log a suspicious activity.
     *
     * @param string $activity
     * @param array $context
     * @return void
     */
    protected function logSuspiciousActivity(string $activity, array $context = []): void
    {
        Log::channel('security')->warning('Suspicious activity detected', array_merge([
            'activity' => $activity,
            'timestamp' => now()->toDateTimeString(),
        ], $context));
    }

    /**
     * Log a password change.
     *
     * @param int|string $userId
     * @param string $ip
     * @return void
     */
    protected function logPasswordChange($userId, string $ip): void
    {
        Log::channel('security')->info('Password changed', [
            'user_id' => $userId,
            'ip' => $ip,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Log a password reset request.
     *
     * @param string $email
     * @param string $ip
     * @return void
     */
    protected function logPasswordResetRequest(string $email, string $ip): void
    {
        Log::channel('auth')->info('Password reset requested', [
            'email' => $email,
            'ip' => $ip,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Log a logout event.
     *
     * @param int|string $userId
     * @param string $ip
     * @return void
     */
    protected function logLogout($userId, string $ip): void
    {
        Log::channel('auth')->info('User logged out', [
            'user_id' => $userId,
            'ip' => $ip,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
