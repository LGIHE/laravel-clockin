<?php

namespace App\Console\Commands;

use App\Mail\ClockinReminderMail;
use App\Models\SystemSetting;
use App\Models\User;
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
        
        $this->info("Sending reminders to " . count($recipientIds) . " recipient(s)...");
        
        $sentCount = 0;
        $failedCount = 0;
        
        // Get all recipient users
        $recipients = User::whereIn('id', $recipientIds)
            ->where('status', 'active')
            ->get();
        
        foreach ($recipients as $user) {
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
        
        $this->info("\nClackin reminder process completed.");
        $this->info("✓ Successfully sent: {$sentCount}");
        
        if ($failedCount > 0) {
            $this->warn("✗ Failed: {$failedCount}");
        }
        
        Log::channel('daily')->info('Clockin reminder batch completed', [
            'total_recipients' => count($recipientIds),
            'sent' => $sentCount,
            'failed' => $failedCount,
        ]);
        
        return 0;
    }
}
