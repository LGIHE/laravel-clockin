<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class SettingsService
{
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return SystemSetting::get($key, $default);
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @param string|null $description
     * @return bool
     */
    public function set(string $key, $value, string $type = 'string', string $group = 'general', ?string $description = null): bool
    {
        return SystemSetting::set($key, $value, $type, $group, $description);
    }

    /**
     * Get application name
     *
     * @return string
     */
    public function appName(): string
    {
        return $this->get('app_name', config('app.name'));
    }

    /**
     * Get application logo path
     *
     * @return string|null
     */
    public function appLogo(): ?string
    {
        return $this->get('app_logo');
    }

    /**
     * Get application address
     *
     * @return string|null
     */
    public function appAddress(): ?string
    {
        return $this->get('app_address');
    }

    /**
     * Get application contact
     *
     * @return string|null
     */
    public function appContact(): ?string
    {
        return $this->get('app_contact');
    }

    /**
     * Get application email
     *
     * @return string|null
     */
    public function appEmail(): ?string
    {
        return $this->get('app_email');
    }

    /**
     * Get auto punch out time
     *
     * @return string
     */
    public function autoPunchOutTime(): string
    {
        return $this->get('auto_punch_out_time', '18:00');
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function timezone(): string
    {
        return $this->get('timezone', config('app.timezone'));
    }

    /**
     * Get date format
     *
     * @return string
     */
    public function dateFormat(): string
    {
        return $this->get('date_format', 'Y-m-d');
    }

    /**
     * Get time format
     *
     * @return string
     */
    public function timeFormat(): string
    {
        return $this->get('time_format', 'H:i:s');
    }

    /**
     * Check if email notifications are enabled
     *
     * @return bool
     */
    public function emailNotificationsEnabled(): bool
    {
        return $this->get('enable_email_notifications', true);
    }

    /**
     * Check if leave notifications are enabled
     *
     * @return bool
     */
    public function leaveNotificationsEnabled(): bool
    {
        return $this->get('enable_leave_notifications', true);
    }

    /**
     * Check if attendance notifications are enabled
     *
     * @return bool
     */
    public function attendanceNotificationsEnabled(): bool
    {
        return $this->get('enable_attendance_notifications', true);
    }

    /**
     * Check if task notifications are enabled
     *
     * @return bool
     */
    public function taskNotificationsEnabled(): bool
    {
        return $this->get('enable_task_notifications', true);
    }

    /**
     * Apply email settings to config
     *
     * @return void
     */
    public function applyEmailSettings(): void
    {
        $mailer = $this->get('mail_mailer', 'smtp');
        $host = $this->get('mail_host', 'smtp.gmail.com');
        $port = $this->get('mail_port', 587);
        $username = $this->get('mail_username');
        $password = $this->get('mail_password');
        $encryption = $this->get('mail_encryption', 'tls');
        $fromAddress = $this->get('mail_from_address', 'noreply@example.com');
        $fromName = $this->get('mail_from_name', config('app.name'));

        Config::set('mail.default', $mailer);
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', $port);
        Config::set('mail.mailers.smtp.username', $username);
        Config::set('mail.mailers.smtp.password', $password);
        Config::set('mail.mailers.smtp.encryption', $encryption);
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', $fromName);
    }

    /**
     * Apply timezone settings
     *
     * @return void
     */
    public function applyTimezone(): void
    {
        $timezone = $this->timezone();
        Config::set('app.timezone', $timezone);
        date_default_timezone_set($timezone);
    }

    /**
     * Get all settings by group
     *
     * @param string $group
     * @return \Illuminate\Support\Collection
     */
    public function getGroup(string $group)
    {
        return SystemSetting::getGroup($group);
    }

    /**
     * Clear settings cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        SystemSetting::clearCache();
    }
}
