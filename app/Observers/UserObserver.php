<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->clearCaches($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->clearCaches($user);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->clearCaches($user);
    }

    /**
     * Clear relevant caches when user changes.
     */
    protected function clearCaches(User $user): void
    {
        // Clear user-specific cache
        Cache::forget("user:{$user->id}");
        Cache::forget("user_stats:{$user->id}:" . now()->format('Y-m'));
        
        // Clear admin stats
        Cache::forget('admin_system_stats');
        
        // Clear supervisor team cache if user has a supervisor
        if ($user->supervisor_id) {
            Cache::forget("supervisor_team:{$user->supervisor_id}");
        }
        
        // Clear department cache if user belongs to a department
        if ($user->department_id) {
            Cache::forget("department_users:{$user->department_id}");
        }
    }
}
