# Passwordless Account Setup Flow

## Overview

The ClockIn system now uses a secure, passwordless onboarding flow for new users. Instead of receiving credentials via email, users receive a secure one-time setup link that allows them to create their password directly.

## User Flow

### 1. Admin Creates New User
- Admin fills out user creation form (name, email, user level, etc.)
- System generates a unique 64-character setup token
- Token is valid for 24 hours
- No password is created initially (random password is set temporarily)

### 2. Email Sent to User
- User receives a professional welcome email
- Email contains a secure setup link: `/account-setup/{token}`
- Link expires after 24 hours
- Link can only be used once

### 3. User Clicks Setup Link
The system validates the token and displays one of three states:

#### a) Valid Token (Token exists and not expired)
- Shows "Complete Your Account Setup" page
- User sees their email (disabled field)
- User creates a new password with validation:
  - Minimum 8 characters
  - Must contain uppercase letters
  - Must contain lowercase letters
  - Must contain numbers
- User confirms password
- Click "Complete Setup & Login"

#### b) Invalid/Used Token
- Shows error message: "Invalid or Used Link"
- Message: "This setup link is invalid or has already been used"
- Provides "Go to Login" button

#### c) Expired Token
- Shows error message: "Invalid or Used Link"
- Message: "This setup link has expired. Please contact your administrator"
- Provides "Go to Login" button

### 4. Password Creation
When user submits the form:
- System validates password meets requirements
- Updates user's password (hashed)
- Clears setup token and expiry
- Sets `password_change_required` to false
- **Automatically logs the user in**
- Shows welcome dialog

### 5. Welcome Dialog
After successful setup:
- Displays celebration message: "🎉 Welcome to ClockIn!"
- Shows personalized greeting with user's name
- Confirms account setup complete
- User is already logged in
- Click "Go to Dashboard" to enter the system

## Database Schema

### Users Table (New Fields)
```sql
setup_token VARCHAR(255) NULLABLE
setup_token_expires_at TIMESTAMP NULLABLE
```

## Technical Implementation

### Token Generation
```php
$setupToken = Str::random(64); // 64-character random string
$setupTokenExpiresAt = now()->addHours(24); // 24-hour expiry
```

### Email Details
- **Subject**: "Welcome to ClockIn - Complete Your Account Setup"
- **From**: tech@lgfug.org
- **Template**: Professional HTML with gradient header
- **Content**: Personalized greeting, account email, setup button
- **Security Notice**: Link validity and one-time use warning

### Routes
```php
// Guest route (no authentication required)
Route::get('/account-setup/{token}', AccountSetup::class)->name('account.setup');
```

### Components
- **Livewire Component**: `App\Livewire\AccountSetup`
- **Email Mailable**: `App\Mail\NewUserAccountMail`
- **View**: `resources/views/livewire/account-setup.blade.php`
- **Email Template**: `resources/views/emails/new-user-account.blade.php`

## Security Features

1. **Unique Tokens**: Each setup link uses a 64-character random token
2. **Time-Limited**: Tokens expire after 24 hours
3. **Single Use**: Token is cleared after successful use
4. **Password Validation**: Strong password requirements enforced
5. **No Credentials in Email**: Email never contains passwords
6. **HTTPS Required**: Setup links should use HTTPS in production

## Password Requirements

- Minimum 8 characters
- At least one uppercase letter (A-Z)
- At least one lowercase letter (a-z)
- At least one number (0-9)
- Password and confirmation must match

## Testing

### Send Test Email
```bash
php artisan email:test your@email.com
```

This sends a test account setup email with a sample token.

### Monitor Email Sending
```bash
# In separate terminal
./monitor-emails.sh

# Or manually
tail -f storage/logs/laravel.log | grep -i "email\|mail\|setup"
```

### Create Test User via UI
1. Login as admin
2. Navigate to Users → Add New User
3. Fill in user details with a valid email
4. Click "Create User"
5. Check logs for email sending confirmation
6. Check recipient's inbox/spam folder
7. Click setup link in email
8. Create password
9. Verify auto-login and welcome dialog

## Logs

### Email Sending Logs
The system logs detailed information about email sending:

```log
[timestamp] local.INFO: Attempting to send account setup email to new user
  {"user_id":"...","email":"...","name":"...","setup_url":"..."}

[timestamp] local.INFO: Account setup email sent successfully
  {"user_id":"...","email":"..."}
```

### Error Logs
If email sending fails:

```log
[timestamp] local.ERROR: Failed to send account setup email
  {"user_id":"...","email":"...","error":"...","trace":"..."}
```

## User Experience Benefits

### Compared to Old Flow (Credentials in Email)

**Old Flow Issues:**
- Password sent in plain text via email
- User must remember/copy temporary password
- Extra login step required
- Must change password after login
- Multiple screens to navigate

**New Flow Benefits:**
✅ No credentials in email (more secure)
✅ One-click setup process
✅ User creates their own password immediately
✅ Automatic login after setup
✅ Welcome dialog provides friendly onboarding
✅ Single, seamless experience
✅ Token-based security with expiry

## Troubleshooting

### Email Not Received
1. Check spam/junk folder
2. Verify SMTP configuration in `.env`
3. Check logs: `cat storage/logs/laravel.log | grep -i email`
4. Run test command: `php artisan email:test your@email.com`
5. See `EMAIL_TROUBLESHOOTING.md` for detailed steps

### Token Invalid
- Token may have expired (24 hours)
- Token may have been used already
- Admin should create a new user or manually reset token

### Password Validation Fails
- Ensure password meets all requirements:
  - 8+ characters
  - Mix of upper/lowercase
  - Contains numbers
- Check that confirmation matches exactly

## Migration Guide

If upgrading from old password-in-email system:

1. Run migration: `php artisan migrate`
2. New users will use new flow automatically
3. Existing users are unaffected
4. Old `FirstLoginPasswordChange` component still exists for backwards compatibility

## Files Modified

### Created
- `database/migrations/2025_10_15_154512_add_setup_token_to_users_table.php`
- `app/Livewire/AccountSetup.php`
- `resources/views/livewire/account-setup.blade.php`

### Modified
- `app/Services/UserService.php` - Token generation
- `app/Mail/NewUserAccountMail.php` - Setup link instead of password
- `resources/views/emails/new-user-account.blade.php` - New email design
- `app/Models/User.php` - Added fillable fields
- `routes/web.php` - Added account setup route
- `app/Console/Commands/TestEmail.php` - Updated for new format

## Production Checklist

Before deploying to production:

- [ ] Run migration: `php artisan migrate`
- [ ] Verify SMTP settings in production `.env`
- [ ] Test email sending: `php artisan email:test your@email.com`
- [ ] Ensure APP_URL is set correctly for proper link generation
- [ ] Verify HTTPS is enabled for secure token transmission
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Test complete flow with real email addresses
- [ ] Monitor logs during first few user creations
- [ ] Check spam folder delivery rates

## Future Enhancements

Potential improvements:
- Email delivery status tracking
- Resend setup link functionality
- Custom token expiry time per user
- SMS-based setup option
- Admin dashboard to view pending setups
- Token regeneration API endpoint
