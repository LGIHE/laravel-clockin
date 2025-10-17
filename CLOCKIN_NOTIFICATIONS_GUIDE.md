# Clockin Notifications System - User Guide

## Overview

The Clockin Notifications System allows administrators to send automated daily reminder emails to selected staff members, prompting them to clock in at their designated times.

## Features

### 1. Notification Type Toggles
Administrators can enable/disable different notification types:
- **Email Notifications** - Master switch for all email notifications
- **Leave Notifications** - Notifications for leave requests and approvals
- **Attendance Notifications** - Includes clockin reminders and attendance-related alerts
- **Notice Notifications** - Notifications for announcements and notices
- **Task Notifications** - Notifications for task assignments and updates

### 2. Daily Clockin Reminders
- Automated email reminders sent to selected staff members
- Customizable reminder time (e.g., 08:00 AM)
- Select specific users who should receive reminders
- Professional email template with clickable "Clock In Now" button

## How to Configure

### Step 1: Access Notification Settings
1. Log in as an **Administrator**
2. Navigate to **Settings** → **Notifications** tab
3. You'll see all notification type toggles

### Step 2: Enable Required Notifications
1. Toggle ON **Email Notifications** (required for any email)
2. Toggle ON **Attendance Notifications** (required for clockin reminders)

### Step 3: Set Clockin Reminder Time
1. Locate the **Attendance Notifications** section (highlighted in blue)
2. Find **"Daily Clockin Reminder Time"**
3. Select your preferred time (e.g., 08:00 for 8:00 AM)
4. This is when the reminder emails will be sent daily

### Step 4: Select Recipients
1. In the **"Clockin Reminder Recipients"** section
2. Check the boxes next to staff members who should receive reminders
3. You can select multiple users
4. Only active users are shown

### Step 5: Save Settings
1. Click **"Save Notification Settings"** button at the bottom
2. You'll see a success message confirming your changes

## Email Template

Recipients will receive a professional email containing:
- Personalized greeting with their name
- Current date
- Reminder to clock in at their designated time
- **"Clock In Now"** button linking to the attendance page
- Important reminders about clocking in/out
- Company branding

## How It Works

### Automated Scheduling
1. The system uses Laravel's task scheduler
2. Every day at the configured time, the system:
   - Checks if Email and Attendance notifications are enabled
   - **Checks if today is a public holiday** (skips all reminders if true)
   - Gets the list of selected recipients
   - **Excludes users who are on approved leave today**
   - Sends personalized emails to eligible recipients
   - Logs all activities for audit purposes

### Smart Exclusions
The system automatically skips sending reminders to:
- ✋ Users on **approved leave** (Granted status) for the current day
- ✋ **All users** if today is a **public holiday**

This ensures employees don't receive unnecessary reminders when they're not expected to work.

### Requirements
Both toggles must be ON for reminders to be sent:
- ✅ Email Notifications: ON
- ✅ Attendance Notifications: ON

If either is disabled, reminders will be skipped automatically.

## Testing the Feature

### Manual Test
Administrators can manually trigger the reminder emails:

```bash
php artisan attendance:send-clockin-reminders
```

This command will:
- Display which emails are being sent
- Show success/failure for each recipient
- Log all activities

### Check Scheduled Tasks
View all scheduled tasks including clockin reminders:

```bash
php artisan schedule:list
```

You should see:
```
0 8 * * *  php artisan attendance:send-clockin-reminders  Next Due: X hours from now
```

## Logs and Monitoring

All clockin reminder activities are logged in:
- **Location**: `storage/logs/laravel-{date}.log`
- **Log Channel**: Daily

Logged information includes:
- When reminders are sent
- Recipient details
- Success/failure status
- Error messages (if any)

### Sample Log Entry (Success)
```
[2025-10-16 08:00:00] local.INFO: Clockin reminder sent
{
  "user_id": "abc-123-def",
  "user_name": "John Doe",
  "user_email": "john@example.com",
  "clockin_time": "08:00"
}
```

