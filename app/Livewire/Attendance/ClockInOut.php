<?php

namespace App\Livewire\Attendance;

use App\Services\AttendanceService;
use Livewire\Component;

class ClockInOut extends Component
{
    public $attendanceStatus;
    public $clockMessage = '';
    public $selectedProject = '';
    public $task = '';
    public $isLoading = false;
    public $userProjects = [];

    protected AttendanceService $attendanceService;

    public function boot(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function mount()
    {
        $this->loadAttendanceStatus();
        $this->loadUserProjects();
    }

    public function loadUserProjects()
    {
        $this->userProjects = auth()->user()->projects()->where('status', 'ACTIVE')->get();
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
        $this->validate([
            'selectedProject' => 'required|exists:projects,id',
            'task' => 'nullable|string|max:255',
            'clockMessage' => 'nullable|string|max:500',
        ]);

        $this->isLoading = true;
        
        try {
            $this->attendanceService->clockIn(
                auth()->id(), 
                $this->clockMessage,
                $this->selectedProject,
                $this->task
            );
            
            $this->clockMessage = '';
            $this->selectedProject = '';
            $this->task = '';
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
