# Global Error Handling and Validation Implementation

## Overview
This document describes the implementation of global error handling and validation for the Laravel ClockIn application, completed as part of Task 15.

## Implementation Summary

### 1. Custom Exception Classes

#### BusinessLogicException (`app/Exceptions/BusinessLogicException.php`)
- Custom exception for domain-specific business logic errors
- Default status code: 400
- Default error code: 'BUSINESS_LOGIC_ERROR'
- Allows customization of status code, error code, and message

#### ResourceNotFoundException (`app/Exceptions/ResourceNotFoundException.php`)
- Custom exception for missing resources
- Default status code: 404
- Default error code: 'RESOURCE_NOT_FOUND'
- Allows customization of error code and message

### 2. Global Exception Handler

The exception handler is configured in `bootstrap/app.php` using Laravel 11's new exception handling pattern. It provides consistent JSON error responses for API requests with the following structure:

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Error message",
    "errors": {} // Only for validation errors
  }
}
```

#### Handled Exception Types:
- **ValidationException** (422): Returns validation errors with field-specific messages
- **AuthenticationException** (401): Returns authentication error and logs to auth channel
- **AuthorizationException** (403): Returns authorization error and logs to security channel
- **ModelNotFoundException** (404): Returns resource not found error
- **NotFoundHttpException** (404): Returns not found error
- **BusinessLogicException** (custom): Returns custom business logic error
- **ResourceNotFoundException** (custom): Returns custom resource not found error
- **HttpException** (varies): Returns HTTP error with appropriate status code
- **All other exceptions** (500): Returns internal server error and logs details

### 3. Security Event Logging

#### LogsSecurityEvents Trait (`app/Traits/LogsSecurityEvents.php`)
A reusable trait that provides methods for logging security-related events:

- `logFailedLogin()`: Logs failed login attempts to auth channel
- `logSuccessfulLogin()`: Logs successful logins to auth channel
- `logUnauthorizedAccess()`: Logs unauthorized access attempts to security channel
- `logSuspiciousActivity()`: Logs suspicious activities to security channel
- `logPasswordChange()`: Logs password changes to security channel
- `logPasswordResetRequest()`: Logs password reset requests to auth channel
- `logLogout()`: Logs logout events to auth channel

#### Integration with AuthService
The `AuthService` has been updated to use the `LogsSecurityEvents` trait and now logs:
- Failed login attempts (invalid credentials or inactive accounts)
- Successful logins
- Logout events
- Password reset requests
- Password changes

#### Integration with CheckRole Middleware
The `CheckRole` middleware now logs unauthorized access attempts to the security channel, including:
- User ID and role
- Required roles
- IP address
- Request URL and method
- Timestamp

### 4. Logging Configuration

#### New Log Channels (`config/logging.php`)

**Security Channel:**
- Driver: daily
- Path: `storage/logs/security.log`
- Level: warning
- Retention: 14 days
- Purpose: Logs security-related events (unauthorized access, suspicious activities)

**Auth Channel:**
- Driver: daily
- Path: `storage/logs/auth.log`
- Level: info
- Retention: 14 days
- Purpose: Logs authentication-related events (logins, logouts, password resets)

### 5. Comprehensive Test Coverage

All error handling functionality is covered by tests in `tests/Feature/ErrorHandling/`:

#### ValidationErrorTest
- Tests validation error response structure
- Tests validation error formatting
- Tests validation error messages

#### AuthenticationErrorTest
- Tests unauthenticated request handling (401)
- Tests invalid token handling
- Tests authentication failure logging
- Tests failed login responses
- Tests inactive user login handling

#### AuthorizationErrorTest
- Tests unauthorized access handling (403)
- Tests role-based access control
- Tests authorization failure logging
- Tests proper error messages

#### NotFoundErrorTest
- Tests 404 responses for missing routes
- Tests 404 responses for missing resources
- Tests model not found handling
- Tests error response structure

#### CustomExceptionTest
- Tests BusinessLogicException behavior
- Tests ResourceNotFoundException behavior
- Tests default values and customization

#### SecurityLoggingTest
- Tests failed login logging
- Tests successful login logging
- Tests unauthorized access logging
- Tests logout logging
- Tests password reset request logging
- Tests inactive account login attempt logging

## Test Results

All 36 tests pass successfully with 99 assertions:
- 7 authentication error tests
- 7 authorization error tests
- 5 custom exception tests
- 7 not found error tests
- 6 security logging tests
- 4 validation error tests

## Usage Examples

### Using Custom Exceptions

```php
// In a service or controller
use App\Exceptions\BusinessLogicException;
use App\Exceptions\ResourceNotFoundException;

// Throw business logic exception
throw new BusinessLogicException('User already clocked in', 400, 'ALREADY_CLOCKED_IN');

// Throw resource not found exception
throw new ResourceNotFoundException('User not found', 'USER_NOT_FOUND');
```

### Using Security Logging Trait

```php
// In a service class
use App\Traits\LogsSecurityEvents;

class MyService
{
    use LogsSecurityEvents;
    
    public function someMethod()
    {
        // Log a failed login
        $this->logFailedLogin($email, $ip, $userAgent);
        
        // Log unauthorized access
        $this->logUnauthorizedAccess($userId, 'delete_user', $ip, 'users/123');
        
        // Log suspicious activity
        $this->logSuspiciousActivity('Multiple failed login attempts', [
            'email' => $email,
            'attempts' => 5,
            'ip' => $ip
        ]);
    }
}
```

### Error Response Examples

**Validation Error (422):**
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid.",
    "errors": {
      "email": ["The email field is required."],
      "password": ["The password must be at least 6 characters."]
    }
  }
}
```

**Authentication Error (401):**
```json
{
  "success": false,
  "error": {
    "code": "AUTHENTICATION_ERROR",
    "message": "Unauthenticated."
  }
}
```

**Authorization Error (403):**
```json
{
  "success": false,
  "error": {
    "code": "AUTHORIZATION_ERROR",
    "message": "This action is unauthorized."
  }
}
```

**Not Found Error (404):**
```json
{
  "success": false,
  "error": {
    "code": "NOT_FOUND",
    "message": "The requested resource was not found."
  }
}
```

## Requirements Satisfied

This implementation satisfies the following requirements from the specification:

- **Requirement 13.5**: Handling errors with Laravel's exception handling with custom error responses
- **Requirement 16.1**: Validating and sanitizing all inputs
- **Requirement 16.7**: Never logging passwords or tokens (implemented in trait and exception handler)

## Security Considerations

1. **Sensitive Data Protection**: The logging system never logs passwords or tokens
2. **Failed Login Tracking**: All failed login attempts are logged for security monitoring
3. **Unauthorized Access Monitoring**: All unauthorized access attempts are logged with context
4. **IP Address Tracking**: All security events include IP addresses for audit trails
5. **Separate Log Channels**: Security and authentication events are logged to separate channels for easier monitoring

## Maintenance Notes

- Log files are automatically rotated daily and retained for 14 days
- Security logs should be monitored regularly for suspicious patterns
- The exception handler can be extended to handle additional custom exceptions
- The LogsSecurityEvents trait can be used in any service or controller that needs security logging