### Sample Log Entry (Skipped - User on Leave)
```
[2025-10-16 08:00:00] local.INFO: Clockin reminder skipped - user on leave
{
  "user_id": "abc-123-def",
  "user_name": "John Doe",
  "date": "2025-10-16"
}
```

### Sample Log Entry (Skipped - Public Holiday)
```
[2025-10-16 08:00:00] local.INFO: Clockin reminders skipped - public holiday
{
  "holiday_name": "Independence Day",
  "date": "2025-10-16"
}
```

### Sample Log Entry (Disabled)
```
[2025-10-16 08:00:00] local.INFO: Clockin reminders skipped - attendance notifications disabled
```

## Troubleshooting

### Emails Not Being Sent

**Check 1: Notifications Enabled?**
- Go to Settings → Notifications
- Verify both Email and Attendance toggles are ON

**Check 2: Recipients Selected?**
- Scroll to "Clockin Reminder Recipients"
- Ensure at least one user is checked

**Check 3: Scheduler Running?**
- In production, verify cron job is configured
- In development, run: `php artisan schedule:work`

**Check 4: Email Configuration**
- Go to Settings → Email tab
- Verify SMTP settings are correct
- Test email by sending a test message

**Check 5: User Status**
- Only **active** users receive emails
- Inactive/suspended users are automatically excluded

### Wrong Time Being Used

**Solution**:
1. Go to Settings → Notifications
2. Update the "Daily Clockin Reminder Time"
3. Click "Save Notification Settings"
4. Restart the scheduler: `php artisan schedule:work`

**Note**: The scheduler reads the time when it starts, so you may need to restart it after changing the time.

### Test Email Not Received

Run the manual command to see detailed output:
```bash
php artisan attendance:send-clockin-reminders
```

Look for error messages indicating:
- Email server connection issues
- Invalid recipient email addresses
- Mail configuration problems

## Best Practices

### Timing Recommendations
- Set reminder time **before** normal work hours
- Common settings:
  - 08:00 AM (for 9:00 AM start time)
  - 07:30 AM (for 8:00 AM start time)
  - 06:00 AM (for early morning shifts)

### Recipient Selection
- **Don't select**: Managers who don't clock in
- **Do select**: Regular staff who need reminders
- **Consider**: Different reminder groups for different shifts (future enhancement)

### Regular Maintenance
- Weekly: Review recipient list for new/departed staff
- Monthly: Check email logs for delivery issues
- Quarterly: Survey staff to ensure reminders are helpful

## Production Deployment

### Cron Job Setup
For the reminders to work automatically in production, ensure you have this cron entry:

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

Or for cPanel with ea-php:
```bash
* * * * * cd /home/username/domain.com && /usr/local/bin/ea-php83 php artisan schedule:run >> /dev/null 2>&1
```

### Monitoring
Set up monitoring to ensure:
- Cron job is running every minute
- Scheduler is processing tasks
- Emails are being delivered successfully

## Security Considerations

- Only administrators can configure notification settings
- Recipient selection limited to active users only
- All email activities are logged for audit trails
- No sensitive information included in emails
- Email addresses validated before sending

## Future Enhancements

Potential improvements for future versions:
- Multiple reminder times per day
- Different reminder schedules for different departments
- SMS notifications option
- Custom email templates per department
- Reminder frequency settings (daily, weekdays only, etc.)
- Automatic exclusion for users on leave
- Reminder acknowledgment tracking

## Support

If you encounter issues:
1. Check this guide's troubleshooting section
2. Review application logs in `storage/logs/`
3. Verify email configuration in Settings → Email
4. Contact your system administrator

---

**Last Updated**: October 16, 2025  
**Feature Version**: 1.0  
**Compatible With**: Laravel 11, PHP 8.3+
