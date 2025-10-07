<?php

namespace App\Livewire\Dashboard;

use App\Services\AttendanceService;
use App\Services\DashboardService;
use Livewire\Component;

class UserDashboard extends Component
{
    public $dashboardData;
    public $attendanceStatus;
    public $clockMessage = '';
    public $isLoading = false;

    protected AttendanceService $attendanceService;
    protected DashboardService $dashboardService;

    public function boot(AttendanceService $attendanceService, DashboardService $dashboardService)
    {
        $this->attendanceService = $attendanceService;
        $this->dashboardService = $dashboardService;
    }

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        try {
            $this->dashboardData = $this->dashboardService->getUserDashboard(auth()->id());
            $this->attendanceStatus = $this->dashboardData['attendance_status'];
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to load dashboard data: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function clockIn()
    {
        $this->isLoading = true;
        
        try {
            $this->attendanceService->clockIn(auth()->id(), $this->clockMessage);
            
            $this->clockMessage = '';
            $this->loadDashboardData();
            
            $this->dispatch('toast', [
                'message' => 'Clocked in successfully!',
                'variant' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function clockOut()
    {
        $this->isLoading = true;
        
        try {
            $this->attendanceService->clockOut(auth()->id(), $this->clockMessage);
            
            $this->clockMessage = '';
            $this->loadDashboardData();
            
            $this->dispatch('toast', [
                'message' => 'Clocked out successfully!',
                'variant' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function refreshDashboard()
    {
        $this->loadDashboardData();
        
        $this->dispatch('toast', [
            'message' => 'Dashboard refreshed',
            'variant' => 'info'
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.user-dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard']);
    }
}
