<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Dashboard\UserDashboard;
use App\Livewire\Dashboard\SupervisorDashboard;
use App\Livewire\Dashboard\AdminDashboard;

// Guest routes (authentication)
Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login');
    Route::get('/login', Login::class)->name('login');
    Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('reset-password');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/supervisor/dashboard', SupervisorDashboard::class)->name('supervisor.dashboard');
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
    
    // Placeholder routes for quick actions (to be implemented in future tasks)
    Route::get('/users', function () {
        return redirect()->route('admin.dashboard')->with('info', 'User management UI coming soon');
    })->name('users.index');
    
    Route::get('/departments', function () {
        return redirect()->route('admin.dashboard')->with('info', 'Department management UI coming soon');
    })->name('departments.index');
    
    Route::get('/projects', function () {
        return redirect()->route('admin.dashboard')->with('info', 'Project management UI coming soon');
    })->name('projects.index');
    
    Route::get('/reports', function () {
        return redirect()->route('admin.dashboard')->with('info', 'Reports UI coming soon');
    })->name('reports.index');
    
    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
