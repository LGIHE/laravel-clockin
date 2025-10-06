# Navigation and Layout Implementation

## Overview

This document describes the implementation of the navigation and layout system for the ClockIn Laravel application, including role-based menu items, responsive mobile navigation, breadcrumbs, and active menu highlighting.

## Components Implemented

### 1. Main Application Layout (`resources/views/components/layouts/app.blade.php`)

The main layout component provides the overall structure for authenticated pages:

**Features:**
- Responsive sidebar with mobile overlay
- Sticky header with user profile dropdown
- Breadcrumb navigation support
- Toast notification system
- Automatic guest/auth layout switching
- Alpine.js state management for sidebar toggle

**Usage:**
```blade
<x-layouts.app title="Page Title" :breadcrumbs="[...]">
    <!-- Page content -->
</x-layouts.app>
```

**Props:**
- `title` - Page title (displayed in browser tab)
- `breadcrumbs` - Array of breadcrumb items (optional)

### 2. Sidebar Navigation (`resources/views/components/layout/sidebar.blade.php`)

Role-based sidebar navigation with active menu highlighting:

**Features:**
- Dynamic menu items based on user role (USER, SUPERVISOR, ADMIN)
- Active route highlighting with blue background
- User info display with avatar
- Collapsible on mobile devices
- Logout button at bottom
- SVG icons for each menu item

**Menu Structure:**

**All Users:**
- Dashboard (role-specific route)
- Attendance
- Leaves
- Reports
- Notices

**Admin Only:**
- Users
- Departments
- Designations
- Projects
- Leave Categories
- Holidays

**Active State:**
- Current route is highlighted with `bg-blue-50 text-blue-700`
- Icon color changes to match active state

### 3. Header Component (`resources/views/components/layout/header.blade.php`)

Sticky header with user profile dropdown and notifications:

**Features:**
- Mobile menu toggle button
- Page title display
- Notification dropdown (Livewire component)
- User profile dropdown with:
  - User name and email
  - Role and department info
  - Quick links to Dashboard, Attendance, Leaves
  - Logout button
- Responsive design (hides some elements on mobile)

**User Profile Dropdown:**
- Avatar with user initial
- User information display
- Quick navigation links
- Logout functionality

### 4. Breadcrumb Navigation (`resources/views/components/layout/breadcrumbs.blade.php`)

Breadcrumb trail for page navigation:

**Features:**
- Home icon linking to role-specific dashboard
- Separator icons between items
- Current page highlighted
- Clickable intermediate items
- Responsive design

**Usage:**
```blade
<x-layouts.app 
    title="Edit User" 
    :breadcrumbs="[
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => 'Edit User']
    ]"
>
    <!-- Content -->
</x-layouts.app>
```

## Role-Based Access Control

### Navigation Items by Role

**USER Role:**
- Dashboard → `dashboard`
- Attendance → `attendance.index`
- Leaves → `leaves.index`
- Reports → `reports.index`
- Notices → `notices.index`

**SUPERVISOR Role:**
- Dashboard → `supervisor.dashboard`
- Attendance → `attendance.index`
- Leaves → `leaves.index`
- Reports → `reports.index`
- Notices → `notices.index`

**ADMIN Role:**
- Dashboard → `admin.dashboard`
- Attendance → `attendance.index`
- Leaves → `leaves.index`
- Reports → `reports.index`
- Notices → `notices.index`
- **Divider**
- Users → `users.index`
- Departments → `departments.index`
- Designations → `designations.index`
- Projects → `projects.index`
- Leave Categories → `leave-categories.index`
- Holidays → `holidays.index`

## Responsive Design

### Desktop (lg and above)
- Sidebar always visible (w-64)
- Full navigation labels shown
- User info displayed in sidebar
- Header shows full user profile

### Mobile (below lg)
- Sidebar hidden by default
- Hamburger menu button in header
- Sidebar slides in from left with overlay
- Close button in sidebar
- Simplified header layout

### Breakpoints
- Mobile: < 1024px (lg)
- Desktop: ≥ 1024px (lg)

## Active Menu Highlighting

The sidebar automatically highlights the active menu item based on the current route:

```php
$isActive = $currentRoute === $item['route'] || 
            (isset($item['activeRoutes']) && in_array($currentRoute, $item['activeRoutes']));
```

**Active Styles:**
- Background: `bg-blue-50`
- Text: `text-blue-700`
- Icon: `text-blue-700`

