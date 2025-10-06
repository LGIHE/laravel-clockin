<?php

namespace App\Livewire\Attendance;

use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Livewire\Component;

class ForcePunch extends Component
{
    public $userId = '';
    public $punchType = 'in';
    public $punchTime = '';
    public $message = '';
    public $isLoading = false;
    
    public $users = [];

    protected AttendanceService $attendanceService;

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'punchType' => 'required|in:in,out',
        'punchTime' => 'required|date',
        'message' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'userId.required' => 'Please select a user',
        'userId.exists' => 'Selected user does not exist',
        'punchType.required' => 'Please select punch type',
        'punchType.in' => 'Invalid punch type',
        'punchTime.required' => 'Please select date and time',
        'punchTime.date' => 'Invalid date and time format',
    ];

    public function boot(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function mount()
    {
        // Set default punch time to now
        $this->punchTime = Carbon::now()->format('Y-m-d\TH:i');
        
        // Load active users
        $this->users = User::select('id', 'name', 'email')
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    public function submit()
    {
        $this->validate();
        
        $this->isLoading = true;
        
        try {
            $this->attendanceService->forcePunch(
                $this->userId,
                $this->punchType,
                $this->punchTime,
                $this->message
            );
            
            $this->dispatch('toast', [
                'message' => 'Force punch completed successfully!',
                'variant' => 'success'
            ]);
            
            // Emit event to refresh attendance list
            $this->dispatch('attendance-updated');
            
            // Reset form
            $this->reset(['userId', 'message']);
            $this->punchType = 'in';
            $this->punchTime = Carbon::now()->format('Y-m-d\TH:i');
            
            // Close modal
            $this->dispatch('close-force-punch-modal');
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
        return view('livewire.attendance.force-punch');
    }
}
