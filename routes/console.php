<?php

use App\Models\SystemSetting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the auto clock-out command to run every 5 minutes
Schedule::command('attendance:auto-clockout')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Schedule daily clockin reminder emails
// Gets the time from system settings, defaults to 08:00
$clockinReminderTime = SystemSetting::get('clockin_reminder_time', '08:00');
Schedule::command('attendance:send-clockin-reminders')
    ->dailyAt($clockinReminderTime)
    ->withoutOverlapping()
    ->runInBackground();

// Reset leave balances on January 1st every year
Schedule::command('leaves:reset-balances')
    ->yearlyOn(1, 1, '00:01') // January 1st at 00:01
    ->withoutOverlapping()
    ->runInBackground();
