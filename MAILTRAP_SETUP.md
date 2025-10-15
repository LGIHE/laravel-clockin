# Mailtrap Setup Guide

## What is Mailtrap?

Mailtrap is an email testing service that captures all outgoing emails in a safe sandbox environment. This prevents accidentally sending test emails to real users during development.

## Setup Instructions

### Step 1: Create Mailtrap Account
1. Go to [https://mailtrap.io](https://mailtrap.io)
2. Sign up for a free account
3. Verify your email address

### Step 2: Get SMTP Credentials
1. After logging in, you'll be in your **Inbox**
2. Click on **"Show Credentials"** or go to **SMTP Settings**
3. Select **Laravel** from the integrations dropdown (or use raw SMTP)
4. You'll see credentials like:
   ```
   Host: sandbox.smtp.mailtrap.io
   Port: 2525
   Username: [your_api_key]  # This is your API token
   Password: [your_api_key]  # Same as username for API auth
   Auth: PLAIN
   TLS: Optional (but recommended)
   ```

**Note for API Token Authentication:**
- Mailtrap's newer API uses a single API key for both username and password
- Format: Just use the API key directly (e.g., `1ce4c248e317111a4205be195527b0b1`)
- Do NOT add `api:` prefix in Laravel - just use the key itself

### Step 3: Update Your .env File

Replace the placeholder values in your `.env` file with your actual Mailtrap credentials:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_api_key_here
MAIL_PASSWORD=your_api_key_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@clockin.test"
MAIL_FROM_NAME="${APP_NAME}"
```

**Important Notes:**
- Both `MAIL_USERNAME` and `MAIL_PASSWORD` should be the SAME API key
- Use `sandbox.smtp.mailtrap.io` for testing (NOT `live.smtp.mailtrap.io`)
- Port should be `2525` for sandbox
- Encryption should be `tls` (NOT `ssl`)
- Don't include quotes around the API key unless it contains spaces

### Step 4: Clear Configuration Cache

After updating `.env`, clear Laravel's configuration cache:

```bash
php artisan optimize:clear
```

**CRITICAL:** If you have `php artisan serve` running, you MUST restart it!
The server loads environment variables on startup and keeps them in memory.

```bash
# Stop the running server (Ctrl+C in the terminal where it's running)
# Or kill it:
ps aux | grep "artisan serve" | grep -v grep | awk '{print $2}' | xargs kill

# Then restart it:
php artisan serve --host=0.0.0.0
```

### Step 5: Test Email Sending

Send a test email to verify the configuration:

```bash
php artisan email:test test@example.com
```

You should see:
```
✓ Email sent successfully!
```

### Step 6: Check Mailtrap Inbox

1. Go back to your Mailtrap dashboard
2. Open your **Inbox**
3. You should see the test email appear instantly
4. Click on it to view the full email with HTML rendering

## Using Mailtrap

### Viewing Emails

Every email sent from your Laravel app will appear in your Mailtrap inbox:
- **HTML & Text tabs** - View both versions of the email
- **Raw** - See the complete email source
- **Check Spam Score** - Ensure your emails won't be flagged as spam
- **Validate HTML/CSS** - Check email rendering across clients

### Creating Multiple Inboxes

You can create multiple inboxes for different purposes:
- **Development Inbox** - For local testing
- **Staging Inbox** - For staging environment
- **Testing Inbox** - For automated tests

Each inbox has its own SMTP credentials.

### Forwarding Emails (Optional)

If you want to receive test emails in your real inbox:
1. Go to inbox settings
2. Enable **Email Forwarding**
3. Add your email address
4. All emails will be forwarded to you

## Testing Account Setup Flow

### Test the Complete Flow:

1. **Create a test user** through the admin panel:
   ```
   Name: John Doe
   Email: any@example.com (can be fake - Mailtrap catches it)
   ```

2. **Check Mailtrap inbox** - Email should appear instantly

3. **Copy the setup link** from the email in Mailtrap

4. **Paste link in browser** to test the account setup page

5. **Create password** and verify auto-login works

6. **Check welcome dialog** appears

### Monitor Email Sending:

Terminal 1 - Run monitor script:
```bash
./monitor-emails.sh
```

Terminal 2 - Create users or send test emails:
```bash
php artisan email:test test@example.com
```

## Mailtrap vs Production

### Development (Mailtrap)
- ✅ Safe testing - no real emails sent
- ✅ Instant delivery
- ✅ HTML preview and validation
- ✅ Spam score checking
- ✅ Free tier available (500 emails/month)

### Production (Real SMTP)
- Use your actual email service (Gmail, SendGrid, AWS SES, etc.)
- Update `.env` with production credentials
- Test thoroughly in staging first

## Troubleshooting

### "Connection refused" error
- Check that `MAIL_HOST` is `sandbox.smtp.mailtrap.io`
- Verify port is `2525`
- Ensure your firewall allows outbound connections on port 2525

### "Authentication failed" error
- Double-check your username and password
- Make sure there are no extra spaces in `.env`
- Try regenerating credentials in Mailtrap

### Emails not appearing in Mailtrap
- Verify you're logged into the correct Mailtrap account
- Check you're viewing the correct inbox
- Look at the email count in the inbox sidebar
- Check Laravel logs: `cat storage/logs/laravel.log | grep -i email`

### Configuration not updating
```bash
# 1. Clear all caches
php artisan optimize:clear

# 2. CRITICAL: Restart php artisan serve if it's running!
# The server caches environment variables in memory on startup

# Find and kill the server:
ps aux | grep "artisan serve" | grep -v grep | awk '{print $2}' | xargs kill

# Or just Ctrl+C in the terminal where it's running, then restart:
php artisan serve --host=0.0.0.0

# 3. Test again
php artisan email:test test@example.com
```

## Free Tier Limits

Mailtrap free plan includes:
- **500 emails/month**
- **1 inbox**
- **Email retention: 1 month**
- **Unlimited team members**

This is perfect for development and testing!

## Alternative Email Testing Services

If you need alternatives:
- **MailHog** - Self-hosted, open source
- **Mailpit** - Modern alternative to MailHog
- **Ethereal Email** - Nodemailer's testing service
- **Gmail** - Can use for testing (not recommended for production)

## Security Note

⚠️ **Never commit `.env` file to version control!**

Your `.env` file is already in `.gitignore` by default. If you need to share configuration:
1. Use `.env.example` with placeholder values
2. Document the setup process
3. Share credentials securely (not in git)

## Production Checklist

Before switching to production email:

- [ ] Test all email types in Mailtrap
- [ ] Verify HTML renders correctly in email clients
- [ ] Check spam scores (use Mailtrap's spam analysis)
- [ ] Validate all links work correctly
- [ ] Test email on mobile devices (Mailtrap preview)
- [ ] Set up production SMTP service
- [ ] Update `.env` with production credentials
- [ ] Clear caches on production server
- [ ] Send test email to real address
- [ ] Monitor delivery rates and bounces

## Useful Mailtrap Features

### 1. Spam Score Analysis
Check if your emails might be flagged as spam:
- Click on an email in Mailtrap
- Go to "Spam Analysis" tab
- See detailed spam score breakdown
- Get suggestions for improvements

### 2. HTML/CSS Validation
Ensure emails render correctly:
- View "HTML Check" tab
- See warnings about unsupported CSS
- Preview across different email clients

### 3. Email Size
Monitor email size to ensure deliverability:
- Large emails (>100KB) may have delivery issues
- Mailtrap shows exact size
- Optimize images if needed

### 4. API Access
Automate email testing:
```bash
# Get all messages
curl -X GET https://mailtrap.io/api/v1/inboxes/{inbox_id}/messages \
  -H "Api-Token: {your_api_token}"
```

## Current Configuration

Your `.env` is now configured with:
```
Host: sandbox.smtp.mailtrap.io
Port: 2525
Encryption: TLS
```

**Next steps:**
1. Get your Mailtrap credentials from [mailtrap.io](https://mailtrap.io)
2. Update `MAIL_USERNAME` and `MAIL_PASSWORD` in `.env`
3. Run `php artisan config:clear`
4. Test with `php artisan email:test test@example.com`
5. Check your Mailtrap inbox!
