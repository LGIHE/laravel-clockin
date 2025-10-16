<?php

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
