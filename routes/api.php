<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveCategoryController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public authentication routes with rate limiting
Route::middleware('throttle:login')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
});

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
});

// Protected authentication routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Attendance
    Route::prefix('attendance')->group(function () {
        Route::post('/clock-in', [AttendanceController::class, 'clockIn']);
        Route::post('/clock-out', [AttendanceController::class, 'clockOut']);
        Route::get('/status', [AttendanceController::class, 'status']);
        Route::get('/', [AttendanceController::class, 'index']);
        Route::get('/{id}', [AttendanceController::class, 'show']);

        // Admin only
        Route::middleware('role:admin')->group(function () {
            Route::post('/force-punch', [AttendanceController::class, 'forcePunch']);
            Route::put('/{id}', [AttendanceController::class, 'update']);
            Route::delete('/{id}', [AttendanceController::class, 'destroy']);
        });
    });

    // Leaves
    Route::prefix('leaves')->group(function () {
        Route::get('/', [LeaveController::class, 'index']);
        Route::post('/', [LeaveController::class, 'store']);
        Route::get('/balance', [LeaveController::class, 'balance']);
        Route::get('/{id}', [LeaveController::class, 'show']);
        Route::put('/{id}', [LeaveController::class, 'update']);
        Route::delete('/{id}', [LeaveController::class, 'destroy']);

        // Supervisor/Admin
        Route::middleware('role:supervisor,admin')->group(function () {
            Route::put('/{id}/approve', [LeaveController::class, 'approve']);
            Route::put('/{id}/reject', [LeaveController::class, 'reject']);
        });
    });

    // User Management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::put('/users/{id}/status', [UserController::class, 'updateStatus']);
        Route::put('/users/{id}/supervisor', [UserController::class, 'assignSupervisor']);
        Route::put('/users/{id}/projects', [UserController::class, 'assignProjects']);
        Route::put('/users/{id}/password', [UserController::class, 'changePassword']);
    });

    // Departments (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::get('/departments/{department}', [DepartmentController::class, 'show']);
        Route::put('/departments/{department}', [DepartmentController::class, 'update']);
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy']);
    });

    // Designations (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/designations', [DesignationController::class, 'index']);
        Route::post('/designations', [DesignationController::class, 'store']);
        Route::get('/designations/{designation}', [DesignationController::class, 'show']);
        Route::put('/designations/{designation}', [DesignationController::class, 'update']);
        Route::delete('/designations/{designation}', [DesignationController::class, 'destroy']);
    });

    // Projects (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index']);
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::get('/projects/{project}', [ProjectController::class, 'show']);
        Route::put('/projects/{project}', [ProjectController::class, 'update']);
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
        Route::get('/projects/{project}/users', [ProjectController::class, 'users']);
        Route::post('/projects/{project}/users', [ProjectController::class, 'assignUsers']);
        Route::delete('/projects/{project}/users/{user}', [ProjectController::class, 'removeUser']);
    });

    // Leave Categories (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/leave-categories', [LeaveCategoryController::class, 'index']);
        Route::post('/leave-categories', [LeaveCategoryController::class, 'store']);
        Route::get('/leave-categories/{leave_category}', [LeaveCategoryController::class, 'show']);
        Route::put('/leave-categories/{leave_category}', [LeaveCategoryController::class, 'update']);
        Route::delete('/leave-categories/{leave_category}', [LeaveCategoryController::class, 'destroy']);
    });

    // Holidays (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/holidays', [HolidayController::class, 'index']);
        Route::post('/holidays', [HolidayController::class, 'store']);
        Route::get('/holidays/{holiday}', [HolidayController::class, 'show']);
        Route::put('/holidays/{holiday}', [HolidayController::class, 'update']);
        Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy']);
    });

    // Notices (Read for all, CUD for admin only)
    Route::get('/notices', [NoticeController::class, 'index']);
    Route::get('/notices/{id}', [NoticeController::class, 'show']);
    Route::middleware('role:admin')->group(function () {
        Route::post('/notices', [NoticeController::class, 'store']);
        Route::put('/notices/{id}', [NoticeController::class, 'update']);
        Route::delete('/notices/{id}', [NoticeController::class, 'destroy']);
    });

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/individual', [ReportController::class, 'individual']);
        Route::get('/summary', [ReportController::class, 'summary']);
        Route::get('/timesheet', [ReportController::class, 'timesheet']);
        Route::get('/export', [ReportController::class, 'export']);
    });

    // Dashboard
    Route::get('/dashboard/user', [DashboardController::class, 'user']);
    Route::get('/dashboard/supervisor', [DashboardController::class, 'supervisor'])
        ->middleware('role:supervisor,admin');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin');
});
