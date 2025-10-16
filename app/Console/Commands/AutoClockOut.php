<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoClockOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-clockout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically clock out users who have not clocked out by their designated time';

    /**
     * The attendance service instance.
     *
     * @var AttendanceService
     */
    protected $attendanceService;

    /**
     * Create a new command instance.
     */
    public function __construct(AttendanceService $attendanceService)
    {
        parent::__construct();
        $this->attendanceService = $attendanceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto clock-out process...');
        
        // Get the global auto clock-out time from settings
        $globalAutoClockOutTime = SystemSetting::get('global_auto_clockout_time', '18:00');
        
        // Get current time
        $currentTime = Carbon::now();
        $currentTimeString = $currentTime->format('H:i');
        
        $this->info("Current time: {$currentTimeString}");
        $this->info("Global auto clock-out time: {$globalAutoClockOutTime}");
        
        // Find all users who are currently clocked in
        $activeAttendances = Attendance::whereNull('out_time')
            ->with('user')
            ->get();
        
        if ($activeAttendances->isEmpty()) {
            $this->info('No users currently clocked in.');
            return 0;
        }
        
        $this->info("Found {$activeAttendances->count()} users currently clocked in.");
        
        $clockedOutCount = 0;
        
        foreach ($activeAttendances as $attendance) {
            $user = $attendance->user;
            
            if (!$user) {
                continue;
            }
            
            // Determine which auto clock-out time to use
            // Priority: User's individual time > Global time
            $autoClockOutTime = $user->auto_punch_out_time 
                ? Carbon::parse($user->auto_punch_out_time)->format('H:i')
                : $globalAutoClockOutTime;
            
            // Skip if no auto clock-out time is set
            if (!$autoClockOutTime) {
                continue;
            }
            
            // Check if current time has passed the auto clock-out time
            // We need to check if the clock-out time for today has passed
            $autoClockOutDateTime = Carbon::parse($attendance->in_time->format('Y-m-d') . ' ' . $autoClockOutTime);
            
            // If the auto clock-out time has passed
            if ($currentTime->greaterThanOrEqualTo($autoClockOutDateTime)) {
                try {
                    // Clock out the user at their designated time
                    $this->attendanceService->clockOut(
                        $user->id,
                        'Automatically clocked out by system',
                        null
                    );
                    
                    // Update the out_time to be the designated auto clock-out time
                    // instead of current time for accuracy
                    $attendance->fresh()->update([
                        'out_time' => $autoClockOutDateTime,
                        'worked' => $attendance->in_time->diffInSeconds($autoClockOutDateTime),
                    ]);
                    
                    $clockedOutCount++;
                    
                    $this->info("✓ Clocked out: {$user->name} (ID: {$user->id}) at {$autoClockOutTime}");
                    
                    Log::channel('daily')->info('Auto clock-out executed', [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'clock_in_time' => $attendance->in_time->format('Y-m-d H:i:s'),
                        'auto_clock_out_time' => $autoClockOutDateTime->format('Y-m-d H:i:s'),
                        'used_global_time' => !$user->auto_punch_out_time,
                    ]);
                    
                } catch (\Exception $e) {
                    $this->error("✗ Failed to clock out {$user->name}: {$e->getMessage()}");
                    
                    Log::channel('daily')->error('Auto clock-out failed', [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                $this->info("- Skipped: {$user->name} (auto clock-out at {$autoClockOutTime})");
            }
        }
        
        $this->info("Auto clock-out process completed. Clocked out {$clockedOutCount} user(s).");
        
        return 0;
    }
}
