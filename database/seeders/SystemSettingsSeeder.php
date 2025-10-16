<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => config('app.name', 'ClockIn'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application Name'
            ],
            [
                'key' => 'app_address',
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application Address'
            ],
            [
                'key' => 'app_contact',
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application Contact Number'
            ],
            [
                'key' => 'app_email',
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application Contact Email'
            ],

            // Email Settings
            [
                'key' => 'mail_mailer',
                'value' => 'smtp',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Mail Mailer'
            ],
            [
                'key' => 'mail_host',
                'value' => 'smtp.gmail.com',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Mail Host'
            ],
            [
                'key' => 'mail_port',
                'value' => '587',
                'type' => 'integer',
                'group' => 'email',
                'description' => 'Mail Port'
            ],
            [
                'key' => 'mail_username',
                'value' => '',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Mail Username'
            ],
            [
                'key' => 'mail_password',
                'value' => '',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Mail Password'
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'tls',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Mail Encryption'
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@example.com',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Mail From Address'
            ],
            [
                'key' => 'mail_from_name',
                'value' => config('app.name', 'ClockIn'),
                'type' => 'string',
                'group' => 'email',
                'description' => 'Mail From Name'
            ],

            // System Settings
            [
                'key' => 'global_auto_clockout_time',
                'value' => '18:00',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Global Auto Clock Out Time - applies to all users without individual settings'
            ],
            [
                'key' => 'auto_punch_out_time',
                'value' => '18:00',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Auto Punch Out Time (deprecated - use global_auto_clockout_time)'
            ],
            [
                'key' => 'timezone',
                'value' => config('app.timezone', 'UTC'),
                'type' => 'string',
                'group' => 'system',
                'description' => 'Application Timezone'
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Date Format'
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i:s',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Time Format'
            ],

            // Notification Settings
            [
                'key' => 'enable_email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notification',
                'description' => 'Enable Email Notifications'
            ],
            [
                'key' => 'enable_leave_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notification',
                'description' => 'Enable Leave Notifications'
            ],
            [
                'key' => 'enable_attendance_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notification',
                'description' => 'Enable Attendance Notifications'
            ],
            [
                'key' => 'enable_task_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notification',
                'description' => 'Enable Task Notifications'
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('System settings seeded successfully!');
    }
}
