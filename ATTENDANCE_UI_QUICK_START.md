# Attendance Management UI - Quick Start Guide

## Overview

The Attendance Management UI provides a comprehensive interface for tracking and managing employee attendance records. This guide will help you get started quickly.

## Accessing the Attendance Page

1. Log in to the application
2. Navigate to `/attendance` in your browser
3. Or click "Attendance" in the navigation menu (if available)

## For Regular Users

### View Your Attendance Records

- The page automatically displays your attendance records
- Default view shows the current month
- Records are sorted by date (newest first)

### Clock In/Out

**Option 1: From Dashboard**
1. Go to your dashboard
2. Find the "Attendance Status" card
3. Enter an optional message
4. Click "Clock In" or "Clock Out"

**Option 2: From Attendance Page**
1. Go to `/attendance`
2. Use the clock in/out widget (if available)
3. Enter an optional message
4. Click the appropriate button

### Filter Your Records

1. Use the date range filters:
   - **Start Date:** Beginning of date range
   - **End Date:** End of date range
2. Click "Apply Filters"
3. Click "Clear Filters" to reset

### View Attendance Details

1. Find the record you want to view
2. Click the eye icon (👁️) in the Actions column
3. Review the detailed information:
   - Date and day of week
   - Clock in time and message
   - Clock out time and message
   - Total hours worked
4. Click "Close" to dismiss

## For Administrators

### View All Attendance Records

- The page displays all users' attendance records by default
- Use filters to narrow down the results

### Filter Records

**By User:**
1. Select a user from the "User" dropdown
2. Click "Apply Filters"

**By Search:**
1. Type a name or email in the "Search" field
2. Results update automatically

**By Date Range:**
1. Set "Start Date" and "End Date"
2. Click "Apply Filters"

**By Status:**
1. Select status from dropdown:
   - All Status
   - Clocked In (currently active)
   - Clocked Out (completed)
2. Click "Apply Filters"

**Adjust Records Per Page:**
1. Select from dropdown: 10, 15, 25, 50, or 100
2. Page updates automatically

### Sort Records

1. Click on column headers to sort:
   - **User** - Sort by user name
   - **Date** - Sort by attendance date
   - **Hours Worked** - Sort by total hours
2. Click again to reverse sort order
3. Arrow icon shows current sort direction

### Force Punch (Admin Only)

Use this feature to manually clock in/out a user:

1. Click "Force Punch" button in the header
2. Fill in the form:
   - **User:** Select the user
   - **Punch Type:** Choose "Clock In" or "Clock Out"
   - **Date and Time:** Select when the punch should occur
   - **Message:** Add an optional note (e.g., "Forgot to clock in")
3. Review the warning message
4. Click "Submit Force Punch"

**Important Notes:**
- Force punch will override existing records
- Use carefully and document the reason
- User must be clocked in before forcing clock out
- User must be clocked out before forcing clock in

### Delete Attendance Records

1. Find the record to delete
2. Click the trash icon (🗑️) in the Actions column
3. Confirm the deletion
4. Record is soft deleted (can be recovered from database)

**Warning:** Only delete records if absolutely necessary. Consider using force punch to correct errors instead.

## Understanding the Table

### Columns Explained

- **User** (Admin only): Employee name and email
- **Date**: Date of attendance with day of week
- **Clock In**: Time clocked in and optional message
- **Clock Out**: Time clocked out and optional message (shows "-" if still clocked in)
- **Hours Worked**: Total hours in HH:MM format (shows "In Progress" if still clocked in)
- **Status**: Badge showing "Completed" (green) or "In Progress" (yellow)
- **Actions**: View details and delete buttons

### Status Badges

- 🟢 **Completed** (Green): User has clocked out
- 🟡 **In Progress** (Yellow): User is currently clocked in

## Tips and Best Practices

### For Users

1. **Always add a message** when clocking in/out for better record keeping
2. **Check your status** before leaving to ensure you've clocked out
3. **Review your records regularly** to catch any errors early
4. **Use date filters** to view specific time periods

### For Administrators

1. **Use filters effectively** to find specific records quickly
2. **Document force punches** by adding detailed messages
3. **Review attendance patterns** to identify issues
4. **Export data regularly** for backup and reporting (when feature is available)
5. **Communicate with employees** before making corrections

## Common Scenarios

### Scenario 1: User Forgot to Clock In

**Solution (Admin):**
1. Click "Force Punch"
2. Select the user
3. Choose "Clock In"
4. Set the appropriate time
5. Add message: "Forgot to clock in - corrected by admin"
6. Submit

### Scenario 2: User Forgot to Clock Out

**Solution (Admin):**
1. Click "Force Punch"
2. Select the user
3. Choose "Clock Out"
4. Set the appropriate time
5. Add message: "Forgot to clock out - corrected by admin"
6. Submit

### Scenario 3: Incorrect Clock In/Out Time

**Solution (Admin):**
1. Delete the incorrect record
2. Use Force Punch to create correct record
3. Add message explaining the correction

### Scenario 4: View Last Month's Attendance

**Solution (User or Admin):**
1. Set Start Date to first day of last month
2. Set End Date to last day of last month
3. Click "Apply Filters"

### Scenario 5: Find All Active Clock-Ins

**Solution (Admin):**
1. Set Status filter to "Clocked In"
2. Click "Apply Filters"
3. Review list of currently clocked-in users

## Keyboard Shortcuts

- **Tab**: Navigate between form fields
- **Enter**: Submit forms (when focused on input)
- **Escape**: Close modals
- **Arrow Keys**: Navigate table rows (when focused)

## Mobile Usage

The interface is fully responsive:

- **Filters**: Collapse into a compact view
- **Table**: Scroll horizontally to view all columns
- **Buttons**: Touch-friendly size
- **Modals**: Full-screen on small devices

## Troubleshooting

### Problem: Can't see any records

**Solutions:**
- Check date range filters - expand the range
- Clear all filters and try again
- Verify you have attendance records for the selected period

### Problem: Clock in/out button not working

**Solutions:**
- Check your internet connection
- Refresh the page
- Verify you're logged in
- Check if you're already clocked in/out

### Problem: Force punch fails

**Solutions:**
- Verify the user is in the correct state (clocked in/out)
- Check the date and time are valid
- Ensure you have admin permissions
- Try refreshing the page

### Problem: Filters not applying

**Solutions:**
- Click "Apply Filters" button after changing values
- Try clearing filters and reapplying
- Refresh the page

## Getting Help

If you encounter issues:

1. Check this guide for solutions
2. Review the detailed implementation documentation
3. Contact your system administrator
4. Check application logs for errors

## Next Steps

After mastering the Attendance Management UI:

1. Explore the Dashboard for quick attendance overview
2. Learn about Leave Management (coming soon)
3. Review Reports for attendance analytics (coming soon)
4. Set up notifications for attendance events (coming soon)

## Summary

The Attendance Management UI provides:
- ✅ Easy clock in/out functionality
- ✅ Comprehensive filtering and sorting
- ✅ Detailed attendance records
- ✅ Admin force punch capability
- ✅ Mobile-responsive design
- ✅ Real-time updates
- ✅ Secure and role-based access

Start using it today to streamline your attendance tracking!
