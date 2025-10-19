<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\UserLevel;
use Illuminate\Database\Seeder;

class DefaultRolePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Get all permissions
        $allPermissions = Permission::all();

        // Admin role - gets all permissions
        $adminRole = UserLevel::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->syncPermissions($allPermissions->pluck('id')->toArray());
        }

        // Supervisor role - gets specific permissions
        $supervisorRole = UserLevel::where('name', 'Supervisor')->first();
        if ($supervisorRole) {
            $supervisorPermissions = Permission::whereIn('slug', [
                // Leave management
                'leaves.view-all',
                'leaves.approve',
                'leaves.reject',
                'leaves.view-balance',
                
                // Attendance
                'attendance.view-all',
                
                // Reports
                'reports.view',
                'reports.export',
                'reports.individual',
                'reports.summary',
                'reports.timesheet',
                
                // Tasks
                'tasks.view-all',
                'tasks.create',
                'tasks.edit',
                
                // Notices
                'notices.view',
                
                // Holidays
                'holidays.view',
                
                // Notifications
                'notifications.leaves',
                'notifications.attendance',
            ])->pluck('id')->toArray();
            
            $supervisorRole->syncPermissions($supervisorPermissions);
        }

        // User role - gets basic permissions
        $userRole = UserLevel::where('name', 'User')->first();
        if ($userRole) {
            $userPermissions = Permission::whereIn('slug', [
                // Own data
                'leaves.view-own',
                'leaves.apply',
                'leaves.edit-own',
                'leaves.delete-own',
                'leaves.view-balance',
                'attendance.clock',
                'attendance.view-own',
                'tasks.view-own',
                
                // View only
                'notices.view',
                'holidays.view',
                'reports.view',
            ])->pluck('id')->toArray();
            
            $userRole->syncPermissions($userPermissions);
        }
    }
}
