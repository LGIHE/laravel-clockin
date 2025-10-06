# Authentication UI - Test Checklist

## Manual Testing Checklist

### Login Page (`/login`)

#### Visual/Layout Tests
- [ ] Page loads without errors
- [ ] ClockIn logo is displayed
- [ ] Form is centered and responsive
- [ ] All form fields are visible
- [ ] Submit button is styled correctly
- [ ] "Forgot password?" link is visible
- [ ] Footer with copyright is displayed

#### Functionality Tests
- [ ] Email field accepts input
- [ ] Password field accepts input
- [ ] Password visibility toggle works
- [ ] Remember me checkbox works
- [ ] Form submits on Enter key
- [ ] Loading spinner shows during submission
- [ ] Button is disabled during loading

#### Validation Tests (Client-Side)
- [ ] Empty email shows error on blur
- [ ] Invalid email format shows error
- [ ] Empty password shows error on blur
- [ ] Password less than 6 characters shows error
- [ ] Form submission prevented if validation fails
- [ ] Error messages are displayed inline
- [ ] Error messages are accessible (ARIA)

#### Validation Tests (Server-Side)
- [ ] Invalid credentials show error message
- [ ] Inactive account shows error message
- [ ] Error toast notification appears
- [ ] Error persists after page interaction

#### Success Flow
- [ ] Valid credentials redirect to dashboard
- [ ] Success toast notification appears
- [ ] User session is created
- [ ] Remember me persists session

#### Accessibility Tests
- [ ] Tab navigation works through all fields
- [ ] ARIA labels are present
- [ ] Screen reader can read all content
- [ ] Focus indicators are visible
- [ ] Error messages are announced

#### Responsive Tests
- [ ] Mobile view (< 640px) works correctly
- [ ] Tablet view (640px - 1024px) works correctly
- [ ] Desktop view (> 1024px) works correctly
- [ ] Touch interactions work on mobile

---

### Forgot Password Page (`/forgot-password`)

#### Visual/Layout Tests
- [ ] Page loads without errors
- [ ] ClockIn logo is displayed
- [ ] Form is centered and responsive
- [ ] Email field is visible
- [ ] Submit button is styled correctly
- [ ] "Back to login" link is visible
- [ ] Footer with copyright is displayed

#### Functionality Tests
- [ ] Email field accepts input
- [ ] Form submits on Enter key
- [ ] Loading spinner shows during submission
- [ ] Button is disabled during loading
- [ ] Success message appears after submission
- [ ] "Back to login" link navigates correctly

#### Validation Tests (Client-Side)
- [ ] Empty email shows error on blur
- [ ] Invalid email format shows error
- [ ] Form submission prevented if validation fails
- [ ] Error messages are displayed inline

#### Validation Tests (Server-Side)
- [ ] Non-existent email handled gracefully
- [ ] Error toast notification appears for failures
- [ ] Success toast notification appears for success

#### Success Flow
- [ ] Success message box appears
- [ ] Success toast notification appears
- [ ] Email is sent (check email)
- [ ] Form can be resubmitted if needed

#### Accessibility Tests
- [ ] Tab navigation works
- [ ] ARIA labels are present
- [ ] Screen reader compatibility
- [ ] Focus indicators are visible

#### Responsive Tests
- [ ] Mobile view works correctly
- [ ] Tablet view works correctly
- [ ] Desktop view works correctly

---

### Reset Password Page (`/reset-password/{token}`)

#### Visual/Layout Tests
- [ ] Page loads without errors
- [ ] ClockIn logo is displayed
- [ ] Form is centered and responsive
- [ ] All three fields are visible (email, password, confirm)
- [ ] Submit button is styled correctly
- [ ] "Back to login" link is visible
- [ ] Footer with copyright is displayed

#### Functionality Tests
- [ ] Email field is pre-filled from query parameter
- [ ] Email field accepts input
- [ ] Password field accepts input
- [ ] Password confirmation field accepts input
- [ ] Password visibility toggle works for password field
- [ ] Password visibility toggle works for confirmation field
- [ ] Form submits on Enter key
- [ ] Loading spinner shows during submission
- [ ] Button is disabled during loading

#### Validation Tests (Client-Side)
- [ ] Empty email shows error on blur
- [ ] Invalid email format shows error
- [ ] Empty password shows error on blur
- [ ] Password less than 6 characters shows error
- [ ] Empty confirmation shows error on blur
- [ ] Mismatched passwords show error
- [ ] Form submission prevented if validation fails
- [ ] Error messages are displayed inline

#### Validation Tests (Server-Side)
- [ ] Invalid token shows error message
- [ ] Expired token shows error message
- [ ] Email mismatch shows error message
- [ ] Error toast notification appears

