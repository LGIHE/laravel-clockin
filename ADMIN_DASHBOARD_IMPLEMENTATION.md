# Admin Dashboard Implementation

## Overview
This document describes the implementation of the Admin Dashboard UI for the Laravel ClockIn application.

## Components Created

### 1. Livewire Component
**File:** `app/Livewire/Dashboard/AdminDashboard.php`

**Features:**
- Loads system-wide statistics from DashboardService
- Displays recent activities
- Shows pending leave approvals across all teams
- Presents department-wise statistics
- Implements quick approval/rejection actions for leaves
- Includes approval modal for detailed review

**Methods:**
- `mount()` - Initializes dashboard data
- `loadDashboardData()` - Fetches all dashboard data from service
- `refreshDashboard()` - Manually refreshes dashboard data
- `openApprovalModal($leaveId)` - Opens modal for leave review
- `closeApprovalModal()` - Closes the approval modal
- `quickApprove($leaveId)` - Approves leave without comments
- `approveWithComments()` - Approves leave with comments from modal
- `quickReject($leaveId)` - Rejects leave with default message
- `rejectWithComments()` - Rejects leave with custom comments

### 2. Blade View
**File:** `resources/views/livewire/dashboard/admin-dashboard.blade.php`

**Sections:**
1. **Header** - Dashboard title with refresh button and logout
2. **System Statistics Cards** (4 cards)
   - Total Users
   - Active Users
   - Total Departments
   - Total Projects
3. **Today's Attendance Statistics** (2 cards)
   - Today's Attendance (total check-ins)
   - Currently Clocked In (active users)
4. **Recent Activities** - Last 10 attendance activities with user names and actions
5. **Pending Leave Approvals** - All pending leaves with quick action buttons
6. **Department Statistics Table** - Performance metrics by department
7. **Quick Action Buttons** - Links to:
   - Manage Users
   - Departments
   - Projects
   - Reports
8. **Approval Modal** - Detailed leave review with comments

### 3. Routes
**File:** `routes/web.php`

**Added Routes:**
- `GET /admin/dashboard` - Admin dashboard (name: admin.dashboard)
- `GET /users` - Placeholder for user management (name: users.index)
- `GET /departments` - Placeholder for department management (name: departments.index)
- `GET /projects` - Placeholder for project management (name: projects.index)
- `GET /reports` - Placeholder for reports (name: reports.index)

## Data Structure

The dashboard uses data from `DashboardService::getAdminDashboard()` which returns:

```php
[
    'system_stats' => [
        'total_users' => int,
        'active_users' => int,
        'inactive_users' => int,
        'total_departments' => int,
        'total_projects' => int,
        'today_attendance' => int,
        'currently_clocked_in' => int,
    ],
    'recent_activities' => Collection [
        'id' => string,
        'user_name' => string,
        'action' => 'clocked_in' | 'clocked_out',
        'time' => datetime,
        'message' => string|null,
    ],
    'pending_approvals' => Collection<Leave>,
    'department_stats' => Collection [
        'id' => string,
        'name' => string,
        'active_users' => int,
        'total_hours_this_month' => int,
        'total_hours_formatted' => string (HH:MM),
    ],
]
```

## UI Components Used

The dashboard leverages existing UI components:
- `<x-ui.card>` - Card container
- `<x-ui.button>` - Button component
- `<x-ui.badge>` - Status badges
- `<x-ui.empty-state>` - Empty state messages

## Features

### System-wide Statistics
- Real-time count of total and active users
- Department and project counts
- Today's attendance metrics
- Currently clocked-in users

### Recent Activities
- Last 10 attendance activities
- Shows user name and action (clock in/out)
- Displays time relative to now
- Includes optional messages

### Pending Approvals
- All pending leave requests across the organization
- Quick approve/reject buttons
- Detailed review modal with comments
- Real-time updates after actions

### Department Statistics
- Monthly performance by department
- Active user count per department
- Total hours worked this month
- Average hours per user calculation

### Quick Actions
- Direct links to management pages
- Visual icons for each action
- Hover effects for better UX
- Organized in a grid layout

## Styling

The dashboard uses:
- Tailwind CSS for styling
- Consistent color scheme:
  - Blue: Users/Primary actions
  - Green: Active/Success states
  - Purple: Departments
  - Orange: Projects
  - Yellow: Pending/Warning states
  - Red: Reject/Danger actions
- Responsive grid layouts
- Card-based design pattern
- Hover effects and transitions

## Integration

The dashboard integrates with:
- `DashboardService` - Data fetching
- `LeaveService` - Leave approval/rejection
- Livewire - Real-time updates
- Alpine.js - Modal interactions

## Access Control

The admin dashboard should be protected by middleware to ensure only admin users can access it. This will be implemented in future tasks when role-based middleware is added to the routes.

## Future Enhancements

1. Add role-based middleware to route
2. Implement actual management pages (users, departments, projects, reports)
3. Add charts/graphs for visual statistics
4. Add date range filters for statistics
5. Add export functionality for reports
6. Add real-time notifications using WebSockets
7. Add search and filter capabilities

## Testing

To test the admin dashboard:
1. Ensure you're logged in as an admin user
2. Navigate to `/admin/dashboard`
3. Verify all statistics are displayed correctly
4. Test the refresh functionality
5. Test leave approval/rejection actions
6. Verify the approval modal works correctly
7. Check quick action links (they redirect with info messages)

## Requirements Satisfied

This implementation satisfies the following requirements from task 22:
- ✅ Create admin dashboard Livewire component
- ✅ Display system-wide statistics (total users, active users, departments, projects)
- ✅ Display recent activities
- ✅ Display pending approvals across all teams
- ✅ Display department-wise statistics
- ✅ Implement quick action buttons
