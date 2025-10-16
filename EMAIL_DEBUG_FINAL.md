# 🎯 FINAL EMAIL DEBUGGING GUIDE

## Current Status

✅ **Email sending works!** (proven by `php artisan email:test` and `php artisan user:test-create`)  
✅ **SMTP configuration is correct**  
✅ **UserService code is working**  
❌ **Emails not sent when creating users via Web UI**

## The Problem

When you create a user through the admin panel, the email is not being sent. But the test commands work perfectly.

## Step-by-Step Debugging

### Step 1: Clear the Log File

```bash
# Clear the log to start fresh
> storage/logs/laravel.log
```

### Step 2: Start Monitoring Logs

**Open a NEW terminal window** and run:

```bash
cd /Users/caleb/LGIHE/Dev/NEW/attendance/laravel-clockin
tail -f storage/logs/laravel.log
```

Leave this window open - you'll see logs appear in real-time.

### Step 3: Create a User via Web UI

1. Open your browser
2. Go to the ClockIn admin panel
3. Navigate to Users → Add New User
4. Fill in the form:
   - Name: Test User
   - Email: test@example.com (or any email)
   - Password: Test123!
   - Select a user level
   - Click "Save" or "Create User"

### Step 4: Watch the Logs

In the terminal where `tail -f` is running, you should see:

#### **Scenario A: Logs appear**
```
[timestamp] local.INFO: UserForm: Creating new user via UI
[timestamp] local.INFO: User created in database, preparing to send email
[timestamp] local.INFO: Attempting to send account setup email to new user
[timestamp] local.INFO: Account setup email sent successfully
```

✅ **If you see this**: Emails ARE being sent! Check your Mailtrap inbox.

#### **Scenario B: No logs at all**
```
(nothing appears)
```

❌ **If you see this**: The UserForm save() method is NOT being called.

**Possible causes:**
- Validation is failing (check for validation errors in the UI)
- JavaScript error preventing form submission
- Form is submitting to wrong route

#### **Scenario C: Partial logs**
```
[timestamp] local.INFO: UserForm: Creating new user via UI
[timestamp] local.INFO: User created in database, preparing to send email
[timestamp] local.ERROR: Failed to send account setup email
```

❌ **If you see this**: Email sending is failing. Check the error message in logs.

### Step 5: Check for Validation Errors

If NO logs appear, check the browser console:

1. Open browser Developer Tools (F12)
2. Go to "Console" tab
3. Try creating a user again
4. Look for any JavaScript errors

Also check the UI for validation error messages (they should appear in red near the form fields).

### Step 6: Verify Server is Running

```bash
ps aux | grep "artisan serve" | grep -v grep
```

Should show the server running. If not, start it:

```bash
php artisan serve --host=0.0.0.0
```

## Testing Checklist

Run these commands to verify everything:

```bash
# 1. Test email configuration
php artisan email:test test@example.com

# 2. Test user creation via command
php artisan user:test-create test-$(date +%s)@example.com

# 3. Check if server is running
ps aux | grep "artisan serve" | grep -v grep

# 4. Run diagnostic
./diagnose-email.sh

# 5. Monitor logs in real-time
tail -f storage/logs/laravel.log
```

## Expected Behavior

When creating a user (via UI OR command), you should see these logs:

```
1. UserForm: Creating new user via UI (only from web UI)
2. User created in database, preparing to send email
3. Attempting to send account setup email to new user
   - Includes: user_id, email, name, setup_url, mail_mailer, from_address
4. Account setup email sent successfully
   - Includes: user_id, email
```

## Common Issues & Solutions

### Issue #1: No logs when creating user via UI

**Cause**: Form validation failing or JavaScript error

**Solution**:
1. Check browser console for errors
2. Look for red validation messages in the UI
3. Ensure all required fields are filled
4. Try with a simple email like `test@example.com`

### Issue #2: Logs show email attempt but error

**Cause**: SMTP connection or authentication issue

**Solution**:
1. Check error message in logs
2. Verify SMTP credentials in `.env`
3. Restart server: `php artisan serve --host=0.0.0.0`

### Issue #3: Emails sent but not received

**Cause**: Using `live.smtp.mailtrap.io` (production) instead of sandbox

**Solution**:
For testing, use sandbox:
```env
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
```

For production (real emails):
```env
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
```

## Live Debugging Session

### Terminal 1: Monitor Logs
```bash
tail -f storage/logs/laravel.log
```

### Terminal 2: Server (if not already running)
```bash
php artisan serve --host=0.0.0.0
```

### Terminal 3: Run diagnostic when needed
```bash
./diagnose-email.sh
```

### Browser: Create User
1. Fill form
2. Submit
3. Watch Terminal 1 for logs

## Success Criteria

✅ Logs show all 4 steps (create attempt → user created → email attempt → email sent)  
✅ Email appears in Mailtrap inbox within seconds  
✅ Setup link in email works and redirects to password setup page  
✅ After setting password, user can login successfully  

## If Nothing Works

Try creating a user with this exact data:

- **Name**: Test User
- **Email**: test123@example.com
- **Password**: Test123!@
- **User Level**: User (or Admin)
- **Status**: Active

Then check:
1. Logs: `tail -f storage/logs/laravel.log`
2. Database: `SELECT * FROM users WHERE email = 'test123@example.com'`
3. Command test: `php artisan user:test-create another@example.com`

If the command works but UI doesn't, the issue is in the Livewire form, not the email system.

## Contact Points

Files to check if issues persist:

1. **UserForm**: `app/Livewire/Users/UserForm.php` (line 175+)
2. **UserService**: `app/Services/UserService.php` (line 22+)
3. **Email Config**: `config/mail.php`
4. **Environment**: `.env` (MAIL_* variables)
5. **Logs**: `storage/logs/laravel.log`

## Next Steps

After following this guide, you should know:

1. ✅ Whether UserForm is being called when you submit the form
2. ✅ Whether UserService createUser is being executed
3. ✅ Whether email sending is being attempted
4. ✅ Whether the email send succeeds or fails
5. ✅ What the specific error is (if any)

Then we can fix the specific issue!
