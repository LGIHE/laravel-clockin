<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait ManagesCache
{
    /**
     * Invalidate user-related caches.
     *
     * @param string $userId
     * @return void
     */
    protected function invalidateUserCache(string $userId): void
    {
        Cache::forget("user:{$userId}");
        Cache::forget("user_stats:{$userId}:" . now()->format('Y-m'));
    }

    /**
     * Invalidate supervisor team cache.
     *
     * @param string $supervisorId
     * @return void
     */
    protected function invalidateSupervisorCache(string $supervisorId): void
    {
        Cache::forget("supervisor_team:{$supervisorId}");
    }

    /**
     * Invalidate admin dashboard caches.
     *
     * @return void
     */
    protected function invalidateAdminCache(): void
    {
        Cache::forget('admin_system_stats');
    }

    /**
     * Invalidate department-related caches.
     *
     * @param string $departmentId
     * @return void
     */
    protected function invalidateDepartmentCache(string $departmentId): void
    {
        Cache::forget("department_users:{$departmentId}");
    }

    /**
     * Invalidate all dashboard caches.
     *
     * @return void
     */
    protected function invalidateAllDashboardCaches(): void
    {
        Cache::forget('admin_system_stats');
        // Clear all supervisor team caches (pattern-based)
        Cache::flush(); // In production, use tags or more specific patterns
    }
}