**Inactive Styles:**
- Background: `transparent` (hover: `bg-gray-50`)
- Text: `text-gray-700` (hover: `text-gray-900`)
- Icon: `text-gray-400`

## State Management

### Alpine.js State
The layout uses Alpine.js for client-side state management:

```javascript
x-data="{ 
    sidebarOpen: false,      // Mobile sidebar visibility
    sidebarCollapsed: false  // Desktop sidebar collapse (future feature)
}"
```

### Events
- `@toggle-sidebar` - Toggles mobile sidebar visibility
- `@toggle-collapse` - Toggles desktop sidebar collapse (future feature)

## Integration with Existing Pages

### Dashboard Views
All dashboard views have been updated to use the new layout:

**User Dashboard:**
```blade
<x-layouts.app title="Dashboard">
    <!-- Dashboard content -->
</x-layouts.app>
```

**Supervisor Dashboard:**
```blade
<x-layouts.app title="Supervisor Dashboard">
    <!-- Dashboard content -->
</x-layouts.app>
```

**Admin Dashboard:**
```blade
<x-layouts.app title="Admin Dashboard">
    <!-- Dashboard content -->
</x-layouts.app>
```

### Other Pages
To integrate the new layout into other pages:

1. Replace the outer `<div>` wrapper with `<x-layouts.app>`
2. Add the `title` prop
3. Optionally add `breadcrumbs` prop
4. Remove any duplicate headers or navigation

**Before:**
```blade
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow">
        <h1>Page Title</h1>
    </div>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Content -->
    </div>
</div>
```

**After:**
```blade
<x-layouts.app title="Page Title">
    <!-- Content -->
</x-layouts.app>
```

## Styling

### Tailwind CSS Classes
The navigation and layout use Tailwind CSS for styling:

- **Colors:** Blue (primary), Gray (neutral), Green (success), Red (danger)
- **Spacing:** Consistent padding and margins
- **Typography:** Inter font family
- **Transitions:** Smooth animations for hover and state changes

### Custom Styles
No custom CSS required - all styling is done with Tailwind utility classes.

## Accessibility

### ARIA Labels
- Screen reader text for icons: `<span class="sr-only">Label</span>`
- Proper semantic HTML structure
- Keyboard navigation support

### Focus States
- Visible focus rings on interactive elements
- `focus:outline-none focus:ring-2 focus:ring-blue-500`

## Browser Compatibility

The navigation and layout are compatible with:
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance Considerations

- **Lazy Loading:** Sidebar content loaded only when authenticated
- **Minimal JavaScript:** Alpine.js for lightweight interactivity
- **CSS Optimization:** Tailwind CSS purges unused styles in production
- **No External Dependencies:** All icons are inline SVG

## Future Enhancements

Potential improvements for future iterations:

1. **Desktop Sidebar Collapse:** Implement full collapse functionality for desktop
2. **Keyboard Shortcuts:** Add keyboard shortcuts for navigation
3. **Search:** Add global search in header
4. **Notifications Badge:** Show unread count on notification icon
5. **Theme Switcher:** Add dark mode support
6. **Customizable Menu:** Allow users to pin/unpin menu items
7. **Recent Pages:** Show recently visited pages in user dropdown

## Testing Checklist

- [x] Sidebar displays correct menu items for each role
- [x] Active menu item is highlighted correctly
- [x] Mobile sidebar opens and closes properly
- [x] User profile dropdown works correctly
- [x] Breadcrumbs display correctly
- [x] Logout functionality works
- [x] Responsive design works on all screen sizes
- [x] Navigation links use wire:navigate for SPA-like experience
- [x] All dashboard views use new layout
- [x] No console errors or warnings

## Troubleshooting

### Sidebar Not Showing
- Check if user is authenticated
- Verify `@auth` directive in layout
- Check Alpine.js is loaded

### Active Menu Not Highlighting
- Verify route names match exactly
- Check `request()->route()->getName()` returns correct value
- Ensure route is defined in `web.php`

### Mobile Menu Not Working
- Verify Alpine.js is loaded
- Check `@click` event handlers
- Ensure `x-data` is on parent element

### User Role Not Displaying Correctly
- Check `User` model has `getRoleAttribute()` accessor
- Verify `userLevel` relationship is loaded
- Check database has correct user_level_id

## Conclusion

The navigation and layout system provides a solid foundation for the ClockIn application with:
- Clean, modern design
- Role-based access control
- Responsive mobile support
- Active menu highlighting
- Easy integration with existing pages
- Accessibility compliance
- Performance optimization

All requirements from task 33 have been successfully implemented.
