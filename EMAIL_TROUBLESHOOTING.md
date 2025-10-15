# Email Troubleshooting Guide

## Quick Test

Run this command to test if emails are working:

```bash
php artisan email:test your@email.com
```

This will:
- Display your current SMTP configuration
- Send a test welcome email
- Show success or detailed error message

## Monitor Email Sending in Real-Time

When creating users, you can monitor email sending in a separate terminal:

```bash
./monitor-emails.sh
```

Or manually:

```bash
tail -f storage/logs/laravel.log | grep -i email
```

## Check Email Logs

View recent email logs:

```bash
tail -n 100 storage/logs/laravel.log | grep -i email
```

## Email Configuration

Your current configuration (from .env):

```
MAIL_MAILER=smtp
MAIL_HOST="mail.lgfug.org"
MAIL_PORT=465
MAIL_USERNAME="tech@lgfug.org"
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="tech@lgfug.org"
```

## Common Issues & Solutions

### 1. Email Not Received

**Check spam/junk folder first!** Many email providers may mark automated emails as spam initially.

**Solutions:**
- Add tech@lgfug.org to your contacts
- Mark the email as "Not Spam" if found in junk folder
- Check email filters/rules in your email client

### 2. SMTP Connection Errors

If you see connection errors, verify:
- SMTP host is reachable: `ping mail.lgfug.org`
- Port 465 is not blocked by firewall
- Credentials are correct

### 3. SSL/TLS Errors

Your configuration uses SSL on port 465, which is correct. If you see SSL errors:
- Make sure MAIL_ENCRYPTION=ssl (not tls or ssh)
- Port 465 requires SSL
- Port 587 would require TLS

### 4. Authentication Failed

If authentication fails:
- Verify MAIL_USERNAME and MAIL_PASSWORD are correct
- Check if the email account requires app-specific passwords
- Ensure the email account is not locked or suspended

## Testing the Complete Flow

1. **Send a test email:**
   ```bash
   php artisan email:test nkunzecaleb@gmail.com
   ```

2. **Create a test user through the UI:**
   - Go to Users → Add New User
   - Fill in the form with a valid email
   - Check the logs: `tail -f storage/logs/laravel.log`

3. **Check what was logged:**
   ```bash
   cat storage/logs/laravel.log | grep -A 5 "Attempting to send welcome email"
   ```

## Log Format

When a user is created, you'll see logs like:

```
[timestamp] local.INFO: Attempting to send welcome email to new user {"user_id":"xxx","email":"user@example.com","name":"User Name"}
[timestamp] local.INFO: Welcome email sent successfully {"user_id":"xxx","email":"user@example.com"}
```

Or if there's an error:

```
[timestamp] local.ERROR: Failed to send new user email {"user_id":"xxx","email":"user@example.com","error":"Error message","trace":"..."}
```

## Clearing Cache

After making .env changes, always run:

```bash
php artisan config:clear
php artisan cache:clear
```

## Queue Configuration

Currently using `QUEUE_CONNECTION=database`. Emails are sent synchronously (immediately).

If you want faster user creation (send emails in background):
1. Change to `QUEUE_CONNECTION=database`
2. Run queue worker: `php artisan queue:work`

## Need Help?

1. Check the logs first: `storage/logs/laravel.log`
2. Run the test command: `php artisan email:test your@email.com`
3. Verify SMTP settings with your email provider
4. Check if port 465 is open and not blocked

## Email Provider Specific Notes

### Gmail
- May require "App Password" instead of regular password
- Enable "Less secure app access" or use OAuth2

### Office 365/Outlook
- May require app-specific password
- Check tenant settings for SMTP authentication

### Custom SMTP (like mail.lgfug.org)
- Verify the server supports SMTP on port 465
- Check if SPF/DKIM records are configured
- Ensure the domain is not blacklisted
