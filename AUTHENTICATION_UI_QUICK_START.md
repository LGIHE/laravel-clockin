# Authentication UI - Quick Start Guide

## Getting Started

### Prerequisites
- Laravel 11.x installed
- Livewire 3.x installed
- Alpine.js configured
- Tailwind CSS configured
- Database configured

### Installation Verification

1. **Check Livewire is installed**:
```bash
php artisan about
# Should show: Livewire v3.6.4 (or later)
```

2. **Verify routes are registered**:
```bash
php artisan route:list --name=login
# Should show login routes
```

3. **Compile assets**:
```bash
npm install
npm run build
```

## Quick Test

### Start the Development Server

```bash
php artisan serve
```

Visit: `http://localhost:8000/login`

### Test Login

**Test User Credentials** (if seeded):
- Email: `admin@example.com`
- Password: `password`

## File Structure

```
laravel-clockin/
├── app/
│   └── Livewire/
│       └── Auth/
│           ├── Login.php
│           ├── ForgotPassword.php
│           └── ResetPassword.php
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   │   └── auth/
│   │   │       ├── login.blade.php
│   │   │       ├── forgot-password.blade.php
│   │   │       └── reset-password.blade.php
│   │   ├── components/
│   │   │   └── layouts/
│   │   │       └── guest.blade.php
│   │   └── dashboard.blade.php
│   └── js/
│       ├── app.js
│       └── toast.js
└── routes/
    └── web.php
```

## Usage Examples

### Displaying Toast Notifications

In any Livewire component:

```php
// Success
$this->dispatch('toast', [
    'message' => 'Operation successful!',
    'variant' => 'success'
]);

// Error
$this->dispatch('toast', [
    'message' => 'Something went wrong!',
    'variant' => 'danger'
]);

// Info
$this->dispatch('toast', [
    'message' => 'Here is some information',
    'variant' => 'info'
]);

// Warning
$this->dispatch('toast', [
    'message' => 'Please be careful!',
    'variant' => 'warning'
]);
```

### Using the Guest Layout

In any Livewire component:

```php
public function render()
{
    return view('livewire.your-component')
        ->layout('components.layouts.guest');
}
```

### Client-Side Validation Pattern

In your Blade view:

```blade
<form wire:submit.prevent="yourMethod"
      x-data="{ 
          email: @entangle('email'),
          emailError: '',
          validateEmail() {
              if (!this.email) {
                  this.emailError = 'Email is required';
                  return false;
              }
              const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
              if (!emailRegex.test(this.email)) {
                  this.emailError = 'Please enter a valid email';
                  return false;
              }
              this.emailError = '';
              return true;
          }
      }">
    
    <x-ui.input 
        type="email" 
        wire:model="email"
        x-model="email"
        @blur="validateEmail()"
    />
    
    <p x-show="emailError" x-text="emailError" class="text-red-600"></p>
</form>
```

## Common Tasks

### Adding a New Auth Page

1. **Create Livewire Component**:
```bash
php artisan make:livewire Auth/YourComponent
```

2. **Add Route**:
```php
// routes/web.php
Route::get('/your-route', YourComponent::class)->name('your-route');
```

3. **Use Guest Layout**:
```php
public function render()
{
    return view('livewire.auth.your-component')
        ->layout('components.layouts.guest');
}
```

### Customizing Toast Duration

In the guest layout or app layout, modify the timeout:

```javascript
setTimeout(() => show = false, 5000); // 5 seconds
// Change to:
setTimeout(() => show = false, 3000); // 3 seconds
```

### Adding Custom Validation

In your Livewire component:

```php
protected $rules = [
    'email' => 'required|email|exists:users,email',
    'password' => 'required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
];

protected $messages = [
    'password.regex' => 'Password must contain uppercase, lowercase, and numbers',
];
```

## Troubleshooting

### Issue: Livewire not updating

**Solution**:
```bash
php artisan view:clear
php artisan cache:clear
```

### Issue: Toast not showing

**Solution**:
1. Check Alpine.js is loaded: `window.Alpine` in browser console
2. Check toast.js is imported in app.js
3. Rebuild assets: `npm run build`

### Issue: Styles not applying

**Solution**:
```bash
npm run build
php artisan view:clear
```

### Issue: Routes not found

**Solution**:
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: CSRF token mismatch

**Solution**:
```bash
php artisan cache:clear
php artisan config:clear
```

## Development Tips

### Hot Reload During Development

```bash
npm run dev
```

This will watch for changes and automatically rebuild assets.

### Debugging Livewire

Add to your component:

```php
public function mount()
{
    logger('Component mounted', ['data' => $this->all()]);
}
```

### Testing Toast Notifications

In browser console:

```javascript
window.dispatchEvent(new CustomEvent('toast', {
    detail: { message: 'Test message', variant: 'success' }
}));
```

## Best Practices

1. **Always validate on both client and server**
2. **Use ARIA labels for accessibility**
3. **Provide loading states for async operations**
4. **Show clear error messages**
5. **Test on multiple browsers**
6. **Test responsive design**
7. **Use semantic HTML**
8. **Keep components focused and single-purpose**

## Next Steps

After authentication UI is working:

1. Test all authentication flows
2. Create automated tests
3. Implement user dashboard (Task 20)
4. Implement supervisor dashboard (Task 21)
5. Implement admin dashboard (Task 22)

## Resources

- [Livewire Documentation](https://livewire.laravel.com)
- [Alpine.js Documentation](https://alpinejs.dev)
- [Tailwind CSS Documentation](https://tailwindcss.com)
- [Laravel Documentation](https://laravel.com/docs)

## Support

For issues or questions:
1. Check the AUTHENTICATION_UI.md documentation
2. Review the test checklist
3. Check Laravel logs: `storage/logs/laravel.log`
4. Check browser console for JavaScript errors

## Quick Commands Reference

```bash
# Start development server
php artisan serve

# Watch and compile assets
npm run dev

# Build assets for production
npm run build

# Clear all caches
php artisan optimize:clear

# List all routes
php artisan route:list

# Check application info
php artisan about

# Run tests (when created)
php artisan test

# Format code
./vendor/bin/pint
```
