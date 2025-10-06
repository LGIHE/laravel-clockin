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

    public $search = '';
    public $userId = '';
    public $status = '';
    public $categoryId = '';
    public $startDate = '';
    public $endDate = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $perPage = 15;
    
    public $selectedLeave = null;
    public $showDetailModal = false;
    public $showApprovalModal = false;
    public $approvalAction = ''; // 'approve' or 'reject'
    public $approvalComments = '';
    
    public $users = [];
    public $categories = [];
    public $statuses = [];
    public $currentUserRole = '';
    public $isAdmin = false;
    public $isSupervisor = false;

    protected LeaveService $leaveService;

    public function boot(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function mount()
    {
        $user = auth()->user();
        $this->currentUserRole = $user->userLevel->name;
        $this->isAdmin = $this->currentUserRole === 'admin';
        $this->isSupervisor = $this->currentUserRole === 'supervisor';
        
        // Set default date range to current month
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        
        // Load filter options
        $this->categories = LeaveCategory::orderBy('name')->get();
        $this->statuses = LeaveStatus::all();
        
        // Load users for filter (admin/supervisor only)
        if ($this->isAdmin) {
            $this->users = User::select('id', 'name', 'email')
                ->where('status', 1)
                ->orderBy('name')
                ->get();
        } elseif ($this->isSupervisor) {
            // Supervisors see their team members
            $this->users = User::select('id', 'name', 'email')
                ->where('supervisor_id', $user->id)
                ->where('status', 1)
                ->orderBy('name')
                ->get();
        } else {
            // Regular users can only see their own leaves
            $this->userId = auth()->id();
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingUserId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortOrder = 'asc';
        }
    }

    public function applyFilters()
    {
        $this->resetPage();
        
        $this->dispatch('toast', [
            'message' => 'Filters applied',
            'variant' => 'info'
        ]);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->userId = ($this->isAdmin || $this->isSupervisor) ? '' : auth()->id();
        $this->status = '';
        $this->categoryId = '';
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->sortBy = 'created_at';
        $this->sortOrder = 'desc';
        
        $this->resetPage();
        
        $this->dispatch('toast', [
            'message' => 'Filters cleared',
            'variant' => 'info'
        ]);
    }

    public function viewDetails($leaveId)
    {
        $this->selectedLeave = Leave::with(['user.userLevel', 'user.department', 'user.designation', 'category', 'status'])
            ->find($leaveId);
        
        if ($this->selectedLeave) {
            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedLeave = null;
    }

    public function openApprovalModal($leaveId, $action)
    {
        $this->selectedLeave = Leave::with(['user', 'category', 'status'])->find($leaveId);
        
        if ($this->selectedLeave) {
            $this->approvalAction = $action;
            $this->approvalComments = '';
            $this->showApprovalModal = true;
        }
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->selectedLeave = null;
        $this->approvalAction = '';
        $this->approvalComments = '';
    }

    public function submitApproval()
    {
        try {
            if ($this->approvalAction === 'approve') {
                $this->leaveService->approveLeave(
                    $this->selectedLeave->id,
                    auth()->id(),
                    $this->approvalComments
                );
                
                $this->dispatch('toast', [
                    'message' => 'Leave approved successfully!',
                    'variant' => 'success'
                ]);
            } elseif ($this->approvalAction === 'reject') {
                $this->leaveService->rejectLeave(
                    $this->selectedLeave->id,
                    auth()->id(),
                    $this->approvalComments
                );
                
                $this->dispatch('toast', [
                    'message' => 'Leave rejected successfully!',
                    'variant' => 'success'
                ]);
            }
            
            $this->closeApprovalModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function deleteLeave($leaveId)
    {
        try {
            $leave = Leave::find($leaveId);
            
            if (!$leave) {
                throw new \Exception('Leave not found');
            }
            
            // Check if user owns the leave
            if ($leave->user_id !== auth()->id()) {
                throw new \Exception('You are not authorized to delete this leave');
            }
            
            // Check if leave is pending
            $pendingStatus = LeaveStatus::where('name', 'pending')->first();
            if ($leave->leave_status_id !== $pendingStatus->id) {
                throw new \Exception('Only pending leaves can be deleted');
            }
            
            $leave->delete();
            
            $this->dispatch('toast', [
                'message' => 'Leave deleted successfully',
                'variant' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    protected $listeners = ['leave-applied' => '$refresh'];

    public function render()
    {
        $query = Leave::with(['user.userLevel', 'user.department', 'category', 'status']);

        // Apply role-based filtering
        $user = auth()->user();
        if ($this->currentUserRole === 'user') {
            // Regular users see only their leaves
            $query->where('user_id', $user->id);
        } elseif ($this->currentUserRole === 'supervisor') {
            // Supervisors see their team's leaves
            if (empty($this->userId)) {
                $teamUserIds = User::where('supervisor_id', $user->id)->pluck('id');
                $query->whereIn('user_id', $teamUserIds->push($user->id));
            } else {
                $query->where('user_id', $this->userId);
            }
        } else {
            // Admins see all leaves or filtered by user
            if (!empty($this->userId)) {
                $query->where('user_id', $this->userId);
            }
        }

        // Apply search filter (search by user name or email)
        if (!empty($this->search) && ($this->isAdmin || $this->isSupervisor)) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if (!empty($this->status)) {
            $statusRecord = LeaveStatus::where('name', $this->status)->first();
            if ($statusRecord) {
                $query->where('leave_status_id', $statusRecord->id);
            }
        }

        // Apply category filter
        if (!empty($this->categoryId)) {
            $query->where('leave_category_id', $this->categoryId);
        }

        // Apply date range filter
        if (!empty($this->startDate)) {
            $query->whereDate('date', '>=', $this->startDate);
        }

        if (!empty($this->endDate)) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortOrder);

        // Paginate results
        $leaves = $query->paginate($this->perPage);

        return view('livewire.leave.leave-list', [
            'leaves' => $leaves
        ]);
    }
}
