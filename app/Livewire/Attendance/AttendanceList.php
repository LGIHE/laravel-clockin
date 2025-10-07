<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceList extends Component
{
    use WithPagination;

    public $search = '';
    public $userId = '';
    public $startDate = '';
    public $endDate = '';
    public $status = '';
    public $sortBy = 'in_time';
    public $sortOrder = 'desc';
    public $perPage = 15;
    
    public $selectedAttendance = null;
    public $showDetailModal = false;
    public $showForcePunchModal = false;
    
    public $users = [];
    public $isAdmin = false;

    protected AttendanceService $attendanceService;

    public function boot(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function mount()
    {
        // Check if user is authenticated and has admin role
        $user = auth()->user();
        $this->isAdmin = $user && $user->userLevel && $user->userLevel->name === 'admin';
        
        // Set default date range to current month
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        
        // Load users for filter (admin only)
        if ($this->isAdmin) {
            $this->users = User::select('id', 'name', 'email')
                ->where('status', 1)
                ->orderBy('name')
                ->get();
        } else {
            // Non-admin users can only see their own attendance
            $this->userId = auth()->id();
        }
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    public function updatedUserId()
    {
        $this->resetPage();
    }
    
    public function updatedStatus()
    {
        $this->resetPage();
    }
    
    public function updatedSearch()
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

    public function clearFilters()
    {
        $this->search = '';
        $this->userId = $this->isAdmin ? '' : auth()->id();
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->status = '';
        $this->sortBy = 'in_time';
        $this->sortOrder = 'desc';
        
        $this->resetPage();
        
        $this->dispatch('toast', [
            'message' => 'Filters cleared successfully',
            'variant' => 'success'
        ]);
    }

    public function viewDetails($attendanceId)
    {
        $this->selectedAttendance = Attendance::with(['user.userLevel', 'user.department', 'user.designation'])
            ->find($attendanceId);
        
        if ($this->selectedAttendance) {
            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedAttendance = null;
    }

    public function openForcePunchModal()
    {
        $this->showForcePunchModal = true;
    }

    public function closeForcePunchModal()
    {
        $this->showForcePunchModal = false;
    }

    protected $listeners = ['attendance-updated' => '$refresh', 'close-force-punch-modal' => 'closeForcePunchModal'];

    public function deleteAttendance($attendanceId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        try {
            $attendance = Attendance::find($attendanceId);
            
            if ($attendance) {
                $attendance->delete();
                
                $this->dispatch('toast', [
                    'message' => 'Attendance record deleted successfully',
                    'variant' => 'success'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to delete attendance record: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function render()
    {
        $query = Attendance::with(['user.userLevel', 'user.department', 'user.designation']);

        // Apply user filter - ensure we always filter for non-admin users
        if (!empty($this->userId)) {
            $query->where('user_id', $this->userId);
        } elseif (!$this->isAdmin && auth()->check()) {
            // Non-admin users should only see their own records
            $query->where('user_id', auth()->id());
        }

        // Apply search filter (search by user name or email)
        if (!empty($this->search) && $this->isAdmin) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Apply date range filter
        if (!empty($this->startDate)) {
            $query->whereDate('in_time', '>=', $this->startDate);
        }

        if (!empty($this->endDate)) {
            $query->whereDate('in_time', '<=', $this->endDate);
        }

        // Apply status filter
        if (!empty($this->status)) {
            if ($this->status === 'clocked_in') {
                $query->whereNull('out_time');
            } elseif ($this->status === 'clocked_out') {
                $query->whereNotNull('out_time');
            }
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortOrder);

        // Paginate results
        $attendances = $query->paginate($this->perPage);

        return view('livewire.attendance.attendance-list', [
            'attendances' => $attendances
        ])->layout('components.layouts.app', ['title' => 'Attendance Management']);
    }
}
