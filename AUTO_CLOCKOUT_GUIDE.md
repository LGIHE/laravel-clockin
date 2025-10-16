# Auto Clock-Out Feature

## Overview
The auto clock-out feature automatically clocks out users who forget to manually clock out at the end of their workday. This ensures accurate time tracking and prevents users from being logged in indefinitely.

## How It Works

### Priority System
The system uses a priority-based approach for determining when to auto clock-out users:

1. **Individual User Setting** (Highest Priority)
   - If a user has a personal auto clock-out time set, that time is used
   - Set via User Management > Actions > Auto Punch Out Time

2. **Global Setting** (Default)
   - If no individual time is set, the global auto clock-out time applies
   - Set by admins in Settings > System Settings > Global Auto Clock Out Time

### Configuration

#### Global Auto Clock-Out Time (Admin Only)
1. Navigate to **Settings > System Settings**
2. Look for **Global Auto Clock Out Time** in the "Auto Clock Out Settings" section
3. Set your desired time (e.g., 18:00 for 6:00 PM)
4. Click **Save System Settings**

This global time will apply to all users who don't have an individual auto clock-out time configured.

#### Individual User Auto Clock-Out Time (Admin Only)
1. Navigate to **User Management**
2. Click the **Actions** dropdown for a user
3. Select **Auto Punch Out Time**
4. Set the time for that specific user
5. Click **Save**

### Automated Process
The system runs an automated check every 5 minutes to:
1. Find all users currently clocked in
2. Check if their auto clock-out time has passed
3. Automatically clock them out at their designated time
4. Log the action for audit purposes

### Scheduled Task Setup

The auto clock-out command runs automatically every 5 minutes via Laravel's scheduler.

#### For Production Servers
Add the following cron entry to your server:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

This single cron entry will handle all scheduled tasks, including the auto clock-out.

#### For Development/Local
You can run the scheduler manually:

```bash
php artisan schedule:work
```

Or test the command directly:

```bash
php artisan attendance:auto-clockout
```

### How It Determines Clock-Out Time

**Example Scenarios:**

1. **User with Individual Time**
   - User: John Doe
   - Individual auto clock-out: 17:00
   - Global auto clock-out: 18:00
   - **Result:** John will be clocked out at 17:00

2. **User without Individual Time**
   - User: Jane Smith
   - Individual auto clock-out: Not set
   - Global auto clock-out: 18:00
   - **Result:** Jane will be clocked out at 18:00

3. **No Settings Configured**
   - User: Bob Johnson
   - Individual auto clock-out: Not set
   - Global auto clock-out: Not set
   - **Result:** Bob will NOT be automatically clocked out

### Logging

All auto clock-out actions are logged to the application's log file with the following information:
- User ID and name
- Clock-in time
- Auto clock-out time
- Whether global or individual setting was used

Logs can be viewed in **Settings > System Logs**.

### Benefits

1. **Accurate Time Tracking**: Ensures work hours are properly recorded
2. **Prevents Overnight Clocking**: Users won't remain clocked in indefinitely
3. **Flexible Configuration**: Admin can set both global and individual times
4. **Audit Trail**: All auto clock-outs are logged for transparency
5. **Low Maintenance**: Runs automatically without manual intervention

### Technical Details

- **Command**: `attendance:auto-clockout`
- **Frequency**: Every 5 minutes
- **Location**: `app/Console/Commands/AutoClockOut.php`
- **Schedule**: Defined in `routes/console.php`
- **Service**: Uses `AttendanceService` for clock-out operations

### Notes

- The system uses the clock-in date to determine the auto clock-out datetime
- If a user clocks in on Monday at 8:00 AM with an auto clock-out time of 18:00, they will be clocked out on Monday at 18:00
- Times are based on the application's configured timezone (Settings > System Settings > Timezone)
- The scheduled task prevents overlapping to ensure it doesn't run multiple times simultaneously

### Troubleshooting

**Auto clock-out not working?**

1. Verify the cron job is running:
   ```bash
   crontab -l
   ```

2. Check if the schedule is registered:
   ```bash
   php artisan schedule:list
   ```

3. Test the command manually:
   ```bash
   php artisan attendance:auto-clockout
   ```

4. Check logs for any errors:
   - Navigate to Settings > System Logs
   - Look for "Auto clock-out" entries

5. Verify settings are configured:
   - Check Global Auto Clock-Out Time in System Settings
   - Check individual user settings if applicable
