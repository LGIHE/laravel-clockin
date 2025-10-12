<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
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
    
    // User Management (Admin only)
    Route::middleware('role:admin')->group(function () {
        // Attendance Management (Admin view)
        Route::get('/attendance/manage', AttendanceList::class)->name('attendance.index');
        
        Route::get('/users', UserList::class)->name('users.index');
        Route::get('/users/create', UserForm::class)->name('users.create');
        Route::get('/users/{userId}/edit', UserForm::class)->name('users.edit');
        
        // Department Management
        Route::get('/departments', DepartmentList::class)->name('departments.index');
        
        // Designation Management
        Route::get('/designations', DesignationList::class)->name('designations.index');
        
        // Project Management
        Route::get('/projects', ProjectList::class)->name('projects.index');
        
        // Leave Category Management
        Route::get('/leave-categories', LeaveCategoryList::class)->name('leave-categories.index');
        
        // System Settings
        Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings/general', [SystemSettingsController::class, 'updateGeneral'])->name('settings.update.general');
        Route::put('/settings/email', [SystemSettingsController::class, 'updateEmail'])->name('settings.update.email');
        Route::put('/settings/system', [SystemSettingsController::class, 'updateSystem'])->name('settings.update.system');
        Route::put('/settings/notification', [SystemSettingsController::class, 'updateNotification'])->name('settings.update.notification');
        Route::get('/settings/logs', [SystemSettingsController::class, 'logs'])->name('settings.logs');
        Route::delete('/settings/logs', [SystemSettingsController::class, 'clearLogs'])->name('settings.logs.clear');
        Route::get('/settings/stats', [SystemSettingsController::class, 'stats'])->name('settings.stats');
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
