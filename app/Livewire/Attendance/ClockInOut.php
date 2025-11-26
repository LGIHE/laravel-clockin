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
    
    // Task creation modal
    public $showCreateTaskModal = false;
    public $newTaskTitle = '';
    public $newTaskDescription = '';
    public $newTaskStartDate = '';
    public $newTaskEndDate = '';

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
    
    public function openCreateTaskModal()
    {
        $this->resetTaskForm();
        // Set default start date to today
        $this->newTaskStartDate = now()->format('Y-m-d');
        $this->showCreateTaskModal = true;
    }
    
    public function closeCreateTaskModal()
    {
        $this->showCreateTaskModal = false;
        $this->resetTaskForm();
    }
    
    public function createTask()
    {
        $this->validate([
            'newTaskTitle' => 'required|string|max:100',
            'newTaskDescription' => 'nullable|string|max:500',
            'newTaskStartDate' => 'required|date',
            'newTaskEndDate' => 'nullable|date|after_or_equal:newTaskStartDate',
        ], [
            'newTaskTitle.required' => 'Task title is required',
            'newTaskTitle.max' => 'Task title must be less than 100 characters',
            'newTaskDescription.max' => 'Description must be less than 500 characters',
            'newTaskStartDate.required' => 'Start date is required',
            'newTaskStartDate.date' => 'Start date must be a valid date',
            'newTaskEndDate.date' => 'End date must be a valid date',
            'newTaskEndDate.after_or_equal' => 'End date must be after or equal to start date',
        ]);
        
        try {
            $task = \App\Models\Task::create([
                'user_id' => auth()->id(),
                'title' => $this->newTaskTitle,
                'description' => $this->newTaskDescription ?: null,
                'project_id' => $this->selectedProject ?: null,
                'start_date' => $this->newTaskStartDate,
                'end_date' => $this->newTaskEndDate ?: null,
                'status' => 'in-progress',
            ]);
            
            // Select the newly created task
            $this->selectedTask = $task->id;
            
            // Reload tasks list
            $this->loadUserTasks();
            
            $this->dispatch('toast', [
                'message' => 'Task created successfully',
                'variant' => 'success'
            ]);
            
            $this->closeCreateTaskModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error creating task: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }
    
    private function resetTaskForm()
    {
        $this->newTaskTitle = '';
        $this->newTaskDescription = '';
        $this->newTaskStartDate = '';
        $this->newTaskEndDate = '';
        $this->resetValidation([
            'newTaskTitle',
            'newTaskDescription',
            'newTaskStartDate',
            'newTaskEndDate'
        ]);
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
