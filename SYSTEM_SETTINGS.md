# System Settings Module

## Overview
The System Settings module provides a comprehensive interface for administrators to configure and manage application-wide settings. All settings are stored in the database and can be modified independently without requiring a full system restart.

## Features

### 1. General Settings
- **Application Name**: Set the name displayed throughout the application
- **Address**: Organization's physical address
- **Contact Number**: Primary contact phone number
- **Contact Email**: Primary contact email address
- **Logo**: Upload and manage the application logo (JPEG, PNG, JPG, GIF, max 2MB)

### 2. Email Configuration
- **Mail Mailer**: Choose between SMTP, Sendmail, Mailgun, Amazon SES
- **Mail Host**: SMTP server hostname
- **Mail Port**: SMTP port (usually 587 for TLS or 465 for SSL)
- **Username**: SMTP username
- **Password**: SMTP password
- **Encryption**: TLS, SSL, or None
- **From Address**: Email address that appears in the "From" field
- **From Name**: Name that appears in the "From" field

### 3. System Settings
- **Auto Punch Out Time**: Automatically punch out users at a specific time if they haven't done so manually
- **Timezone**: Application timezone (affects all date/time calculations)
- **Date Format**: Display format for dates throughout the application
- **Time Format**: Display format for times throughout the application

### 4. Notification Settings
Toggle switches for:
- Email Notifications
- Leave Notifications
- Attendance Notifications
- Task Notifications

### 5. Logs & Statistics
- **System Logs**: View recent application logs
- **System Statistics**: View system information including:
  - PHP Version
  - Laravel Version
  - Server Software
  - Database Type
  - Cache Driver
  - Queue Driver
  - Disk Space
  - Memory Limit
  - Max Execution Time
- **Cache Management**: Clear application cache with one click

## Usage

### Accessing Settings
1. Log in as an **Admin** user
2. Navigate to **Settings** in the sidebar menu
3. Use the tabs to switch between different setting categories

### Saving Settings
Each settings section has its own "Save" button. This allows you to:
- Save settings independently without affecting other sections
- Make quick changes to specific configurations
- Reduce the risk of accidentally modifying unrelated settings

### Using Settings in Code

#### 1. Using the Helper Function
```php
// Get a specific setting
$appName = settings('app_name');

// Get with default value
$logo = settings('app_logo', 'default-logo.png');

// Get the settings service instance
$settings = settings();
$timezone = $settings->timezone();
```

#### 2. Using the Service Class
```php
use App\Services\SettingsService;

class YourController extends Controller
{
    protected $settings;

    public function __construct(SettingsService $settings)
    {
        $this->settings = $settings;
    }

    public function index()
    {
        $appName = $this->settings->appName();
        $emailEnabled = $this->settings->emailNotificationsEnabled();
        // ... more code
    }
}
```

#### 3. Using the Model Directly
```php
use App\Models\SystemSetting;

// Get a setting
$value = SystemSetting::get('app_name', 'Default Name');

// Set a setting
SystemSetting::set('app_name', 'New Name', 'string', 'general', 'Application Name');

// Get all settings in a group
$emailSettings = SystemSetting::getGroup('email');
```

### Available Service Methods
The `SettingsService` provides convenient methods:

```php
// General
settings()->appName()
settings()->appLogo()
settings()->appAddress()
settings()->appContact()
settings()->appEmail()

// System
settings()->autoPunchOutTime()
settings()->timezone()
settings()->dateFormat()
settings()->timeFormat()

// Notifications
settings()->emailNotificationsEnabled()
settings()->leaveNotificationsEnabled()
settings()->attendanceNotificationsEnabled()
settings()->taskNotificationsEnabled()

// Apply settings
settings()->applyEmailSettings()
settings()->applyTimezone()
settings()->clearCache()
```

## Database Structure

### Table: `system_settings`
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| key | STRING | Unique setting key |
| value | TEXT | Setting value |
| type | STRING | Data type (string, boolean, integer, json, file) |
| description | TEXT | Setting description |
| group | STRING | Setting group (general, email, notification, system) |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

## Security
- All settings routes are protected by the `auth` and `role:admin` middleware
- Only users with the Admin role can access and modify system settings
- Settings are cached for performance and automatically cleared when updated

## Caching
Settings are cached for 1 hour to improve performance. The cache is automatically cleared when:
- A setting is updated
- The "Clear Application Cache" button is clicked
- The `settings()->clearCache()` method is called

## Auto-Loading
Settings are automatically applied when the application boots:
- Email configuration is applied to Laravel's mail system
- Timezone is applied to PHP and Laravel
- Failed loads (during migrations, etc.) are silently ignored

## Seeding Default Settings
To populate default settings, run:
```bash
php artisan db:seed --class=SystemSettingsSeeder
```

## File Structure
```
app/
├── Http/Controllers/
│   └── SystemSettingsController.php
├── Models/
│   └── SystemSetting.php
├── Providers/
│   └── SettingsServiceProvider.php
├── Services/
│   └── SettingsService.php
└── helpers.php

resources/views/admin/settings/
├── index.blade.php
├── logs.blade.php
├── stats.blade.php
└── partials/
    ├── general.blade.php
    ├── email.blade.php
    ├── system.blade.php
    ├── notifications.blade.php
    └── logs-stats.blade.php

database/
├── migrations/
│   └── 2025_10_12_095209_create_system_settings_table.php
└── seeders/
    └── SystemSettingsSeeder.php

routes/
└── web.php (settings routes)
```

## Best Practices
1. Always use the helper function or service class to access settings
2. Provide sensible default values when getting settings
3. Clear cache after bulk updates
4. Use appropriate data types when storing settings
5. Document custom settings in this file

## Troubleshooting

### Settings not applying
- Clear the cache: Settings → Logs & Stats → Clear Application Cache
- Check database connection
- Verify the setting exists in the database

### Email not working
- Verify email configuration in Settings → Email Config
- Test with a simple email send
- Check server firewall settings
- Verify SMTP credentials

### Logo not displaying
- Check file permissions in `storage/app/public`
- Run `php artisan storage:link` to create symbolic link
- Verify image was uploaded successfully
- Check file size (max 2MB)

## Future Enhancements
- Add setting validation rules
- Implement setting versioning/history
- Add import/export functionality
- Create setting categories for better organization
- Add API endpoints for programmatic access
