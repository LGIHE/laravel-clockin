<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SettingsService;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, function ($app) {
            return new SettingsService();
        });

        // Create an alias for easier access
        $this->app->alias(SettingsService::class, 'settings');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Apply system settings after database connection is available
        try {
            $settings = $this->app->make(SettingsService::class);
            
            // Apply email settings
            $settings->applyEmailSettings();
            
            // Apply timezone settings
            $settings->applyTimezone();
        } catch (\Exception $e) {
            // Database might not be available yet (during migrations, etc.)
            // Silently fail and use default config values
        }
    }
}
