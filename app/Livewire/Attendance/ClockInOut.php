<?php

namespace App\Livewire\Attendance;

use App\Services\AttendanceService;
use Livewire\Component;

class ClockInOut extends Component
{
    public $attendanceStatus;
    public $clockMessage = '';
    public $selectedProject = '';
    public $selectedTask = '';
    public $isLoading = false;
    public $userProjects = [];
    public $userTasks = [];

    protected AttendanceService $attendanceService;

    public function boot(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function mount()
    {
        $this->loadAttendanceStatus();
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
        return view('livewire.attendance.clock-in-out')
            ->layout('components.layouts.app', ['title' => 'Clock In/Out']);
    }
}
