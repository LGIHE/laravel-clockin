<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\FirstLoginPasswordChange;
use App\Livewire\AccountSetup;
use App\Livewire\Dashboard\UserDashboard;
use App\Livewire\Dashboard\SupervisorDashboard;
use App\Livewire\Dashboard\AdminDashboard;
use App\Livewire\Attendance\AttendanceList;
use App\Livewire\Attendance\UserAttendance;
use App\Livewire\Leave\LeaveList;
use App\Livewire\Users\UserList;
use App\Livewire\Users\UserForm;
use App\Livewire\Departments\DepartmentList;
use App\Livewire\Designations\DesignationList;
use App\Livewire\Projects\ProjectList;
use App\Livewire\LeaveCategories\LeaveCategoryList;
use App\Livewire\Holidays\HolidayList;
use App\Livewire\Notices\NoticeList;
use App\Livewire\Tasks\TaskList;
use App\Http\Controllers\SystemSettingsController;

// Guest routes (authentication)
Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('home');
    Route::get('/login', Login::class)->name('login');
    Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('reset-password');
    Route::get('/account-setup/{token}', AccountSetup::class)->name('account.setup');
});

// Password change route (requires authentication but bypasses password change check)
Route::middleware('auth')->group(function () {
    Route::get('/change-password', FirstLoginPasswordChange::class)->name('password.change.first-login');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/supervisor/dashboard', SupervisorDashboard::class)->name('supervisor.dashboard');
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
    
    // Attendance - User view (accessible to all authenticated users)
    Route::get('/attendance', UserAttendance::class)->name('attendance.user');
    
    // Tasks (accessible to all authenticated users)
    Route::get('/tasks', TaskList::class)->name('tasks.index');
    
    // Holiday Calendar (accessible to all authenticated users, but only admin can create/edit)
    Route::get('/holidays', HolidayList::class)->name('holidays.index');
    
    // Leave Management
    Route::get('/leaves', LeaveList::class)->name('leaves.index');
    Route::get('/leaves/apply', \App\Livewire\Leave\ApplyLeave::class)->name('leaves.apply');
    Route::get('/compensation-leaves', \App\Livewire\Leave\CompensationLeaveRequests::class)->name('compensation-leaves.index');
    
    // Attendance Management
    Route::middleware('permission:attendance.view-all')->group(function () {
        Route::get('/attendance/manage', AttendanceList::class)->name('attendance.index');
    });
    
    // User Management
    Route::middleware('permission:users.view')->group(function () {
        Route::get('/users', UserList::class)->name('users.index');
    });
    
    Route::middleware('permission:users.edit')->group(function () {
        Route::get('/users/{userId}/edit', UserForm::class)->name('users.edit');
    });
    
    // Department Management
    Route::middleware('permission:departments.view')->group(function () {
        Route::get('/departments', DepartmentList::class)->name('departments.index');
    });
    
    // Designation Management
    Route::middleware('permission:designations.view')->group(function () {
        Route::get('/designations', DesignationList::class)->name('designations.index');
    });
    
    // Project Management
    Route::middleware('permission:projects.view')->group(function () {
        Route::get('/projects', ProjectList::class)->name('projects.index');
    });
    
    // Leave Category Management
    Route::middleware('permission:leave-categories.view')->group(function () {
        Route::get('/leave-categories', LeaveCategoryList::class)->name('leave-categories.index');
    });
    
    // Role & Permission Management
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('/roles', \App\Livewire\Roles\RoleList::class)->name('roles.index');
        Route::get('/roles/create', \App\Livewire\Roles\RoleForm::class)->name('roles.create');
        Route::get('/roles/{roleId}/edit', \App\Livewire\Roles\RoleForm::class)->name('roles.edit');
    });
    
    // System Settings
    Route::middleware('permission:settings.view')->group(function () {
        Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/logs', [SystemSettingsController::class, 'logs'])->name('settings.logs');
        Route::get('/settings/stats', [SystemSettingsController::class, 'stats'])->name('settings.stats');
    });
    
    Route::middleware('permission:settings.manage')->group(function () {
        Route::put('/settings/general', [SystemSettingsController::class, 'updateGeneral'])->name('settings.update.general');
        Route::put('/settings/email', [SystemSettingsController::class, 'updateEmail'])->name('settings.update.email');
        Route::put('/settings/system', [SystemSettingsController::class, 'updateSystem'])->name('settings.update.system');
        Route::put('/settings/notification', [SystemSettingsController::class, 'updateNotification'])->name('settings.update.notification');
        Route::delete('/settings/logs', [SystemSettingsController::class, 'clearLogs'])->name('settings.logs.clear');
        Route::post('/settings/cache/clear', [SystemSettingsController::class, 'clearCache'])->name('settings.cache.clear');
    });
    
    // Notice Board (accessible to all authenticated users)
    Route::get('/notices', NoticeList::class)->name('notices.index');
    
    // Reports (accessible to all authenticated users)
    Route::get('/reports', \App\Livewire\Reports\ReportsIndex::class)->name('reports.index');
    Route::get('/reports/individual', \App\Livewire\Reports\IndividualReport::class)->name('reports.individual');
    Route::get('/reports/summary', \App\Livewire\Reports\SummaryReport::class)->name('reports.summary');
    Route::get('/reports/timesheet', \App\Livewire\Reports\TimesheetReport::class)->name('reports.timesheet');
    Route::get('/reports/export', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    
    // Profile page
    Route::get('/profile', \App\Livewire\ProfilePage::class)->name('profile');

    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});

// Utility routes (Admin only - for maintenance)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/clear-cache', function () {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        
        return response()->json([
            'success' => true,
            'message' => 'Application cache cleared successfully!',
            'details' => [
                'config' => 'Configuration cache cleared',
                'routes' => 'Route cache cleared',
                'views' => 'Compiled views cleared',
                'events' => 'Cached events cleared',
                'cache' => 'Application cache cleared',
            ]
        ]);
    })->name('cache.clear');
});
