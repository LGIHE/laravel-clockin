# Leave Management UI Implementation

## Overview
This document describes the implementation of Task 24: Leave Management UI for the Laravel ClockIn application.

## Components Implemented

### 1. Livewire Components

#### ApplyLeave Component (`app/Livewire/Leave/ApplyLeave.php`)
- **Purpose**: Allows users to apply for leave
- **Features**:
  - Leave category selection with remaining balance display
  - Date picker (future dates only)
  - Optional description field (max 500 characters)
  - Real-time leave balance display for all categories
  - Visual progress bars showing used vs. remaining days
  - Form validation with error messages
  - Success/error toast notifications
  - Automatic form reset after submission

#### LeaveList Component (`app/Livewire/Leave/LeaveList.php`)
- **Purpose**: Display and manage leave requests
- **Features**:
  - Role-based filtering (User sees own leaves, Supervisor sees team leaves, Admin sees all)
  - Advanced filtering:
    - User filter (Admin/Supervisor only)
    - Search by name or email
    - Status filter (Pending, Approved, Rejected)
    - Category filter
    - Date range filter
    - Pagination with configurable items per page
  - Sortable columns (User, Leave Date, Applied On)
  - Leave detail modal with complete information
  - Approval/Rejection modal (Supervisor/Admin only)
  - Delete functionality for pending leaves (owner only)
  - Status badges with color coding
  - Responsive table design

### 2. Blade Views

#### Apply Leave View (`resources/views/livewire/leave/apply-leave.blade.php`)
- Leave balance summary cards with visual progress bars
- Form with category dropdown, date picker, and description textarea
- Character counter for description field
- Loading state during submission
- Responsive grid layout

#### Leave List View (`resources/views/livewire/leave/leave-list.blade.php`)
- Integrated apply leave form at the top
- Comprehensive filter panel
- Data table with sortable columns
- Action buttons (View, Approve, Reject, Delete)
- Detail modal showing:
  - User information (Admin/Supervisor view)
  - Leave information (date, category, status, description)
  - Timestamps (created, updated)
- Approval/Rejection modal with:
  - Leave summary
  - Comments field
  - Confirmation message
  - Color-coded actions (green for approve, red for reject)
- Empty state when no records found
- Pagination controls

### 3. API Enhancements

#### New Endpoint: Leave Balance
- **Route**: `GET /api/leaves/balance`
- **Purpose**: Retrieve leave balance for all categories
- **Parameters**:
  - `user_id` (optional): User ID to check balance for
  - `year` (optional): Year to check (defaults to current year)
- **Authorization**: Users can only see their own balance unless Admin/Supervisor
- **Response**: Array of balance objects with category, total, used, and remaining days

### 4. Routes

#### Web Routes
- `GET /leaves` - Leave management page (authenticated users)

#### API Routes
- `GET /api/leaves/balance` - Get leave balance

## Features Implemented

### Leave Application
✅ Create leave application Livewire component with form
✅ Leave category selection
✅ Date picker with future date validation
✅ Optional description field
✅ Display leave balance information
✅ Real-time validation
✅ Success/error notifications

### Leave List
✅ Create leave list Livewire component with pagination and filtering
✅ Role-based data access
✅ Search functionality (Admin/Supervisor)
✅ Status filter (Pending, Approved, Rejected)
✅ Category filter
✅ Date range filter
✅ Sortable columns
✅ Configurable pagination

### Leave Detail View
✅ Create leave detail view modal
✅ Display user information (role-based)
✅ Display leave information
✅ Display timestamps
✅ Responsive design

### Leave Approval/Rejection
✅ Implement leave approval/rejection UI (supervisor/admin)
✅ Approval modal with comments
✅ Rejection modal with comments
✅ Confirmation messages
✅ Color-coded actions
✅ Success notifications

### Leave Balance
✅ Display leave balance information
✅ Visual progress bars
✅ Category-wise breakdown
✅ Used vs. remaining days
✅ Percentage display

### Status Badges and Filters
✅ Add status badges with color coding:
  - Pending: Yellow/Warning
  - Approved: Green/Success
  - Rejected: Red/Danger
✅ Status filter dropdown
✅ Category filter dropdown

## Requirements Satisfied

- **4.1**: Leave application functionality ✅
- **4.2**: View leave requests ✅
- **4.3**: Pending leaves display (Supervisor/Admin) ✅
- **4.4**: Leave approval functionality ✅
- **4.5**: Leave rejection functionality ✅
- **14.3**: Pagination, sorting, and filtering ✅
- **14.4**: Form validation and error handling ✅

## User Experience

### For Regular Users
1. View leave balance at a glance
2. Apply for leave with simple form
3. View their own leave history
4. Filter and search their leaves
5. Delete pending leave requests
6. View detailed leave information

### For Supervisors
1. View team member leaves
2. Filter by team member
3. Approve or reject pending leaves
4. Add comments to approvals/rejections
5. View leave details for team members

### For Admins
1. View all leave requests across the organization
2. Filter by any user
3. Approve or reject any pending leave
4. Add comments to approvals/rejections
5. View complete leave details

## Technical Details

### Security
- Role-based access control enforced at component level
- Authorization checks in API endpoints
- CSRF protection on forms
- Input validation and sanitization

### Performance
- Pagination to limit data transfer
- Eager loading of relationships (user, category, status)
- Debounced search input
- Efficient database queries

### User Interface
- Responsive design (mobile, tablet, desktop)
- Consistent with existing UI components
- Tailwind CSS styling
- Alpine.js for modal interactions
- Loading states and animations
- Toast notifications for feedback

## Testing Recommendations

1. **Functional Testing**:
   - Apply for leave as regular user
   - View leave list with different filters
   - Approve/reject leaves as supervisor
   - Delete pending leaves
   - Check leave balance display

2. **Authorization Testing**:
   - Verify users can only see their own leaves
   - Verify supervisors can see team leaves
   - Verify admins can see all leaves
   - Verify approval/rejection permissions

3. **Validation Testing**:
   - Try to apply for past dates
   - Exceed leave category limits
   - Submit empty forms
   - Test character limits

4. **UI/UX Testing**:
   - Test on different screen sizes
   - Verify modal interactions
   - Check toast notifications
   - Test pagination and sorting

## Files Created/Modified

### Created Files
1. `app/Livewire/Leave/ApplyLeave.php`
2. `app/Livewire/Leave/LeaveList.php`
3. `resources/views/livewire/leave/apply-leave.blade.php`
4. `resources/views/livewire/leave/leave-list.blade.php`

### Modified Files
1. `routes/web.php` - Added leave management route
2. `routes/api.php` - Added leave balance endpoint
3. `app/Http/Controllers/LeaveController.php` - Added balance method

## Next Steps

To complete the leave management feature:
1. Add leave management link to navigation/sidebar
2. Add quick action links in dashboards
3. Consider adding email notifications for approvals/rejections
4. Add leave calendar view
5. Add bulk approval functionality
6. Add leave reports and analytics

## Conclusion

The Leave Management UI has been successfully implemented with all required features:
- ✅ Leave application form with balance display
- ✅ Leave list with pagination and filtering
- ✅ Leave detail view
- ✅ Approval/rejection UI for supervisors and admins
- ✅ Leave balance information
- ✅ Status badges and filters

The implementation follows Laravel best practices, maintains consistency with existing UI components, and provides a complete user experience for all user roles.
