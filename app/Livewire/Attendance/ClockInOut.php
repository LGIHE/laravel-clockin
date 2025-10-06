<?php

namespace App\Livewire\Attendance;

use App\Services\AttendanceService;
use Livewire\Component;

class ClockInOut extends Component
{
    public $attendanceStatus;
    public $clockMessage = '';
    public $isLoading = false;

    protected AttendanceService $attendanceService;

    public function boot(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function mount()
    {
        $this->loadAttendanceStatus();
    }

    public function loadAttendanceStatus()
    {
        try {
            $this->attendanceStatus = $this->attendanceService->getCurrentStatus(auth()->id());
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to load attendance status: ' . $e->getMessage(),
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
            $this->loadAttendanceStatus();
            
            $this->dispatch('toast', [
                'message' => 'Clocked in successfully!',
                'variant' => 'success'
            ]);
            
            // Emit event to refresh other components
            $this->dispatch('attendance-updated');
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
            $this->loadAttendanceStatus();
            
            $this->dispatch('toast', [
                'message' => 'Clocked out successfully!',
                'variant' => 'success'
            ]);
            
            // Emit event to refresh other components
            $this->dispatch('attendance-updated');
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function refreshStatus()
    {
        $this->loadAttendanceStatus();
    }

    public function render()
    {
        return view('livewire.attendance.clock-in-out');
    }
}
