# User Dashboard UI Implementation

## Overview
This document describes the implementation of Task 20: User Dashboard UI for the Laravel ClockIn application.

## Components Created

### 1. Livewire Component
**File:** `app/Livewire/Dashboard/UserDashboard.php`

**Features:**
- Loads dashboard data using `DashboardService`
- Handles clock in/out actions using `AttendanceService`
- Real-time status updates
- Loading states for better UX
- Toast notifications for user feedback
- Refresh functionality

**Methods:**
- `mount()` - Initializes dashboard data
- `loadDashboardData()` - Fetches fresh data from services
- `clockIn()` - Handles clock in action with optional message
- `clockOut()` - Handles clock out action with optional message
- `refreshDashboard()` - Manually refreshes all dashboard data

### 2. Blade View
**File:** `resources/views/livewire/dashboard/user-dashboard.blade.php`

**Sections:**
1. **Header** - Welcome message, refresh button, logout button
2. **Clock In/Out Widget** - Main attendance control with:
   - Current status badge (Clocked In/Not Clocked In)
   - Time since clock in
   - Optional message input
   - Clock in/out button with loading state
3. **Statistics Cards** - Three cards showing:
   - Total hours this month
   - Days worked this month
   - Average hours per day
4. **Recent Attendance** - Last 7 days of attendance records with:
   - Date and status badge
   - In/out times
   - Total worked hours
5. **Upcoming Leaves** - Next 30 days of scheduled leaves with:
   - Date and status badge
   - Leave category
   - Description
6. **Notifications** - Unread notifications with:
   - Title and message
   - Relative timestamp

## UI Components Used

- `x-ui.card` - Container for sections
- `x-ui.badge` - Status indicators (success, warning, danger)
- `x-ui.button` - Action buttons
- `x-ui.empty-state` - Empty state messages

## Styling

- Tailwind CSS for responsive design
- Mobile-first approach
- Grid layout for statistics cards
- Responsive two-column layout for attendance and leaves
- Color-coded status badges:
  - Green (success) - Clocked in, Approved leaves
  - Yellow (warning) - Not clocked in, Pending leaves
  - Red (danger) - Rejected leaves

## Data Flow

1. User accesses `/dashboard` route
2. Livewire component mounts and calls `loadDashboardData()`
3. `DashboardService::getUserDashboard()` fetches:
   - Current attendance status
   - Recent attendance (last 7 days)
   - Upcoming leaves (next 30 days)
   - Unread notifications
   - Monthly statistics (cached for 10 minutes)
4. Data is rendered in the view
5. User interactions (clock in/out, refresh) trigger component methods
6. Component updates data and dispatches toast notifications

## Caching Strategy

- User data: 5 minutes
- Monthly statistics: 10 minutes
- Real-time data (attendance status): No cache
- Cache keys include user ID and month for proper invalidation

## Requirements Met

✅ **Requirement 12.1** - User dashboard displays attendance status, recent records, leaves, and notifications
✅ **Requirement 12.4** - Dashboard displays data for current day/week/month
✅ **Requirement 12.5** - Quick actions for clock in/out
✅ **Requirement 14.1** - PHP-based UI with Livewire and Alpine.js
✅ **Requirement 14.2** - Responsive layouts for desktop, tablet, and mobile

## Testing

All existing dashboard tests pass:
- User dashboard data retrieval
- Attendance status display (clocked in/out)
- Role-based access control
- Statistics calculation
- Authentication requirements

Run tests with:
```bash
php artisan test --filter=DashboardTest
```

## Usage

### Accessing the Dashboard
Navigate to `/dashboard` after logging in. The route is protected by authentication middleware.

### Clock In
1. Enter optional message
2. Click "Clock In" button
3. Success toast notification appears
4. Dashboard refreshes with updated status

### Clock Out
1. Enter optional message
2. Click "Clock Out" button
3. Success toast notification appears
4. Dashboard refreshes with updated status

### Refresh Dashboard
Click the refresh icon in the header to manually reload all dashboard data.

## Future Enhancements

Potential improvements for future iterations:
- Real-time updates using Livewire polling
- Charts for attendance trends
- Quick leave application from dashboard
- Notification mark as read functionality
- Export attendance summary
- Customizable dashboard widgets
