# Quick Fix: Mailtrap Not Working

## ⚠️ THE MAIN ISSUE

**Your `php artisan serve` is running with OLD environment variables!**

The server loads `.env` on startup and keeps values in memory. Even after changing `.env`, the running server still uses old values.

## ✅ SOLUTION

### 1. Stop the Running Server
```bash
# Find the process
ps aux | grep "artisan serve"

# Kill it
ps aux | grep "artisan serve" | grep -v grep | awk '{print $2}' | xargs kill

# OR just press Ctrl+C in the terminal where it's running
```

### 2. Clear All Caches
```bash
php artisan optimize:clear
```

### 3. Restart the Server
```bash
php artisan serve --host=0.0.0.0
```

### 4. Test Email
```bash
php artisan email:test test@example.com
```

## 📧 Correct Mailtrap Configuration

Your `.env` should have:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=1ce4c248e317111a4205be195527b0b1
MAIL_PASSWORD=1ce4c248e317111a4205be195527b0b1
MAIL_ENCRYPTION=tls
```

**Key Points:**
- ✅ Use `sandbox.smtp.mailtrap.io` (NOT `live.smtp.mailtrap.io`)
- ✅ Port `2525` (NOT `587` or `465`)
- ✅ Encryption `tls` (NOT `ssl`)
- ✅ SAME API key for both username AND password
- ✅ NO `api:` prefix - just the key itself

## 🔍 Verify It's Working

```bash
# Check current config
php artisan tinker --execute="echo config('mail.mailers.smtp.host');"

# Should show: sandbox.smtp.mailtrap.io
# If it shows old values, the server wasn't restarted!
```

## 📝 Common Mistakes

❌ Using `live.smtp.mailtrap.io` instead of `sandbox.smtp.mailtrap.io`  
❌ Adding `api:` prefix to the API key  
❌ Using different values for username and password  
❌ Not restarting `php artisan serve` after changing `.env`  
❌ Using port 587 or 465 instead of 2525  
❌ Using ssl instead of tls encryption

## 🎯 After Fixing

1. Email test command should show:
   ```
   MAIL_HOST: sandbox.smtp.mailtrap.io
   MAIL_PORT: 2525
   MAIL_USERNAME: 1ce4c248e317111a4205be195527b0b1
   MAIL_ENCRYPTION: tls
   ✓ Email sent successfully!
   ```

2. Check your Mailtrap inbox - email should appear instantly

3. Click the email to view HTML rendering and test the setup link
