# Auto Clock-Out Scheduler Setup Guide

## ⚠️ IMPORTANT: The Scheduler Must Be Running!

The auto clock-out feature **will not work** unless the Laravel scheduler is actively running. Simply defining the scheduled task is not enough - Laravel's scheduler is passive and needs to be triggered.

---

## Quick Start

### For Development/Local Testing

Run this command in your terminal:

```bash
cd /path/to/laravel-clockin
php artisan schedule:work
```

Keep this terminal window open - the scheduler will run continuously and execute the auto clock-out command every 5 minutes.

**Alternative (Background):**
```bash
cd /path/to/laravel-clockin
nohup php artisan schedule:work > storage/logs/scheduler.log 2>&1 &
```

---

## Production Setup

### Step 1: Add Cron Job

On your production server, add this **single** cron entry:

```bash
* * * * * cd /path/to/laravel-clockin && php artisan schedule:run >> /dev/null 2>&1
```

**To add the cron job:**

```bash
# Open crontab editor
crontab -e

# Add this line (replace /path/to/laravel-clockin with actual path)
* * * * * cd /path/to/laravel-clockin && php artisan schedule:run >> /dev/null 2>&1

# Save and exit
```

### Step 2: Verify Cron Job

```bash
# List current cron jobs
crontab -l

# You should see your entry listed
```

---

## Verification

### Check if Scheduler is Running

**Development:**
```bash
ps aux | grep "schedule:work"
```

You should see a PHP process running `artisan schedule:work`.

**Production:**
```bash
# Check cron jobs
crontab -l

# Check if schedule:run is being executed (check logs)
grep "schedule:run" /var/log/syslog
```

### Test the Auto Clock-Out Command

Run manually to test:

```bash
php artisan attendance:auto-clockout
```

Expected output:
```
Starting auto clock-out process...
Current time: 15:30
Global auto clock-out time: 18:00
Found X users currently clocked in.
...
Auto clock-out process completed. Clocked out X user(s).
```

### View Scheduled Tasks

```bash
php artisan schedule:list
```

You should see:
```
*/5 * * * *  php artisan attendance:auto-clockout ........ Next Due: X minutes from now
```

---

## Common Issues & Solutions

### Issue 1: "Auto clock-out not working"

**Symptoms:**
- Task appears in `schedule:list`
- No users being clocked out automatically

**Solution:**
- **Development:** Ensure `php artisan schedule:work` is running
- **Production:** Verify cron job is added with `crontab -l`

**Fix:**
```bash
# Development
php artisan schedule:work

# Production
crontab -e
# Add: * * * * * cd /path/to/laravel-clockin && php artisan schedule:run >> /dev/null 2>&1
```

### Issue 2: Scheduler stopped working

**Solution:**
```bash
# Find and kill existing scheduler
ps aux | grep "schedule:work"
kill -9 [PID]

# Restart scheduler
php artisan schedule:work
```

### Issue 3: No users being clocked out

**Check:**
1. Are there users currently clocked in?
   ```bash
   php artisan tinker --execute="echo App\Models\Attendance::whereNull('out_time')->count();"
   ```

2. Is the global setting configured?
   ```bash
   php artisan tinker --execute="echo App\Models\SystemSetting::where('key', 'global_auto_clockout_time')->value('value');"
   ```

3. Has the auto clock-out time passed?
   - If global time is 18:00, users will only be auto-clocked out after 18:00

### Issue 4: Permission denied errors

**Solution:**
```bash
# Ensure storage and logs are writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Ensure proper ownership
chown -R www-data:www-data storage bootstrap/cache
```

---

## Process Management (Production)

### Using Supervisor (Recommended)

Create supervisor configuration file:

```bash
sudo nano /etc/supervisor/conf.d/laravel-scheduler.conf
```

Add:

```ini
[program:laravel-scheduler]
process_name=%(program_name)s
command=/usr/bin/php /path/to/laravel-clockin/artisan schedule:work
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/laravel-clockin/storage/logs/scheduler.log
stopwaitsecs=3600
```

Apply changes:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-scheduler
```

Check status:

```bash
sudo supervisorctl status laravel-scheduler
```

---

## Monitoring

### Check Logs

**Application Logs:**
```bash
tail -f storage/logs/laravel.log | grep "Auto clock-out"
```

**Scheduler Logs (if using supervisor):**
```bash
tail -f storage/logs/scheduler.log
```

### Monitor Execution

Create a monitoring script:

```bash
#!/bin/bash
# save as monitor-scheduler.sh

LOG_FILE="storage/logs/laravel.log"
LAST_RUN=$(grep "Auto clock-out" $LOG_FILE | tail -1)

if [ -z "$LAST_RUN" ]; then
    echo "❌ No auto clock-out executions found"
else
    echo "✅ Last execution: $LAST_RUN"
fi

# Check if scheduler is running
if pgrep -f "schedule:work" > /dev/null; then
    echo "✅ Scheduler is running"
else
    echo "❌ Scheduler is NOT running"
fi
```

Run it:
```bash
chmod +x monitor-scheduler.sh
./monitor-scheduler.sh
```

---

## Best Practices

1. **Always use cron in production** - More reliable than `schedule:work`
2. **Use supervisor for schedule:work** - If you prefer schedule:work in production
3. **Monitor logs regularly** - Check for errors or failed executions
4. **Test after deployment** - Always verify scheduler works after deployments
5. **Document your setup** - Keep track of which method you're using

---

## Quick Reference

| Environment | Command | Notes |
|------------|---------|-------|
| **Development** | `php artisan schedule:work` | Keep terminal open |
| **Development (bg)** | `nohup php artisan schedule:work &` | Runs in background |
| **Production** | Add cron job | Recommended approach |
| **Production (alt)** | Use supervisor | For schedule:work |
| **Test** | `php artisan attendance:auto-clockout` | Run manually |
| **List tasks** | `php artisan schedule:list` | View all scheduled tasks |
| **Test scheduler** | `php artisan schedule:test` | Test task execution |

---

## Support

If auto clock-out still isn't working after following this guide:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify system settings are configured
3. Ensure users have clock-in records
4. Check server time matches expected timezone
5. Review permissions on storage and bootstrap/cache directories

For more information, see `AUTO_CLOCKOUT_GUIDE.md`.
