<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use App\Services\LeaveService;
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
    
    // For quick approval modal
    public $selectedLeave = null;
    public $approvalComments = '';
    public $showApprovalModal = false;

    protected DashboardService $dashboardService;
    protected LeaveService $leaveService;

    public function boot(DashboardService $dashboardService, LeaveService $leaveService)
    {
        $this->dashboardService = $dashboardService;
        $this->leaveService = $leaveService;
    }

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        try {
            $this->dashboardData = $this->dashboardService->getAdminDashboard();
            $this->systemStats = $this->dashboardData['system_stats'];
            $this->recentActivities = $this->dashboardData['recent_activities'];
            $this->pendingApprovals = $this->dashboardData['pending_approvals'];
            $this->departmentStats = $this->dashboardData['department_stats'];
            $this->monthlyAttendance = $this->dashboardData['monthly_attendance'];
            $this->holidays = $this->dashboardData['holidays'];
            $this->notices = $this->dashboardData['notices'];
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to load dashboard data: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
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
