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
    public $showEditModal = false;
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
        $this->isAdmin = $user && $user->userLevel && strtolower($user->userLevel->name) === 'admin';
        
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

    public function editAttendance($attendanceId)
    {
        $this->selectedAttendance = Attendance::with(['user.userLevel', 'user.department', 'user.designation'])
            ->find($attendanceId);
        
        if ($this->selectedAttendance) {
            // Check if user has permission to edit
            if (!$this->isAdmin && $this->selectedAttendance->user_id !== auth()->id()) {
                $this->dispatch('toast', [
                    'message' => 'You are not authorized to edit this attendance record',
                    'variant' => 'danger'
                ]);
                return;
            }
            
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedAttendance = null;
        $this->resetValidation();
    }

    public function updateAttendance()
    {
        if (!$this->selectedAttendance) {
            return;
        }

        // Validate the input
        $this->validate([
            'selectedAttendance.in_time' => 'required|date',
            'selectedAttendance.out_time' => 'nullable|date|after:selectedAttendance.in_time',
            'selectedAttendance.in_message' => 'nullable|string|max:255',
            'selectedAttendance.out_message' => 'nullable|string|max:255',
        ]);

        try {
            $attendance = Attendance::find($this->selectedAttendance->id);
            
            if (!$attendance) {
                $this->dispatch('toast', [
                    'message' => 'Attendance record not found',
                    'variant' => 'danger'
                ]);
                return;
            }

            // Check permissions
            if (!$this->isAdmin && $attendance->user_id !== auth()->id()) {
                $this->dispatch('toast', [
                    'message' => 'You are not authorized to update this attendance record',
                    'variant' => 'danger'
                ]);
                return;
            }

            // Update the attendance record
            $attendance->in_time = $this->selectedAttendance->in_time;
            $attendance->out_time = $this->selectedAttendance->out_time;
            $attendance->in_message = $this->selectedAttendance->in_message;
            $attendance->out_message = $this->selectedAttendance->out_message;

            // Recalculate worked hours if both times are set
            if ($attendance->in_time && $attendance->out_time) {
                $inTime = Carbon::parse($attendance->in_time);
                $outTime = Carbon::parse($attendance->out_time);
                $attendance->worked = $outTime->diffInSeconds($inTime);
            } else {
                $attendance->worked = null;
            }

            $attendance->save();

            $this->dispatch('toast', [
                'message' => 'Attendance record updated successfully',
                'variant' => 'success'
            ]);

            $this->closeEditModal();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to update attendance record: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

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
