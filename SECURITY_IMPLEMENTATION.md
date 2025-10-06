# Security Implementation Summary

This document summarizes the security features implemented for the Laravel ClockIn application.

## Implemented Security Features

### 1. CORS Configuration
**File:** `config/cors.php`

- Configured allowed origins from environment variable (`CORS_ALLOWED_ORIGINS`)
- Default origins: `http://localhost:3000`, `http://localhost:5173`
- Enabled credentials support for authenticated requests
- Set max age to 24 hours for preflight caching
- Exposed Authorization header for client access

**Environment Variables:**
```env
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:5173
```

### 2. Rate Limiting
**Files:** 
- `app/Providers/AppServiceProvider.php`
- `routes/api.php`
- `bootstrap/app.php`

**Implemented Rate Limiters:**
- **Login:** 5 attempts per minute per IP address
- **API:** 60 requests per minute per authenticated user
- **Sensitive Operations:** 10 requests per minute per user
- **Password Reset:** 5 requests per minute per IP address

**Applied To:**
- `/api/auth/login` - 5 requests/minute
- `/api/auth/forgot-password` - 5 requests/minute
- `/api/auth/reset-password` - 5 requests/minute
- All API routes - 60 requests/minute per user

### 3. Authorization Policies
**Files:** `app/Policies/`

Created comprehensive authorization policies for:

#### LeavePolicy
- Users can view/create their own leaves
- Supervisors can view/approve team member leaves
- Admins can view/approve all leaves
- Users cannot approve their own leaves

#### AttendancePolicy
- Users can view their own attendance
- Supervisors can view team attendance
- Only admins can update/delete attendance records
- Only admins can force punch

#### UserPolicy
- Only admins can create/delete users
- Users can view/update their own profile
- Admins cannot delete themselves
- Only admins can assign supervisors/projects

#### ProjectPolicy
- All users can view projects
- Only admins can create/update/delete projects
- Only admins can assign/remove users from projects

**Policy Registration:**
Policies are automatically registered in `AppServiceProvider::boot()`

### 4. Input Sanitization
**File:** `app/Traits/SanitizesInput.php`

Created a reusable trait for input sanitization that:
- Trims whitespace from all string inputs
- Removes null bytes
- Strips HTML tags (configurable per request)
- Recursively sanitizes nested arrays

**Applied To:**
- `LoginRequest`
- `CreateUserRequest`
- `ClockInRequest`
- Can be added to any Form Request class

**Usage:**
```php
use App\Traits\SanitizesInput;

class YourRequest extends FormRequest
{
    use SanitizesInput;
    
    // Your validation rules...
}
```

### 5. Secure Session Configuration
**File:** `config/session.php`

Enhanced session security with:
- Session encryption enabled by default
- Expire on close enabled
- Secure cookies (HTTPS only) in production
- HTTP-only cookies (JavaScript cannot access)
- SameSite set to 'strict' for CSRF protection

**Environment Variables:**
```env
SESSION_ENCRYPT=true
SESSION_EXPIRE_ON_CLOSE=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

### 6. CSRF Protection
**File:** `bootstrap/app.php`

- CSRF protection enabled for web routes
- API routes excluded (use token authentication)
- Configured in middleware stack

## Security Tests

Comprehensive security tests implemented in `tests/Feature/Security/`:

### RateLimitingTest (4 tests)
- ✓ Login rate limiting
- ✓ API rate limiting for authenticated users
- ✓ Forgot password rate limiting
- ✓ Rate limiting is per IP address

### CorsTest (5 tests)
- ✓ CORS headers are present
- ✓ CORS allows configured origins
- ✓ CORS preflight requests
- ✓ CORS supports credentials
- ✓ CORS exposes authorization header

### AuthorizationPolicyTest (10 tests)
- ✓ User can view own leave
- ✓ User cannot view another user's leave
- ✓ Supervisor can view team members leave
- ✓ Admin can view any leave
- ✓ User cannot approve own leave
- ✓ Supervisor can approve team members leave
- ✓ Only admin can update attendance
- ✓ Only admin can create projects
- ✓ Only admin can delete users
- ✓ Admin cannot delete themselves

### InputSanitizationTest (8 tests)
- ✓ Login input is sanitized
- ✓ HTML tags are stripped
- ✓ Null bytes are removed
- ✓ SQL injection is prevented
- ✓ XSS is prevented in attendance messages
- ✓ Validation prevents malicious paths
- ✓ Email validation prevents invalid formats
- ✓ Password validation enforces minimum length

**Total: 27 security tests, all passing**

## Best Practices Implemented

1. **Defense in Depth:** Multiple layers of security (rate limiting, validation, sanitization, policies)
2. **Principle of Least Privilege:** Users only have access to what they need
3. **Secure by Default:** Security features enabled by default in configuration
4. **Input Validation:** All user input is validated and sanitized
5. **Output Encoding:** HTML tags stripped to prevent XSS
6. **Authentication & Authorization:** Proper separation of concerns
7. **Rate Limiting:** Protection against brute force and DoS attacks
8. **CORS:** Controlled cross-origin access
9. **Session Security:** Encrypted, secure, HTTP-only cookies

## Usage Guidelines

### Adding Sanitization to New Form Requests
```php
use App\Traits\SanitizesInput;

class YourRequest extends FormRequest
{
    use SanitizesInput;
    
    // If you need to allow HTML in specific fields:
    protected function htmlAllowedFields(): array
    {
        return ['description', 'message'];
    }
}
```

### Using Policies in Controllers
```php
// Check authorization before action
$this->authorize('update', $leave);

// Or in routes
Route::put('/leaves/{leave}', [LeaveController::class, 'update'])
    ->can('update', 'leave');
```

### Configuring Rate Limits
Edit `app/Providers/AppServiceProvider.php`:
```php
RateLimiter::for('custom', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
});
```

Then apply in routes:
```php
Route::middleware('throttle:custom')->group(function () {
    // Your routes
});
```

## Production Deployment Checklist

- [ ] Set `CORS_ALLOWED_ORIGINS` to production frontend URL
- [ ] Enable `SESSION_SECURE_COOKIE=true` (requires HTTPS)
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper `SESSION_DOMAIN`
- [ ] Review and adjust rate limits based on traffic
- [ ] Enable security logging and monitoring
- [ ] Set up SSL/TLS certificates
- [ ] Configure firewall rules
- [ ] Enable database query logging for suspicious activity
- [ ] Set up intrusion detection system (IDS)

## Security Monitoring

The application logs security events to:
- `storage/logs/security-{date}.log` - Unauthorized access attempts
- `storage/logs/auth-{date}.log` - Authentication failures

Monitor these logs regularly for suspicious activity.

## Future Enhancements

Consider implementing:
1. Two-factor authentication (2FA)
2. API key management for third-party integrations
3. IP whitelisting for admin access
4. Security headers (CSP, HSTS, X-Frame-Options)
5. Automated security scanning in CI/CD pipeline
6. Regular security audits and penetration testing
7. Database encryption at rest
8. Audit logging for all sensitive operations
