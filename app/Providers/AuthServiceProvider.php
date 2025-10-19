<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Super admin bypass (admin role gets all permissions)
        Gate::before(function ($user, $ability) {
            if ($user && $user->role === 'ADMIN') {
                return true;
            }
        });

        // Register all permissions as gates
        try {
            if (app()->environment() !== 'testing' && Schema::hasTable('permissions')) {
                Permission::all()->each(function ($permission) {
                    Gate::define($permission->slug, function ($user) use ($permission) {
                        return $user->hasPermission($permission->slug);
                    });
                });
            }
        } catch (\Exception $e) {
            // Permissions table might not exist yet during migration or fresh install
            Log::debug('Permission gates not registered: ' . $e->getMessage());
        }
    }
}
