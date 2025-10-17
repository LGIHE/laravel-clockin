<?php

namespace App\Console\Commands;

use App\Mail\ClockinReminderMail;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveStatus;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendClockinReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-clockin-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily clockin reminder emails to selected staff members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting clockin reminder email process...');
        
        // Check if attendance notifications are enabled
        if (!SystemSetting::get('enable_attendance_notifications', true)) {
            $this->warn('Attendance notifications are disabled. Skipping reminder emails.');
            Log::channel('daily')->info('Clockin reminders skipped - attendance notifications disabled');
            return 0;
        }
        
        // Check if email notifications are enabled
        if (!SystemSetting::get('enable_email_notifications', true)) {
            $this->warn('Email notifications are disabled. Skipping reminder emails.');
            Log::channel('daily')->info('Clockin reminders skipped - email notifications disabled');
            return 0;
        }
        
        // Get recipient user IDs
        $recipientIds = SystemSetting::get('clockin_notification_recipients', []);
        
        // Ensure it's an array
        if (!is_array($recipientIds)) {
            $recipientIds = json_decode($recipientIds, true) ?? [];
        }
        
        if (empty($recipientIds)) {
            $this->warn('No recipients configured for clockin reminders.');
            Log::channel('daily')->info('Clockin reminders skipped - no recipients configured');
            return 0;
        }
        
        // Get the clockin reminder time
        $clockinTime = SystemSetting::get('clockin_reminder_time', '08:00');
        
        // Check if today is a public holiday
        $today = Carbon::today();
        $isPublicHoliday = Holiday::whereDate('date', $today)->exists();
        
        if ($isPublicHoliday) {
            $holiday = Holiday::whereDate('date', $today)->first();
            $this->warn("Today is a public holiday: {$holiday->name}. Skipping all reminders.");
            Log::channel('daily')->info('Clockin reminders skipped - public holiday', [
                'holiday_name' => $holiday->name,
                'date' => $today->format('Y-m-d'),
            ]);
            return 0;
        }
        
        $this->info("Sending reminders to " . count($recipientIds) . " recipient(s)...");
        
        // Get the "Granted" leave status ID
        $grantedStatusId = LeaveStatus::where('name', 'Granted')->value('id');
        
        // Get users who are on approved leave today
        $usersOnLeave = Leave::where('leave_status_id', $grantedStatusId)
            ->whereDate('date', $today)
            ->pluck('user_id')
            ->toArray();
        
        $sentCount = 0;
        $failedCount = 0;
        $skippedCount = 0;
        
        // Get all recipient users
        $recipients = User::whereIn('id', $recipientIds)
            ->where('status', 1)
            ->get();
        
        foreach ($recipients as $user) {
            // Skip if user is on leave today
            if (in_array($user->id, $usersOnLeave)) {
                $skippedCount++;
                $this->info("⊘ Skipped: {$user->name} (on approved leave)");
                
                Log::channel('daily')->info('Clockin reminder skipped - user on leave', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'date' => $today->format('Y-m-d'),
                ]);
                
                continue;
            }
            
            try {
                // Send the reminder email
                Mail::to($user->email)->send(new ClockinReminderMail($user, $clockinTime));
                
                $sentCount++;
                $this->info("✓ Sent reminder to: {$user->name} ({$user->email})");
                
                Log::channel('daily')->info('Clockin reminder sent', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'clockin_time' => $clockinTime,
                ]);
                
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("✗ Failed to send to {$user->name}: {$e->getMessage()}");
                
                Log::channel('daily')->error('Clockin reminder failed', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        $this->info("\nClockin reminder process completed.");
        $this->info("✓ Successfully sent: {$sentCount}");
        
        if ($skippedCount > 0) {
            $this->info("⊘ Skipped (on leave): {$skippedCount}");
        }
        
        if ($failedCount > 0) {
            $this->warn("✗ Failed: {$failedCount}");
        }
        
        Log::channel('daily')->info('Clockin reminder batch completed', [
            'total_recipients' => count($recipientIds),
            'sent' => $sentCount,
            'skipped' => $skippedCount,
            'failed' => $failedCount,
        ]);
        
        return 0;
    }
}
