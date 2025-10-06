# Notification UI Implementation

## Overview

The notification UI has been successfully implemented as a dropdown component that displays in the application header. This provides users with real-time access to their notifications with an unread count badge.

## Components Created

### 1. NotificationDropdown Livewire Component
**Location:** `app/Livewire/Notifications/NotificationDropdown.php`

**Features:**
- Fetches unread notifications for the authenticated user
- Displays unread notification count badge
- Allows marking individual notifications as read
- Allows marking all notifications as read
- Auto-refreshes notification list after actions
- Limits display to 5 most recent unread notifications

**Methods:**
- `loadNotifications()` - Fetches notifications from database
- `markAsRead($notificationId)` - Marks a single notification as read
- `markAllAsRead()` - Marks all unread notifications as read
- `toggleDropdown()` - Controls dropdown visibility

### 2. Notification Dropdown Blade View
**Location:** `resources/views/livewire/notifications/notification-dropdown.blade.php`

**Features:**
- Bell icon with unread count badge (shows "99+" for counts over 99)
- Dropdown panel with smooth transitions
- Empty state when no notifications
- Notification list with icons based on type (Leave, Attendance, etc.)
- Timestamp display using Carbon's `diffForHumans()`
- Click to mark as read functionality
- "Mark all as read" button
- "View all notifications" link in footer

### 3. Updated Header Component
**Location:** `resources/views/components/layout/header.blade.php`

The header component now includes the notification dropdown for authenticated users.

### 4. Updated Toast Component
**Location:** `resources/views/components/ui/toast.blade.php`

Enhanced to support dynamic variant selection from Livewire events.

## Usage

### Adding Notification Dropdown to Pages

The notification dropdown is automatically included in the header component. To use it in your pages:

```blade
<x-layout.header>
    <div class="flex-1">
        <h1 class="text-2xl font-bold text-gray-900">Page Title</h1>
        <p class="text-sm text-gray-600 mt-1">Page description</p>
    </div>

    <x-slot:actions>
        <!-- Your action buttons here -->
    </x-slot:actions>
</x-layout.header>
```

### Creating Notifications

Notifications are created in the database with the following structure:

```php
DB::table('notifications')->insert([
    'id' => Str::uuid(),
    'type' => 'App\\Notifications\\LeaveApproved', // Notification type
    'notifiable_type' => 'App\\Models\\User',
    'notifiable_id' => $userId,
    'data' => json_encode([
        'title' => 'Leave Approved',
        'message' => 'Your leave request has been approved.',
    ]),
    'read_at' => null,
    'created_at' => now(),
    'updated_at' => now(),
]);
```

### Triggering Toast Notifications

From Livewire components:

```php
$this->dispatch('toast', [
    'message' => 'Notification marked as read',
    'variant' => 'success', // Options: info, success, warning, danger
]);
```

## Styling

The notification dropdown uses Tailwind CSS classes and follows the shadcn/ui design patterns:

- **Colors:** Blue for primary actions, red for unread badge
- **Transitions:** Smooth fade and scale animations
- **Responsive:** Works on mobile and desktop
- **Accessibility:** Includes ARIA labels and keyboard navigation support

## Features Implemented

✅ Notification bell icon in header  
✅ Unread notification count badge  
✅ Dropdown panel with notification list  
✅ Mark individual notification as read  
✅ Mark all notifications as read  
✅ Display notification timestamps  
✅ Empty state when no notifications  
✅ Icon differentiation by notification type  
✅ Toast feedback for actions  
✅ Responsive design  
✅ Click-away to close dropdown  

## Database Schema

The component works with the existing `notifications` table:

```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id CHAR(36) NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

## Testing

To test the notification dropdown:

1. **Create test notifications:**
   ```php
   DB::table('notifications')->insert([
       'id' => Str::uuid(),
       'type' => 'App\\Notifications\\TestNotification',
       'notifiable_type' => 'App\\Models\\User',
       'notifiable_id' => auth()->id(),
       'data' => json_encode([
           'title' => 'Test Notification',
           'message' => 'This is a test notification.',
       ]),
       'read_at' => null,
       'created_at' => now(),
       'updated_at' => now(),
   ]);
   ```

2. **Navigate to any page with the header component** (e.g., `/notices`)

3. **Verify:**
   - Bell icon shows unread count badge
   - Clicking bell opens dropdown
   - Notifications are displayed with correct information
   - Clicking a notification marks it as read
   - "Mark all as read" button works
   - Toast notifications appear for actions

## Integration with Existing Features

The notification dropdown integrates seamlessly with:

- **Leave Management:** Notifications created when leaves are approved/rejected
- **Attendance:** Notifications for attendance-related events
- **Dashboard:** Notifications displayed in dashboard views
- **Toast System:** Uses existing toast component for feedback

## Future Enhancements

Potential improvements for future iterations:

- Real-time notifications using Laravel Echo and WebSockets
- Notification preferences/settings
- Notification categories and filtering
- Notification sound alerts
- Desktop push notifications
- Notification history page
- Bulk notification actions

## Requirements Satisfied

This implementation satisfies the following requirements from the specification:

- **10.1:** Notifications created for leave approvals/rejections
- **10.2:** Unread notifications displayed prominently
- **10.3:** Mark notification as read functionality
- **14.1:** Notification icon in header
- **14.2:** Role-based notification access

## Notes

- The component uses Alpine.js for dropdown interactivity
- Livewire handles server-side logic and state management
- The dropdown is positioned absolutely and uses z-index 50 to appear above other content
- The component automatically refreshes when notifications are marked as read
- The unread count badge displays "99+" for counts over 99 to prevent layout issues
