# Primary and Secondary Supervisor Implementation

## Overview
Enhanced the supervisor assignment system to support primary and secondary supervisors. Only the primary supervisor receives leave request notifications and emails.

## Changes Made

### 1. Database Changes

#### Migration: `fix_user_supervisor_constraints`
- Added `supervisor_type` enum column ('primary', 'secondary') to `user_supervisor` table
- Changed unique constraint from `[user_id, supervisor_id]` to `[user_id, supervisor_type]`
- This allows a user to have one primary and one secondary supervisor

### 2. Model Updates

#### User Model (`app/Models/User.php`)
Added new relationship methods:
- `primarySupervisor()` - Returns the primary supervisor
- `secondarySupervisor()` - Returns the secondary supervisor
- Updated `supervisors()` to include `supervisor_type` in pivot
- Updated `supervisor()` to prioritize primary supervisor

### 3. Service Updates

#### UserService (`app/Services/UserService.php`)
Updated `assignSupervisor()` method to handle:
- New format: `['primary' => id, 'secondary' => id]`
- Legacy format: Array of IDs (first = primary, second = secondary)
- Validates that primary and secondary are different users
- Syncs supervisors with their types to the pivot table

#### LeaveService (`app/Services/LeaveService.php`)
- Changed to only notify **primary supervisor** when leave is applied
- Sends both in-app notification and email to primary supervisor only
- Secondary supervisor is not notified (backup role only)

#### NotificationService (`app/Services/NotificationService.php`)
- Updated `notifyLeaveRequest()` to accept a single supervisor instead of multiple
- Simplified notification logic for single supervisor

### 4. UI Updates

#### UserForm Component (`app/Livewire/Users/UserForm.php`)
- Replaced `selectedSupervisors` array with:
  - `primarySupervisorId` - Single select for primary supervisor
  - `secondarySupervisorId` - Single select for secondary supervisor
- Added validation to ensure secondary is different from primary
- Updated save logic to pass supervisors in new format

#### UserForm View (`resources/views/livewire/users/user-form.blade.php`)
- Replaced multi-select dropdown with two separate dropdowns:
  - **Primary Supervisor** - Required for leave notifications
  - **Secondary Supervisor** - Optional backup
- Added helpful text explaining that primary supervisor receives notifications
- Secondary supervisor dropdown disables the primary supervisor option

## How It Works

### When Creating/Editing a User:
1. Admin selects a primary supervisor (optional)
2. Admin can select a secondary supervisor (optional, must be different from primary)
3. System saves both with their respective types in the pivot table

### When a User Applies for Leave:
1. System looks up the user's primary supervisor
2. If primary supervisor exists:
   - Sends in-app notification to primary supervisor
   - Sends email to primary supervisor
3. If no primary supervisor:
   - Logs a warning but doesn't fail
   - Leave is still created successfully

### Secondary Supervisor Role:
- Acts as a backup/reference
- Does NOT receive leave notifications
- Can be used for organizational hierarchy
- Can be promoted to primary if needed

## Benefits

1. **Clear Responsibility**: Only one person receives leave notifications
2. **Reduced Noise**: Supervisors don't get duplicate notifications
3. **Backup System**: Secondary supervisor available if primary is unavailable
4. **Flexible**: Can have primary only, both, or neither
5. **Backward Compatible**: Legacy code still works with `supervisor()` method

## Database Structure

```
user_supervisor table:
- id (bigint)
- user_id (char 36)
- supervisor_id (char 36)
- supervisor_type (enum: 'primary', 'secondary')
- created_at (timestamp)
- updated_at (timestamp)

Unique constraint: [user_id, supervisor_type]
(A user can have only one primary and one secondary supervisor)
```

## Testing

To test the new functionality:

1. **Create/Edit a User**:
   - Go to Users > Create/Edit User
   - Select a primary supervisor
   - Optionally select a secondary supervisor
   - Save

2. **Apply for Leave**:
   - Login as the user
   - Apply for leave
   - Check that only the primary supervisor receives:
     - In-app notification
     - Email notification

3. **Verify Secondary Supervisor**:
   - Secondary supervisor should NOT receive notifications
   - But relationship is stored in database for reference

## Migration Notes

- Existing supervisor relationships are preserved
- First supervisor in existing relationships becomes primary
- Second supervisor (if exists) becomes secondary
- No data loss during migration
