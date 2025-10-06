<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Dashboard\UserDashboard;
use App\Livewire\Dashboard\SupervisorDashboard;
use App\Livewire\Dashboard\AdminDashboard;
use App\Livewire\Attendance\AttendanceList;
use App\Livewire\Leave\LeaveList;
use App\Livewire\Users\UserList;
use App\Livewire\Users\UserForm;
use App\Livewire\Departments\DepartmentList;
use App\Livewire\Designations\DesignationList;
use App\Livewire\Projects\ProjectList;
use App\Livewire\LeaveCategories\LeaveCategoryList;
use App\Livewire\Holidays\HolidayList;
use App\Livewire\Notices\NoticeList;

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
    
    // Attendance Management
    Route::get('/attendance', AttendanceList::class)->name('attendance.index');
    
    // Leave Management
    Route::get('/leaves', LeaveList::class)->name('leaves.index');
    
    // User Management (Admin only)
    Route::middleware('role:admin')->group(function () {
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
        
        // Holiday Management
        Route::get('/holidays', HolidayList::class)->name('holidays.index');
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