#### Success Flow
- [ ] Valid reset redirects to login
- [ ] Success toast notification appears
- [ ] Success message on login page
- [ ] Can login with new password

#### Accessibility Tests
- [ ] Tab navigation works through all fields
- [ ] ARIA labels are present
- [ ] Screen reader compatibility
- [ ] Focus indicators are visible
- [ ] Error messages are announced

#### Responsive Tests
- [ ] Mobile view works correctly
- [ ] Tablet view works correctly
- [ ] Desktop view works correctly

---

### Toast Notifications

#### Visual Tests
- [ ] Toast appears in top-right corner
- [ ] Toast has correct color for variant (success=green, danger=red, info=blue, warning=yellow)
- [ ] Toast has correct icon for variant
- [ ] Toast message is readable
- [ ] Close button is visible

#### Functionality Tests
- [ ] Toast auto-dismisses after 5 seconds
- [ ] Close button dismisses toast immediately
- [ ] Multiple toasts can be shown (if triggered)
- [ ] Toast animations are smooth

#### Variant Tests
- [ ] Success variant displays correctly
- [ ] Danger variant displays correctly
- [ ] Info variant displays correctly
- [ ] Warning variant displays correctly

---

### Dashboard (Authenticated Area)

#### Access Tests
- [ ] Unauthenticated users redirected to login
- [ ] Authenticated users can access dashboard
- [ ] Dashboard displays user information
- [ ] Logout button is visible

#### Logout Tests
- [ ] Logout button works
- [ ] Session is invalidated
- [ ] Redirected to login page
- [ ] Cannot access dashboard after logout

---

### Cross-Browser Testing

#### Chrome
- [ ] All features work
- [ ] Styling is correct
- [ ] Animations are smooth

#### Firefox
- [ ] All features work
- [ ] Styling is correct
- [ ] Animations are smooth

#### Safari
- [ ] All features work
- [ ] Styling is correct
- [ ] Animations are smooth

#### Edge
- [ ] All features work
- [ ] Styling is correct
- [ ] Animations are smooth

---

### Performance Tests

- [ ] Page loads in < 2 seconds
- [ ] Form submission responds in < 1 second
- [ ] No console errors
- [ ] No console warnings
- [ ] Assets are minified
- [ ] Images are optimized

---

### Security Tests

- [ ] CSRF token is present in forms
- [ ] Passwords are not visible in network tab
- [ ] Tokens are not logged in console
- [ ] XSS protection is working
- [ ] SQL injection protection is working

---

## Automated Testing Checklist

### Unit Tests (To Be Created)
- [ ] Login component validation logic
- [ ] Forgot password component validation logic
- [ ] Reset password component validation logic
- [ ] Toast notification helper

### Feature Tests (To Be Created)
- [ ] Login with valid credentials
- [ ] Login with invalid credentials
- [ ] Login with inactive account
- [ ] Forgot password email sending
- [ ] Reset password with valid token
- [ ] Reset password with invalid token
- [ ] Reset password with expired token
- [ ] Logout functionality
- [ ] Session management
- [ ] CSRF protection

### Integration Tests (To Be Created)
- [ ] Complete login flow
- [ ] Complete forgot password flow
- [ ] Complete reset password flow
- [ ] Authentication middleware
- [ ] Guest middleware

---

## Test Results

### Date: ___________
### Tester: ___________

| Test Category | Pass | Fail | Notes |
|--------------|------|------|-------|
| Login Page Visual | [ ] | [ ] | |
| Login Page Functionality | [ ] | [ ] | |
| Login Page Validation | [ ] | [ ] | |
| Forgot Password Visual | [ ] | [ ] | |
| Forgot Password Functionality | [ ] | [ ] | |
| Forgot Password Validation | [ ] | [ ] | |
| Reset Password Visual | [ ] | [ ] | |
| Reset Password Functionality | [ ] | [ ] | |
| Reset Password Validation | [ ] | [ ] | |
| Toast Notifications | [ ] | [ ] | |
| Dashboard Access | [ ] | [ ] | |
| Cross-Browser | [ ] | [ ] | |
| Performance | [ ] | [ ] | |
| Security | [ ] | [ ] | |
| Accessibility | [ ] | [ ] | |
| Responsive Design | [ ] | [ ] | |

---

## Issues Found

| Issue # | Description | Severity | Status | Notes |
|---------|-------------|----------|--------|-------|
| | | | | |
| | | | | |
| | | | | |

---

## Sign-Off

- [ ] All critical tests passed
- [ ] All high-priority issues resolved
- [ ] Documentation is complete
- [ ] Code is committed to version control

**Tester Signature**: _____________________ **Date**: ___________

**Developer Signature**: __________________ **Date**: ___________
