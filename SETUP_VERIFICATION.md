# Setup Verification Guide

This document helps verify that all components of the Laravel ClockIn application are properly configured.

## Verification Checklist

### 1. PHP and Composer
```bash
php --version
# Expected: PHP 8.2 or higher

composer --version
# Expected: Composer 2.x
```

### 2. Node.js and NPM
```bash
node --version
# Expected: Node.js 18.x or higher

npm --version
# Expected: NPM 9.x or higher
```

### 3. Laravel Installation
```bash
php artisan --version
# Expected: Laravel Framework 11.x
```

### 4. Database Connection
```bash
php artisan db:show
# Should display database connection information
```

### 5. Installed Packages

#### PHP Packages (Composer)
```bash
composer show | grep laravel
```

Expected packages:
- `laravel/framework` (^11.0)
- `laravel/sanctum` (^4.0)
- `livewire/livewire` (^3.0)
- `laravel/pint` (^1.0)

#### JavaScript Packages (NPM)
```bash
npm list --depth=0
```

Expected packages:
- `tailwindcss`
- `alpinejs`
- `@tailwindcss/forms`
- `autoprefixer`
- `postcss`

### 6. Configuration Files

Check that these files exist and are properly configured:

- ✓ `.env` - Environment variables configured
- ✓ `config/sanctum.php` - Sanctum configuration
- ✓ `config/cors.php` - CORS configuration
- ✓ `tailwind.config.js` - Tailwind CSS configuration
- ✓ `postcss.config.js` - PostCSS configuration
- ✓ `vite.config.js` - Vite configuration

### 7. Frontend Assets

Build assets and verify:
```bash
npm run build
```

Check that these files are created:
- `public/build/manifest.json`
- `public/build/assets/app-*.css`
- `public/build/assets/app-*.js`

### 8. Testing Environment

Run tests to verify setup:
```bash
php artisan test
```

Expected output: All tests passing

### 9. Code Formatting

Verify Laravel Pint:
```bash
./vendor/bin/pint --test
```

Expected output: No formatting issues

### 10. Development Server

Start the development server:
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

Expected: Welcome page displaying with:
- Tailwind CSS styling applied
- Alpine.js interactive button working
- All checkmarks showing green

### 11. Livewire

Verify Livewire is installed:
```bash
php artisan livewire:publish --config
```

### 12. Sanctum Migrations

Check Sanctum migrations:
```bash
php artisan migrate:status
```

Expected: `personal_access_tokens` table migration listed

## Common Issues and Solutions

### Issue: "Class 'Livewire\Livewire' not found"
**Solution:** Run `composer dump-autoload`

### Issue: Tailwind styles not applying
**Solution:** 
1. Run `npm run build`
2. Clear browser cache
3. Check `tailwind.config.js` content paths

### Issue: Alpine.js not working
**Solution:**
1. Check browser console for errors
2. Verify `resources/js/app.js` imports Alpine
3. Run `npm run build`

### Issue: Database connection failed
**Solution:**
1. Check `.env` database credentials
2. Ensure MySQL is running
3. Create the `clockin` database if it doesn't exist

### Issue: "Vite manifest not found"
**Solution:** Run `npm run build` before starting the server

## Environment Variables

Ensure these are set in `.env`:

```env
APP_NAME="ClockIn Laravel"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clockin
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SANCTUM_TOKEN_EXPIRATION=1440
```

## Next Steps

After verifying the setup:

1. Review the main `README.md` for project overview
2. Check `tasks.md` for implementation tasks
3. Review `design.md` for architecture details
4. Start implementing features according to the task list

## Support

If you encounter issues not covered here:
1. Check Laravel documentation: https://laravel.com/docs
2. Check Livewire documentation: https://livewire.laravel.com
3. Check Tailwind CSS documentation: https://tailwindcss.com
4. Check Alpine.js documentation: https://alpinejs.dev
