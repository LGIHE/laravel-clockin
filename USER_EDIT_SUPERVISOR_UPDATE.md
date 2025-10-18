# User Edit and Change Supervisor Modal Updates

## Overview
Updated the user edit modal and change supervisor modal in the UserList component to support the new primary and secondary supervisor system.

## Changes Made

### 1. UserList Component (`app/Livewire/Users/UserList.php`)

#### Property Updates:
- Changed `editUser['supervisor_ids']` array to:
  - `editUser['primary_supervisor_id']` - Single ID for primary supervisor
  - `editUser['secondary_supervisor_id']` - Single ID for secondary supervisor

- Changed `changeSupervisorData['new_supervisor_ids']` array to:
  - `changeSupervisorData['new_primary_supervisor_id']` - Single ID
  - `changeSupervisorData['new_secondary_supervisor_id']` - Single ID
  - `changeSupervisorData['current_primary_supervisor']` - Display name
  - `changeSupervisorData['current_secondary_supervisor']` - Display name

#### Method Updates:

**`changeSupervisor($userId)`**
- Now loads primary and secondary supervisors separately from pivot table
- Displays current primary and secondary supervisor names
- Pre-fills the form with current supervisor IDs

**`saveChangeSupervisor()`**
- Validates primary and secondary supervisor IDs
- Ensures secondary is different from primary
- Uses `UserService::assignSupervisor()` with new format
- Prevents user from being their own supervisor

**`openEditUserModal($userId)`**
- Loads primary and secondary supervisor IDs from user's supervisors relationship
- Checks pivot table `supervisor_type` to determine which is which
- Pre-fills the form fields correctly

**`updateUser()`**
- Validates primary and secondary supervisor fields
- Uses `UserService::assignSupervisor()` with new format
- Simplified validation (no circular relationship checks needed)

**Removed Methods:**
- `removeEditUserSupervisor()` - No longer needed with single selects
- `removeChangeSupervisor()` - No longer needed with single selects

### 2. View Updates (`resources/views/livewire/users/user-list.blade.php`)

#### Change Supervisor Modal:
**Before:**
- Multi-select dropdown for supervisors
- "Hold Ctrl/Cmd to select multiple" instructions
- Chip display with remove buttons

**After:**
- Two separate single-select dropdowns:
  - Primary Supervisor dropdown
  - Secondary Supervisor dropdown
- Current supervisors displayed separately (primary and secondary)
- Secondary dropdown disables primary supervisor option
- Helpful text explaining primary receives notifications

#### Edit User Modal:
**Before:**
- Multi-select dropdown for supervisors
- Size="5" for multiple selection
- Chip display with remove buttons

**After:**
- Two separate single-select dropdowns:
  - Primary Supervisor dropdown
  - Secondary Supervisor dropdown
- Each in its own grid column
- Secondary dropdown disables primary supervisor option
- Helpful text for each field

### 3. Validation Rules

**Change Supervisor Modal:**
```php
'changeSupervisorData.new_primary_supervisor_id' => 'nullable|exists:users,id'
'changeSupervisorData.new_secondary_supervisor_id' => 'nullable|exists:users,id|different:changeSupervisorData.new_primary_supervisor_id'
```

**Edit User Modal:**
```php
'editUser.primary_supervisor_id' => 'nullable|exists:users,id'
'editUser.secondary_supervisor_id' => 'nullable|exists:users,id|different:editUser.primary_supervisor_id'
```

## User Experience Improvements

### Before:
1. Confusing multi-select interface
2. Users could select many supervisors
3. Unclear who receives notifications
4. Required Ctrl/Cmd key knowledge
5. Easy to accidentally deselect all

### After:
1. Clear, simple dropdown selections
2. Exactly one primary, one secondary (or none)
3. Explicitly states primary receives notifications
4. Standard dropdown interaction
5. Cannot accidentally clear selections
6. Visual feedback when same person selected for both

## UI Features

### Change Supervisor Modal:
- Shows current primary and secondary supervisors in info box
- Separate labeled dropdowns for each type
- Secondary dropdown automatically disables primary selection
- Clear "Primary receives notifications" helper text

### Edit User Modal:
- Integrated into existing form layout
- Consistent with other form fields
- Side-by-side layout for primary and secondary
- Excludes the user being edited from supervisor options

## Data Flow

### Loading Supervisors:
```php
foreach ($user->supervisors as $supervisor) {
    if ($supervisor->pivot->supervisor_type === 'primary') {
        $this->editUser['primary_supervisor_id'] = $supervisor->id;
    } elseif ($supervisor->pivot->supervisor_type === 'secondary') {
        $this->editUser['secondary_supervisor_id'] = $supervisor->id;
    }
}
```

### Saving Supervisors:
```php
$this->userService->assignSupervisor($user->id, [
    'primary' => $this->editUser['primary_supervisor_id'],
    'secondary' => $this->editUser['secondary_supervisor_id'],
]);
```

## Testing Checklist

- [ ] Open Change Supervisor modal - displays current supervisors correctly
- [ ] Change primary supervisor - saves successfully
- [ ] Change secondary supervisor - saves successfully
- [ ] Select same person for both - shows validation error
- [ ] Clear both supervisors - saves successfully (no supervisors)
- [ ] Open Edit User modal - pre-fills current supervisors
- [ ] Edit user with new primary supervisor - saves correctly
- [ ] Edit user with new secondary supervisor - saves correctly
- [ ] Try to select user as their own supervisor - prevented
- [ ] Secondary dropdown disables primary selection - works correctly

## Benefits

1. **Clearer Interface**: No confusion about multi-select
2. **Better UX**: Standard dropdown interactions
3. **Explicit Roles**: Clear distinction between primary and secondary
4. **Validation**: Built-in prevention of duplicate selections
5. **Consistency**: Matches the user creation form
6. **Mobile Friendly**: Single selects work better on mobile
7. **Accessibility**: Better screen reader support

## Backward Compatibility

- Existing supervisor relationships are preserved
- UserService handles both old and new formats
- No data migration needed
- Existing code continues to work
