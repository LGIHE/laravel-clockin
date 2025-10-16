# ⚠️ AUTO CLOCK-OUT SCHEDULER - IMPORTANT!

## The Scheduler Must Be Running!

The auto clock-out feature **requires** the Laravel scheduler to be actively running. Simply having the scheduled task defined is **not enough**.

---

## Quick Start (Development)

### Option 1: Use the Startup Script
```bash
./start-scheduler.sh
```

### Option 2: Manual Start
```bash
php artisan schedule:work
```

Keep the terminal open or run in background:
```bash
nohup php artisan schedule:work > storage/logs/scheduler.log 2>&1 &
```

---

## Quick Start (Production)

Add this to your server's crontab:
```bash
crontab -e
```

Add this line:
```
* * * * * cd /path/to/laravel-clockin && php artisan schedule:run >> /dev/null 2>&1
```

---

## Verify It's Working

### Check if scheduler is running:
```bash
# Development
ps aux | grep "schedule:work"

# Production
crontab -l
```

### View scheduled tasks:
```bash
php artisan schedule:list
```

Should show:
```
*/5 * * * *  php artisan attendance:auto-clockout ........ Next Due: X minutes from now
```

### Test manually:
```bash
php artisan attendance:auto-clockout
```

---

## Stop the Scheduler (Development)

```bash
pkill -f "schedule:work"
```

---

## Full Documentation

- **Setup Guide:** `SCHEDULER_SETUP_GUIDE.md`
- **Auto Clock-Out Guide:** `AUTO_CLOCKOUT_GUIDE.md`
- **Implementation Details:** `GLOBAL_AUTO_CLOCKOUT_IMPLEMENTATION.md`

---

## Common Issues

**"Auto clock-out not working"**
→ Make sure the scheduler is running (see above)

**"No users being clocked out"**
→ Check if:
1. Users are currently clocked in
2. Global auto clock-out time is set in Settings
3. Current time has passed the auto clock-out time

**"Permission denied"**
→ Run: `chmod +x start-scheduler.sh`

---

**Need help?** Check `SCHEDULER_SETUP_GUIDE.md` for detailed troubleshooting.
