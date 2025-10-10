<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use App\Services\LeaveService;
use App\Services\AttendanceService;
use Livewire\Component;

class AdminDashboard extends Component
{
    public $dashboardData;
    public $systemStats;
    public $recentActivities;
    public $pendingApprovals;
    public $departmentStats;
    public $monthlyAttendance;
    public $holidays;
    public $notices;
    public $isLoading = false;
    
    // For clock in/out
    public $attendanceStatus;
    public $clockMessage = '';
    public $selectedProject = '';
    public $selectedTask = '';
    public $taskStatus = 'completed';
    public $userProjects = [];
    public $userTasks = [];
    public $showPunchOutModal = false;
    
    // For quick approval modal
    public $selectedLeave = null;
    public $approvalComments = '';
    public $showApprovalModal = false;

    protected DashboardService $dashboardService;
    protected LeaveService $leaveService;
    protected AttendanceService $attendanceService;

    public function boot(
        DashboardService $dashboardService, 
        LeaveService $leaveService,
        AttendanceService $attendanceService
    ) {
        $this->dashboardService = $dashboardService;
        $this->leaveService = $leaveService;
        $this->attendanceService = $attendanceService;
    }

    public function mount()
    {
        $this->loadDashboardData();
        $this->loadAttendanceStatus();
        $this->loadUserProjects();
        $this->loadUserTasks();
    }
    
    public function loadUserProjects()
    {
        // Load all active projects instead of just assigned ones
        $this->userProjects = \App\Models\Project::where('status', 'ACTIVE')->get();
        
        \Log::info('AdminDashboard loadUserProjects', [
            'user_id' => auth()->id(),
            'projects_count' => $this->userProjects->count(),
            'projects' => $this->userProjects->pluck('name', 'id')->toArray()
        ]);
        
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
    
    public function loadAttendanceStatus()
    {
        try {
            $this->attendanceStatus = $this->attendanceService->getCurrentStatus(auth()->id());
        } catch (\Exception $e) {
            \Log::error('Failed to load attendance status: ' . $e->getMessage());
        }
    }

    public function loadDashboardData()
    {
        try {
            $this->dashboardData = $this->dashboardService->getAdminDashboard();
            $this->systemStats = $this->dashboardData['system_stats'] ?? [];
            $this->recentActivities = $this->dashboardData['recent_activities'] ?? collect([]);
            $this->pendingApprovals = $this->dashboardData['pending_approvals'] ?? collect([]);
            $this->departmentStats = $this->dashboardData['department_stats'] ?? collect([]);
            $this->monthlyAttendance = $this->dashboardData['monthly_attendance'] ?? [];
            $this->holidays = $this->dashboardData['holidays'] ?? collect([]);
            $this->notices = $this->dashboardData['notices'] ?? collect([]);
            
            // Debug logging
            \Log::info('Dashboard Data Loaded', [
                'systemStats' => $this->systemStats,
                'recentActivitiesCount' => is_countable($this->recentActivities) ? count($this->recentActivities) : 0,
                'holidaysCount' => is_countable($this->holidays) ? count($this->holidays) : 0,
            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard Load Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('toast', [
                'message' => 'Failed to load dashboard data: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function refreshDashboard()
    {
        $this->loadDashboardData();
        $this->loadAttendanceStatus();
        
        $this->dispatch('toast', [
            'message' => 'Dashboard refreshed',
            'variant' => 'info'
        ]);
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
            $this->loadAttendanceStatus();
            $this->loadDashboardData(); // Refresh recent activities
            
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
        \Log::info('AdminDashboard openPunchOutModal called', [
            'user_id' => auth()->id(),
            'showPunchOutModal_before' => $this->showPunchOutModal,
            'attendance_status' => $this->attendanceStatus
        ]);
        
        $this->showPunchOutModal = true;
        
        \Log::info('AdminDashboard openPunchOutModal after setting', [
            'showPunchOutModal_after' => $this->showPunchOutModal
        ]);
    }

    public function closePunchOutModal()
    {
        $this->showPunchOutModal = false;
        $this->clockMessage = '';
        $this->taskStatus = 'completed';
    }

    public function confirmClockOut()
    {
        \Log::info('AdminDashboard confirmClockOut called', [
            'user_id' => auth()->id(),
            'clockMessage' => $this->clockMessage,
            'taskStatus' => $this->taskStatus
        ]);
        
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
            $this->loadAttendanceStatus();
            $this->loadDashboardData(); // Refresh recent activities
            
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

    public function openApprovalModal($leaveId)
    {
        $this->selectedLeave = $this->pendingApprovals->firstWhere('id', $leaveId);
        $this->approvalComments = '';
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->selectedLeave = null;
        $this->approvalComments = '';
        $this->showApprovalModal = false;
    }

    public function quickApprove($leaveId)
    {
        $this->isLoading = true;
        
        try {
            $this->leaveService->approveLeave($leaveId, auth()->id(), null);
            
            $this->loadDashboardData();
            
            $this->dispatch('toast', [
                'message' => 'Leave approved successfully!',
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

    public function approveWithComments()
    {
        $this->isLoading = true;
        
        try {
            if (!$this->selectedLeave) {
                throw new \Exception('No leave selected');
            }

            $this->leaveService->approveLeave(
                $this->selectedLeave->id, 
                auth()->id(), 
                $this->approvalComments ?: null
            );
            
            $this->closeApprovalModal();
            $this->loadDashboardData();
            
            $this->dispatch('toast', [
                'message' => 'Leave approved successfully!',
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

    public function quickReject($leaveId)
    {
        $this->isLoading = true;
        
        try {
            $this->leaveService->rejectLeave($leaveId, auth()->id(), 'Rejected by admin');
            
            $this->loadDashboardData();
            
            $this->dispatch('toast', [
                'message' => 'Leave rejected',
                'variant' => 'info'
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

    public function rejectWithComments()
    {
        $this->isLoading = true;
        
        try {
            if (!$this->selectedLeave) {
                throw new \Exception('No leave selected');
            }

            if (empty(trim($this->approvalComments))) {
                throw new \Exception('Please provide a reason for rejection');
            }

            $this->leaveService->rejectLeave(
                $this->selectedLeave->id, 
                auth()->id(), 
                $this->approvalComments
            );
            
            $this->closeApprovalModal();
            $this->loadDashboardData();
            
            $this->dispatch('toast', [
                'message' => 'Leave rejected',
                'variant' => 'info'
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

    public function render()
    {
        return view('livewire.dashboard.admin-dashboard')
            ->layout('components.layouts.app', ['title' => 'Admin Dashboard']);
    }
}
