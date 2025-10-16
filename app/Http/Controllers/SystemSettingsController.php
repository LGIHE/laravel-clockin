<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class SystemSettingsController extends Controller
{
    /**
     * Display the system settings page.
     */
    public function index()
    {
        $settings = [
            'general' => SystemSetting::where('group', 'general')->get(),
            'email' => SystemSetting::where('group', 'email')->get(),
            'notification' => SystemSetting::where('group', 'notification')->get(),
            'system' => SystemSetting::where('group', 'system')->get(),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_address' => 'nullable|string|max:500',
            'app_contact' => 'nullable|string|max:255',
            'app_email' => 'nullable|email|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        SystemSetting::set('app_name', $request->app_name, 'string', 'general', 'Application Name');
        SystemSetting::set('app_address', $request->app_address, 'string', 'general', 'Application Address');
        SystemSetting::set('app_contact', $request->app_contact, 'string', 'general', 'Application Contact');
        SystemSetting::set('app_email', $request->app_email, 'string', 'general', 'Application Email');

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $logoPath = $request->file('app_logo')->store('logos', 'public');
            
            // Delete old logo if exists
            $oldLogo = SystemSetting::get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            SystemSetting::set('app_logo', $logoPath, 'file', 'general', 'Application Logo');
        }

        return back()->with('success', 'General settings updated successfully!');
    }

    /**
     * Update email configuration settings
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        SystemSetting::set('mail_mailer', $request->mail_mailer, 'string', 'email', 'Mail Mailer');
        SystemSetting::set('mail_host', $request->mail_host, 'string', 'email', 'Mail Host');
        SystemSetting::set('mail_port', $request->mail_port, 'integer', 'email', 'Mail Port');
        SystemSetting::set('mail_username', $request->mail_username, 'string', 'email', 'Mail Username');
        SystemSetting::set('mail_password', $request->mail_password, 'string', 'email', 'Mail Password');
        SystemSetting::set('mail_encryption', $request->mail_encryption, 'string', 'email', 'Mail Encryption');
        SystemSetting::set('mail_from_address', $request->mail_from_address, 'string', 'email', 'Mail From Address');
        SystemSetting::set('mail_from_name', $request->mail_from_name, 'string', 'email', 'Mail From Name');

        return back()->with('success', 'Email settings updated successfully!');
    }

    /**
     * Update system settings
     */
    public function updateSystem(Request $request)
    {
        $request->validate([
            'global_auto_clockout_time' => 'nullable|date_format:H:i',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
        ]);

        SystemSetting::set('global_auto_clockout_time', $request->global_auto_clockout_time, 'string', 'system', 'Global Auto Clock Out Time');
        SystemSetting::set('timezone', $request->timezone, 'string', 'system', 'Application Timezone');
        SystemSetting::set('date_format', $request->date_format, 'string', 'system', 'Date Format');
        SystemSetting::set('time_format', $request->time_format, 'string', 'system', 'Time Format');

        return back()->with('success', 'System settings updated successfully!');
    }

    /**
     * Update notification settings
     */
    public function updateNotification(Request $request)
    {
        $request->validate([
            'enable_email_notifications' => 'boolean',
            'enable_leave_notifications' => 'boolean',
            'enable_attendance_notifications' => 'boolean',
            'enable_task_notifications' => 'boolean',
        ]);

        SystemSetting::set('enable_email_notifications', $request->boolean('enable_email_notifications'), 'boolean', 'notification', 'Enable Email Notifications');
        SystemSetting::set('enable_leave_notifications', $request->boolean('enable_leave_notifications'), 'boolean', 'notification', 'Enable Leave Notifications');
        SystemSetting::set('enable_attendance_notifications', $request->boolean('enable_attendance_notifications'), 'boolean', 'notification', 'Enable Attendance Notifications');
        SystemSetting::set('enable_task_notifications', $request->boolean('enable_task_notifications'), 'boolean', 'notification', 'Enable Task Notifications');

        return back()->with('success', 'Notification settings updated successfully!');
    }

    /**
     * View system logs
     */
    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = '';
        
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            // Get last 1000 lines
            $logLines = explode("\n", $logs);
            $logs = implode("\n", array_slice($logLines, -1000));
        }

        return view('admin.settings.logs', compact('logs'));
    }

    /**
     * Clear system logs
     */
    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
        }

        return back()->with('success', 'System logs cleared successfully!');
    }

    /**
     * View system stats
     */
    public function stats()
    {
        $stats = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'disk_space' => $this->formatBytes(disk_free_space('/')),
            'total_disk_space' => $this->formatBytes(disk_total_space('/')),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ];

        return view('admin.settings.stats', compact('stats'));
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        return back()->with('success', 'Application cache cleared successfully!');
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
