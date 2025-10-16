# Global Auto Clock-Out Feature - Implementation Summary

## Overview
Implemented a global auto clock-out feature that allows administrators to set a system-wide automatic clock-out time for all users who don't have individual auto clock-out times configured.

## Changes Made

### 1. Database Changes

#### Migration
- **File**: `database/migrations/2025_10_16_124715_add_global_auto_clockout_setting.php`
- Added `global_auto_clockout_time` system setting to the database
- Default value: 18:00 (6:00 PM)

#### Seeder Update
- **File**: `database/seeders/SystemSettingsSeeder.php`
- Added new system setting: `global_auto_clockout_time`
- Description: "Global Auto Clock Out Time - applies to all users without individual settings"
- Group: system
- Type: string

### 2. Backend Changes

#### New Console Command
- **File**: `app/Console/Commands/AutoClockOut.php`
- Command signature: `attendance:auto-clockout`
- Description: Automatically clocks out users who haven't clocked out by their designated time
- Features:
  - Checks all currently clocked-in users
  - Uses individual user time if set, otherwise uses global time
  - Logs all auto clock-out actions
  - Sets clock-out time to the designated time (not current time)
  - Prevents overlapping executions

#### Settings Service Update
- **File**: `app/Services/SettingsService.php`
- Added method: `globalAutoClockOutTime()`
- Returns the global auto clock-out time setting
- Default: 18:00

#### Controller Update
- **File**: `app/Http/Controllers/SystemSettingsController.php`
- Updated `updateSystem()` method to handle `global_auto_clockout_time`
- Validates time format (H:i)
- Saves to system_settings table

### 3. Frontend Changes

#### Settings View Update
- **File**: `resources/views/admin/settings/partials/system.blade.php`
- Added new section: "Auto Clock Out Settings"
- Visual improvements:
  - Blue-highlighted section with icon
  - Clear description of global vs individual settings
  - Informational note explaining priority system
- Field: `global_auto_clockout_time` (time input)

### 4. Scheduled Task Configuration

#### Console Routes
- **File**: `routes/console.php`
- Added scheduled command: `attendance:auto-clockout`
- Frequency: Every 5 minutes
- Features:
  - `withoutOverlapping()` - prevents concurrent executions
  - `runInBackground()` - doesn't block other tasks

### 5. Documentation

#### Auto Clock-Out Guide
- **File**: `AUTO_CLOCKOUT_GUIDE.md`
- Comprehensive guide covering:
  - How the feature works
  - Priority system (individual > global)
  - Configuration steps for admins
  - Automated process details
  - Scheduled task setup for production
  - Example scenarios
  - Logging information
  - Benefits
  - Technical details
  - Troubleshooting guide

## How It Works

### Priority System
1. **Individual User Setting** (Highest Priority)
   - User's personal `auto_punch_out_time` is used if set
   - Configured via User Management > Actions > Auto Punch Out Time

2. **Global Setting** (Default)
   - `global_auto_clockout_time` applies to users without individual settings
   - Configured via Settings > System Settings

3. **No Auto Clock-Out**
   - If neither setting exists, user is not automatically clocked out

### Execution Flow
1. Scheduled task runs every 5 minutes
2. Command retrieves global setting from database
3. Finds all users currently clocked in (no out_time)
4. For each user:
   - Determines which auto clock-out time to use
   - Checks if current time ≥ auto clock-out time
   - If yes, clocks out user at designated time
   - Logs the action
5. Reports number of users clocked out

### Clock-Out Time Accuracy
- The system sets the `out_time` to the **designated auto clock-out time**, not the current time
- Example: If auto clock-out is 18:00 and the command runs at 18:03, out_time will be 18:00
- This ensures accurate time tracking

## Testing Results

Successfully tested the implementation:
- ✅ Database seeding completed
- ✅ Migration executed successfully
- ✅ Command executed and clocked out 7 users
- ✅ Schedule registered (runs every 5 minutes)
- ✅ Settings page updated with new field
- ✅ Global setting stored and retrievable

## Production Deployment Steps

1. **Run Migration**
   ```bash
   php artisan migrate
   ```

2. **Seed Settings** (if needed)
   ```bash
   php artisan db:seed --class=SystemSettingsSeeder
   ```

3. **Clear Cache**
   ```bash
   php artisan optimize:clear
   ```

4. **⚠️ CRITICAL: Set Up Scheduler** 
   
   **The auto clock-out will NOT work without this step!**
   
   **Option A - Production (Recommended):**
   Add this cron job to your server:
   ```bash
   crontab -e
   # Add this line:
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   ```
   
   **Option B - Development:**
   Run the scheduler manually:
   ```bash
   php artisan schedule:work
   ```
   
   **Option C - Production with Supervisor:**
   See `SCHEDULER_SETUP_GUIDE.md` for supervisor configuration

5. **Configure Global Time**
   - Login as admin
   - Navigate to Settings > System Settings
   - Set "Global Auto Clock Out Time"
   - Save settings

6. **Verify Schedule**
   ```bash
   php artisan schedule:list
   # Should show: */5 * * * * php artisan attendance:auto-clockout
   ```

7. **Test Execution**
   ```bash
   php artisan attendance:auto-clockout
   # Should show current status and any clock-outs performed
   ```

## Files Modified

1. `resources/views/admin/settings/partials/system.blade.php`
2. `app/Http/Controllers/SystemSettingsController.php`
3. `app/Services/SettingsService.php`
4. `database/seeders/SystemSettingsSeeder.php`
5. `routes/console.php`

## Files Created

1. `app/Console/Commands/AutoClockOut.php`
2. `database/migrations/2025_10_16_124715_add_global_auto_clockout_setting.php`
3. `AUTO_CLOCKOUT_GUIDE.md`

## Benefits

1. **Centralized Management**: Admin can set one time for all users
2. **Flexibility**: Users can still have individual times if needed
3. **Accurate Tracking**: Prevents users from being clocked in indefinitely
4. **Automated**: Runs every 5 minutes without manual intervention
5. **Audit Trail**: All actions are logged
6. **Low Overhead**: Efficient query and execution
7. **Non-Intrusive**: Works in background without affecting user experience

## Future Enhancements (Optional)

1. Notification system to alert users before auto clock-out
2. Different auto clock-out times for different days of the week
3. Holiday exceptions
4. Grace period configuration
5. Email notifications to admins about auto clock-outs
6. Dashboard widget showing upcoming auto clock-outs

## Support

For questions or issues, refer to:
- `AUTO_CLOCKOUT_GUIDE.md` for user documentation
- Application logs for troubleshooting
- Settings > System Logs for auto clock-out history
