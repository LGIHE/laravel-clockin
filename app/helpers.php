<?php

use App\Services\SettingsService;

if (!function_exists('settings')) {
    /**
     * Get the settings service instance or a specific setting value
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function settings(?string $key = null, $default = null)
    {
        $settings = app(SettingsService::class);
        
        if (is_null($key)) {
            return $settings;
        }
        
        return $settings->get($key, $default);
    }
}
