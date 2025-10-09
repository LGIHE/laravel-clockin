<?php

namespace App\Livewire\Leave;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\LeaveStatus;
use App\Models\User;
use App\Services\LeaveService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LeaveList extends Component
{
    use WithPagination;

    // Filter properties
    public $searchTerm = '';
    public $categoryFilter = '';
    public $statusFilter = '';
    
    // Tab state
    public $activeTab = 'my-leaves';
    
    // Dialog states
    public $showDeleteDialog = false;
    public $showRejectDialog = false;
    public $deleteLeaveId = null;
    public $rejectLeaveId = null;
    public $rejectionReason = '';
    
    // User role flags
    public $isAdmin = false;
    public $isSupervisor = false;
    
    // Data
    public $categories = [];
    public $isLoading = false;
    
    // Leave balance
    public $approvedDays = 0;
    public $pendingDays = 0;
    public $pendingCount = 0;
    public $totalRequests = 0;

    protected LeaveService $leaveService;

    public function boot(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function mount()
    {
        $user = auth()->user();
        $this->isAdmin = $user->userLevel->name === 'admin';
        $this->isSupervisor = $user->userLevel->name === 'supervisor';
        
        // Load filter options
        $this->categories = LeaveCategory::orderBy('name')->get();
        
        // Calculate leave balance
        $this->calculateLeaveBalance();
    }

    public function calculateLeaveBalance()
    {
        $userId = auth()->id();
        // Use case-insensitive search and handle both 'approved' and 'granted'
        $approvedStatus = LeaveStatus::whereRaw('LOWER(name) IN (?, ?)', ['approved', 'granted'])->first();
        $pendingStatus = LeaveStatus::whereRaw('LOWER(name) = ?', ['pending'])->first();
        
        // Count approved leaves (assuming 1 day per leave for now)
        if ($approvedStatus) {
            $this->approvedDays = Leave::where('user_id', $userId)
                ->where('leave_status_id', $approvedStatus->id)
                ->count();
        } else {
            $this->approvedDays = 0;
        }
            
        // Count pending leaves
        if ($pendingStatus) {
            $this->pendingDays = Leave::where('user_id', $userId)
                ->where('leave_status_id', $pendingStatus->id)
                ->count();
        } else {
            $this->pendingDays = 0;
        }
            
        // Total requests
        $this->totalRequests = Leave::where('user_id', $userId)->count();
        
        // Pending approvals (for admin/supervisor)
        if ($this->isAdmin || $this->isSupervisor) {
            if ($pendingStatus) {
                $this->pendingCount = Leave::where('leave_status_id', $pendingStatus->id)->count();
            } else {
                $this->pendingCount = 0;
            }
        }
    }

    public function approveLeave($leaveId)
    {
        try {
            $this->leaveService->approveLeave(
                $leaveId,
                auth()->id(),
                ''
            );
            
            $this->dispatch('toast', [
                'message' => 'Leave approved successfully!',
                'variant' => 'success'
            ]);
            
            $this->calculateLeaveBalance();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openRejectDialog($leaveId)
    {
        $this->rejectLeaveId = $leaveId;
        $this->rejectionReason = '';
        $this->showRejectDialog = true;
    }

    public function closeRejectDialog()
    {
        $this->showRejectDialog = false;
        $this->rejectLeaveId = null;
        $this->rejectionReason = '';
    }

    public function confirmReject()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5'
        ]);

        try {
            $this->leaveService->rejectLeave(
                $this->rejectLeaveId,
                auth()->id(),
                $this->rejectionReason
            );
            
            $this->dispatch('toast', [
                'message' => 'Leave rejected successfully!',
                'variant' => 'success'
            ]);
            
            $this->closeRejectDialog();
            $this->calculateLeaveBalance();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openDeleteDialog($leaveId)
    {
        $this->deleteLeaveId = $leaveId;
        $this->showDeleteDialog = true;
    }

    public function closeDeleteDialog()
    {
        $this->showDeleteDialog = false;
        $this->deleteLeaveId = null;
    }

    public function confirmDelete()
    {
        try {
            $leave = Leave::find($this->deleteLeaveId);
            
            if (!$leave) {
                throw new \Exception('Leave not found');
            }
            
            // Check if user owns the leave
            if ($leave->user_id !== auth()->id()) {
                throw new \Exception('You are not authorized to delete this leave');
            }
            
            // Check if leave is pending (case-insensitive)
            $pendingStatus = LeaveStatus::whereRaw('LOWER(name) = ?', ['pending'])->first();
            if (!$pendingStatus) {
                throw new \Exception('Pending status not found in the system');
            }
            
            if ($leave->leave_status_id !== $pendingStatus->id) {
                throw new \Exception('Only pending leaves can be deleted');
            }
            
            $leave->delete();
            
            $this->dispatch('toast', [
                'message' => 'Leave deleted successfully',
                'variant' => 'success'
            ]);
            
            $this->closeDeleteDialog();
            $this->calculateLeaveBalance();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function getFilteredLeaves($forCurrentUser = false)
    {
        $query = Leave::with(['user', 'category', 'status']);

        // Filter by user
        if ($forCurrentUser) {
            $query->where('user_id', auth()->id());
        } elseif (!$this->isAdmin && !$this->isSupervisor) {
            $query->where('user_id', auth()->id());
        }

        // Apply category filter
        if (!empty($this->categoryFilter)) {
            $query->where('leave_category_id', $this->categoryFilter);
        }

        // Apply status filter
        if (!empty($this->statusFilter)) {
            $status = LeaveStatus::where('name', strtolower($this->statusFilter))->first();
            if ($status) {
                $query->where('leave_status_id', $status->id);
            }
        }

        // Apply search filter
        if (!empty($this->searchTerm)) {
            $query->where(function($q) {
                $q->where('description', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('user', function($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        $myLeaves = $this->getFilteredLeaves(true);
        $allLeaves = ($this->isAdmin || $this->isSupervisor) ? $this->getFilteredLeaves(false) : collect();

        return view('livewire.leave.leave-list', [
            'myLeaves' => $myLeaves,
            'allLeaves' => $allLeaves,
        ])->layout('components.layouts.app');
    }
}
