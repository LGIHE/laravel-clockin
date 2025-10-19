<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'users.view', 'category' => 'users', 'description' => 'View user list and details'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'category' => 'users', 'description' => 'Create new users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'category' => 'users', 'description' => 'Edit existing users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'category' => 'users', 'description' => 'Delete users'],
            ['name' => 'Archive Users', 'slug' => 'users.archive', 'category' => 'users', 'description' => 'Archive/unarchive users'],
            ['name' => 'Change User Status', 'slug' => 'users.status', 'category' => 'users', 'description' => 'Activate/deactivate users'],
            ['name' => 'Assign Supervisors', 'slug' => 'users.assign-supervisor', 'category' => 'users', 'description' => 'Assign supervisors to users'],
            ['name' => 'Assign Projects', 'slug' => 'users.assign-projects', 'category' => 'users', 'description' => 'Assign projects to users'],
            ['name' => 'Reset User Password', 'slug' => 'users.reset-password', 'category' => 'users', 'description' => 'Reset user passwords'],

            // Leave Management
            ['name' => 'View All Leaves', 'slug' => 'leaves.view-all', 'category' => 'leaves', 'description' => 'View all leave requests'],
            ['name' => 'View Own Leaves', 'slug' => 'leaves.view-own', 'category' => 'leaves', 'description' => 'View own leave requests'],
            ['name' => 'Apply Leave', 'slug' => 'leaves.apply', 'category' => 'leaves', 'description' => 'Apply for leave'],
            ['name' => 'Edit Own Leave', 'slug' => 'leaves.edit-own', 'category' => 'leaves', 'description' => 'Edit own pending leave requests'],
            ['name' => 'Delete Own Leave', 'slug' => 'leaves.delete-own', 'category' => 'leaves', 'description' => 'Delete own pending leave requests'],
            ['name' => 'Approve Leaves', 'slug' => 'leaves.approve', 'category' => 'leaves', 'description' => 'Approve leave requests'],
            ['name' => 'Reject Leaves', 'slug' => 'leaves.reject', 'category' => 'leaves', 'description' => 'Reject leave requests'],
            ['name' => 'View Leave Balance', 'slug' => 'leaves.view-balance', 'category' => 'leaves', 'description' => 'View leave balance'],

            // Leave Categories
            ['name' => 'View Leave Categories', 'slug' => 'leave-categories.view', 'category' => 'leave-categories', 'description' => 'View leave categories'],
            ['name' => 'Manage Leave Categories', 'slug' => 'leave-categories.manage', 'category' => 'leave-categories', 'description' => 'Create, edit, delete leave categories'],

            // Attendance Management
            ['name' => 'Clock In/Out', 'slug' => 'attendance.clock', 'category' => 'attendance', 'description' => 'Clock in and out'],
            ['name' => 'View Own Attendance', 'slug' => 'attendance.view-own', 'category' => 'attendance', 'description' => 'View own attendance records'],
            ['name' => 'View All Attendance', 'slug' => 'attendance.view-all', 'category' => 'attendance', 'description' => 'View all attendance records'],
            ['name' => 'Edit Attendance', 'slug' => 'attendance.edit', 'category' => 'attendance', 'description' => 'Edit attendance records'],
            ['name' => 'Delete Attendance', 'slug' => 'attendance.delete', 'category' => 'attendance', 'description' => 'Delete attendance records'],
            ['name' => 'Force Punch', 'slug' => 'attendance.force-punch', 'category' => 'attendance', 'description' => 'Force clock in/out for users'],

            // Department Management
            ['name' => 'View Departments', 'slug' => 'departments.view', 'category' => 'departments', 'description' => 'View departments'],
            ['name' => 'Manage Departments', 'slug' => 'departments.manage', 'category' => 'departments', 'description' => 'Create, edit, delete departments'],

            // Designation Management
            ['name' => 'View Designations', 'slug' => 'designations.view', 'category' => 'designations', 'description' => 'View designations'],
            ['name' => 'Manage Designations', 'slug' => 'designations.manage', 'category' => 'designations', 'description' => 'Create, edit, delete designations'],

            // Project Management
            ['name' => 'View Projects', 'slug' => 'projects.view', 'category' => 'projects', 'description' => 'View projects'],
            ['name' => 'Manage Projects', 'slug' => 'projects.manage', 'category' => 'projects', 'description' => 'Create, edit, delete projects'],

            // Task Management
            ['name' => 'View Own Tasks', 'slug' => 'tasks.view-own', 'category' => 'tasks', 'description' => 'View own tasks'],
            ['name' => 'View All Tasks', 'slug' => 'tasks.view-all', 'category' => 'tasks', 'description' => 'View all tasks'],
            ['name' => 'Create Tasks', 'slug' => 'tasks.create', 'category' => 'tasks', 'description' => 'Create new tasks'],
            ['name' => 'Edit Tasks', 'slug' => 'tasks.edit', 'category' => 'tasks', 'description' => 'Edit tasks'],
            ['name' => 'Delete Tasks', 'slug' => 'tasks.delete', 'category' => 'tasks', 'description' => 'Delete tasks'],

            // Holiday Management
            ['name' => 'View Holidays', 'slug' => 'holidays.view', 'category' => 'holidays', 'description' => 'View holiday calendar'],
            ['name' => 'Manage Holidays', 'slug' => 'holidays.manage', 'category' => 'holidays', 'description' => 'Create, edit, delete holidays'],

            // Notice Management
            ['name' => 'View Notices', 'slug' => 'notices.view', 'category' => 'notices', 'description' => 'View notices'],
            ['name' => 'Create Notices', 'slug' => 'notices.create', 'category' => 'notices', 'description' => 'Create new notices'],
            ['name' => 'Edit Notices', 'slug' => 'notices.edit', 'category' => 'notices', 'description' => 'Edit notices'],
            ['name' => 'Delete Notices', 'slug' => 'notices.delete', 'category' => 'notices', 'description' => 'Delete notices'],

            // Reports
            ['name' => 'View Reports', 'slug' => 'reports.view', 'category' => 'reports', 'description' => 'View reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'category' => 'reports', 'description' => 'Export reports'],
            ['name' => 'View Individual Reports', 'slug' => 'reports.individual', 'category' => 'reports', 'description' => 'View individual user reports'],
            ['name' => 'View Summary Reports', 'slug' => 'reports.summary', 'category' => 'reports', 'description' => 'View summary reports'],
            ['name' => 'View Timesheet Reports', 'slug' => 'reports.timesheet', 'category' => 'reports', 'description' => 'View timesheet reports'],

            // System Settings
            ['name' => 'View Settings', 'slug' => 'settings.view', 'category' => 'settings', 'description' => 'View system settings'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'category' => 'settings', 'description' => 'Manage system settings'],
            ['name' => 'View Logs', 'slug' => 'settings.logs', 'category' => 'settings', 'description' => 'View system logs'],
            ['name' => 'Clear Cache', 'slug' => 'settings.cache', 'category' => 'settings', 'description' => 'Clear system cache'],

            // Role & Permission Management
            ['name' => 'View Roles', 'slug' => 'roles.view', 'category' => 'roles', 'description' => 'View user roles/types'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'category' => 'roles', 'description' => 'Create, edit, delete user roles/types'],
            ['name' => 'Assign Permissions', 'slug' => 'roles.assign-permissions', 'category' => 'roles', 'description' => 'Assign permissions to roles'],

            // Notifications
            ['name' => 'Receive Leave Notifications', 'slug' => 'notifications.leaves', 'category' => 'notifications', 'description' => 'Receive notifications about leave requests'],
            ['name' => 'Receive Attendance Notifications', 'slug' => 'notifications.attendance', 'category' => 'notifications', 'description' => 'Receive notifications about attendance'],
            ['name' => 'Receive System Notifications', 'slug' => 'notifications.system', 'category' => 'notifications', 'description' => 'Receive system notifications'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
