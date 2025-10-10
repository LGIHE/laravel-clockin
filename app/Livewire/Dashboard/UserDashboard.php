<?php

namespace App\Livewire\Dashboard;

use App\Services\AttendanceService;
use App\Services\DashboardService;
use Livewire\Component;

class UserDashboard extends Component
{
    public $dashboardData;
    public $attendanceStatus;
    public $chartData;
    public $holidays;
    public $notices;
    public $clockMessage = '';
    public $selectedProject = '';
    public $selectedTask = '';
    public $taskStatus = 'completed';
    public $userProjects = [];
    public $userTasks = [];
    public $showPunchOutModal = false;
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
        // Initialize with safe defaults
        $this->dashboardData = [
            'attendance_status' => [
                'clocked_in' => false,
                'in_time' => null,
                'in_message' => null,
            ],
            'stats' => [
                'leave_this_year' => 0,
                'last_30_days_formatted' => '00:00:00',
            ],
            'work_duration' => '00:00:00',
            'recent_attendance' => collect([]),
        ];
        $this->attendanceStatus = $this->dashboardData['attendance_status'];
        $this->chartData = [];
        $this->holidays = collect([]);
        $this->notices = collect([]);
        
        $this->loadDashboardData();
        $this->loadUserProjects();
        $this->loadUserTasks();
    }

    public function loadUserProjects()
    {
        // Load all active projects instead of just assigned ones
        $this->userProjects = \App\Models\Project::where('status', 'ACTIVE')->get();
        
        // If user has only one project, select it by default
        if ($this->userProjects->count() === 1 && empty($this->selectedProject)) {
            $this->selectedProject = $this->userProjects->first()->id;
        }
    }

    public function loadUserTasks()
    {
        $this->userTasks = \App\Models\Task::where('user_id', auth()->id())
            ->whereIn('status', ['in-progress', 'on-hold'])
            ->get();
    }

    public function loadDashboardData()
    {
        try {
            $this->dashboardData = $this->dashboardService->getUserDashboard(auth()->id());
            $this->attendanceStatus = $this->dashboardData['attendance_status'] ?? [
                'clocked_in' => false,
                'in_time' => null,
                'in_message' => null,
            ];
            $this->chartData = $this->dashboardData['chart_data'] ?? [];
            $this->holidays = $this->dashboardData['holidays'] ?? collect([]);
            $this->notices = $this->dashboardData['notices'] ?? collect([]);
        } catch (\Exception $e) {
            // Set safe defaults on error
            $this->dashboardData = [
                'attendance_status' => [
                    'clocked_in' => false,
                    'in_time' => null,
                    'in_message' => null,
                ],
                'stats' => [
                    'leave_this_year' => 0,
                    'last_30_days_formatted' => '00:00:00',
                ],
                'work_duration' => '00:00:00',
                'recent_attendance' => collect([]),
            ];
            $this->attendanceStatus = $this->dashboardData['attendance_status'];
            $this->chartData = [];
            $this->holidays = collect([]);
            $this->notices = collect([]);
            
            $this->dispatch('toast', [
                'message' => 'Failed to load dashboard data: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function clockIn()
    {
        $this->validate([
            'selectedProject' => 'required|exists:projects,id',
            'selectedTask' => 'nullable|exists:tasks,id',
            'clockMessage' => 'nullable|string|max:500',
        ]);

        $this->isLoading = true;
        
        try {
            $this->attendanceService->clockIn(
                auth()->id(), 
                $this->clockMessage,
                $this->selectedProject,
                $this->selectedTask ?: null
            );
            
            $this->clockMessage = '';
            $this->selectedProject = '';
            $this->selectedTask = '';
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

    public function openPunchOutModal()
    {
        $this->showPunchOutModal = true;
    }

    public function closePunchOutModal()
    {
        $this->showPunchOutModal = false;
        $this->clockMessage = '';
        $this->taskStatus = 'completed';
    }

    public function confirmClockOut()
    {
        $this->validate([
            'clockMessage' => 'nullable|string|max:500',
            'taskStatus' => 'nullable|in:in-progress,on-hold,completed',
        ]);

        $this->isLoading = true;
        
        try {
            $this->attendanceService->clockOut(
                auth()->id(), 
                $this->clockMessage,
                $this->taskStatus
            );
            
            $this->clockMessage = '';
            $this->taskStatus = 'completed';
            $this->showPunchOutModal = false;
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
