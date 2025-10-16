# 🚨 CRITICAL: WHY EMAILS AREN'T SENDING

## The Problem

You have **TWO** issues preventing emails from sending when creating users:

### Issue #1: Server Running with OLD Configuration ❌

Your `php artisan serve` is still running with **SMTP configuration** even though your `.env` file says `MAIL_MAILER=mailtrap`.

**Current Server Status:**
- Started: Earlier today
- Environment loaded: OLD SMTP settings  
- MAIL_MAILER in memory: `smtp`
- MAIL_MAILER in .env: `mailtrap` ✅

**The server caches environment variables when it starts!**

### Issue #2: You Need to Test Properly

The `php artisan email:test` command works because it creates a NEW process that loads the CURRENT `.env` file. But the web server (where you create users) is using CACHED old values.

## ✅ THE SOLUTION

### Step 1: Stop the Current Server

**In the terminal where `php artisan serve` is running (Terminal: php83):**

Press `Ctrl+C` to stop the server.

**Or kill it from any terminal:**
```bash
ps aux | grep "artisan serve" | grep -v grep | awk '{print $2}' | xargs kill
```

### Step 2: Verify .env is Correct

```bash
grep "^MAIL" .env
```

**Should show:**
```
MAIL_MAILER=mailtrap
MAILTRAP_API_KEY=1ce4c248e317111a4205be195527b0b1
MAIL_FROM_ADDRESS="clockin@lgfug.org"
MAIL_FROM_NAME="${APP_NAME}"
```

✅ This is **CORRECT**!

### Step 3: Clear ALL Caches

```bash
php artisan optimize:clear
```

### Step 4: Restart the Server

```bash
php artisan serve --host=0.0.0.0
```

**The server will now load the CORRECT `.env` values!**

### Step 5: Verify Configuration Loaded

In a **NEW terminal**, run:

```bash
php artisan tinker --execute="echo 'MAIL_MAILER: ' . config('mail.default');"
```

**Should show:**
```
MAIL_MAILER: mailtrap
```

If it still shows `smtp`, the server wasn't restarted properly!

### Step 6: Test User Creation

```bash
php artisan user:test-create test@example.com
```

You should see:
```
Creating test user with email: test@example.com
Mail configuration:
- MAIL_MAILER: mailtrap  ← Should say "mailtrap" not "smtp"!
- FROM_ADDRESS: clockin@lgfug.org

✅ User created successfully!
```

### Step 7: Check Logs

```bash
tail -f storage/logs/laravel.log
```

You should see:
```
[timestamp] local.INFO: User created in database, preparing to send email
[timestamp] local.INFO: Attempting to send account setup email to new user
[timestamp] local.INFO: Account setup email sent successfully
```

### Step 8: Create User via UI

1. Login to admin panel
2. Go to Users → Add New User
3. Fill in the form
4. Submit

**Watch the log in real-time:**
```bash
tail -f storage/logs/laravel.log | grep -i "email\|user created"
```

### Step 9: Check Mailtrap

1. Go to [https://mailtrap.io](https://mailtrap.io)
2. Open your inbox
3. The email should appear instantly!

## 🔍 How to Verify Server is Using Correct Config

### Before Restarting Server:
```bash
# Check what the RUNNING server sees
curl http://localhost:8000 > /dev/null 2>&1
php artisan tinker --execute="echo config('mail.default');"
# Output: smtp ← OLD value cached in server memory
```

### After Restarting Server:
```bash
# Server loads fresh .env
php artisan serve --host=0.0.0.0

# In new terminal:
php artisan tinker --execute="echo config('mail.default');"
# Output: mailtrap ← NEW value from .env
```

## 📊 Quick Diagnostic Checklist

Run these commands to verify everything:

```bash
# 1. Check .env file
echo "=== .ENV FILE ==="
grep "^MAIL" .env

# 2. Check if server is running
echo "=== SERVER STATUS ==="
ps aux | grep "artisan serve" | grep -v grep

# 3. Check loaded config (this creates NEW process, so shows correct value)
echo "=== CONFIG IN NEW PROCESS ==="
php artisan tinker --execute="echo config('mail.default');"

# 4. If server IS running, it has CACHED values (may be different!)
# You MUST restart the server to load new .env values!
```

## 🎯 Why This Happens

```
┌─────────────────────────────────────┐
│  You Change .env File               │
│  MAIL_MAILER=smtp → mailtrap        │
└──────────┬──────────────────────────┘
           │
           ↓
┌─────────────────────────────────────┐
│  php artisan serve (ALREADY RUNNING)│
│  Still has OLD values in memory!    │
│  MAIL_MAILER = smtp                 │
└─────────────────────────────────────┘
           
           VS
           
┌─────────────────────────────────────┐
│  php artisan email:test             │
│  NEW process - loads CURRENT .env   │
│  MAIL_MAILER = mailtrap ✓           │
└─────────────────────────────────────┘
```

**Solution:** Restart `php artisan serve` after ANY `.env` changes!

## 🚀 Quick Fix Script

I've created `./restart-server.sh` for you:

```bash
./restart-server.sh
```

This script:
1. Kills old server
2. Clears all caches
3. Shows current mail config
4. Starts fresh server with NEW .env values

## ⚠️ Common Mistakes

❌ Changing `.env` but not restarting server  
❌ Running `php artisan config:clear` without restarting server  
❌ Assuming server auto-reloads `.env` (it doesn't!)  
❌ Testing with `email:test` (new process) but creating users via web (old server)

✅ **Always restart server after changing .env!**

## 📝 Summary

1. Your `.env` is **CORRECT** ✅
2. Your `php artisan email:test` works because it creates a **NEW process** ✅  
3. Your web server is **STILL RUNNING WITH OLD CONFIG** ❌
4. **SOLUTION: Restart the server!** 🔄

```bash
# In terminal with php artisan serve:
Ctrl+C

# Clear caches:
php artisan optimize:clear

# Start fresh:
php artisan serve --host=0.0.0.0
```

**THAT'S IT!** After restarting, user creation will send emails! 📧✨
