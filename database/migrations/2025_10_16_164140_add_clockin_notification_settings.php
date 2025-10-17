<?php

use App\Models\SystemSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new system settings for clockin notifications
        $settings = [
            [
                'key' => 'enable_notice_notifications',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable or disable notice notifications',
                'group' => 'notifications',
            ],
            [
                'key' => 'clockin_reminder_time',
                'value' => '08:00',
                'type' => 'string',
                'description' => 'Time to send daily clockin reminder emails (HH:MM format)',
                'group' => 'notifications',
            ],
            [
                'key' => 'clockin_notification_recipients',
                'value' => '[]',
                'type' => 'json',
                'description' => 'User IDs who should receive clockin reminder emails',
                'group' => 'notifications',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        SystemSetting::whereIn('key', [
            'enable_notice_notifications',
            'clockin_reminder_time',
            'clockin_notification_recipients',
        ])->delete();
    }
};
