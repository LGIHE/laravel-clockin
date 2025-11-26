<?php

namespace App\Livewire\Dashboard;

use App\Services\AttendanceService;
use App\Services\DashboardService;
use Livewire\Component;

class UserDashboard extends Component
{
    public $dashboardData;
    public $attendanceStatus;
    public $chartData;
    public $holidays;
    public $notices;
    public $clockMessage = '';
    public $selectedProjects = []; // Changed to array for multiple selection
    public $selectedTasks = []; // Changed to array for multiple selection
    public $selectedPredefinedTasks = []; // Array for predefined task selections
    public $taskStatuses = []; // Array to store status for each task
    public $projectToAdd = ''; // Temporary holder for project selection
    public $taskToAdd = ''; // Temporary holder for task selection
    public $predefinedTaskToAdd = ''; // Temporary holder for predefined task selection
    public $showCustomTaskField = false; // Show/hide custom task field
    public $userProjects = [];
    public $userTasks = [];
    public $showPunchOutModal = false;
    public $isLoading = false;
    
    // Task creation modal
    public $showCreateTaskModal = false;
    public $newTaskTitle = '';
    public $newTaskDescription = '';
    public $newTaskStartDate = '';
    public $newTaskEndDate = '';

    // Predefined task options
    public $predefinedTaskOptions = [
        'Report writing',
        'Database update',
        'Pretraining discussion',
        'Module development',
        'Post-training discussion',
        'Other'
    ];

    protected AttendanceService $attendanceService;
    protected DashboardService $dashboardService;

    public function boot(AttendanceService $attendanceService, DashboardService $dashboardService)
    {
        $this->attendanceService = $attendanceService;
        $this->dashboardService = $dashboardService;
    }

    public function mount()
    {
        // Initialize with safe defaults
        $this->dashboardData = [
            'attendance_status' => [
                'clocked_in' => false,
                'in_time' => null,
                'in_message' => null,
            ],
            'stats' => [
                'leave_this_year' => 0,
                'last_30_days_formatted' => '00:00:00',
            ],
            'work_duration' => '00:00:00',
            'recent_attendance' => collect([]),
        ];
        $this->attendanceStatus = $this->dashboardData['attendance_status'];
        $this->chartData = [];
        $this->holidays = collect([]);
        $this->notices = collect([]);
        
        $this->loadDashboardData();
        $this->loadUserProjects();
        $this->loadUserTasks();
    }

    public function loadUserProjects()
    {
        // Load all active projects instead of just assigned ones
        $this->userProjects = \App\Models\Project::where('status', 'ACTIVE')->get();
        
        // If user has only one project, select it by default
        if ($this->userProjects->count() === 1 && empty($this->selectedProjects)) {
            $this->selectedProjects = [$this->userProjects->first()->id];
        }
    }

    public function updatedProjectToAdd($value)
    {
        if (!empty($value) && !in_array($value, $this->selectedProjects)) {
            $this->selectedProjects[] = $value;
            $this->projectToAdd = ''; // Reset the select
        }
    }

    public function removeProject($projectId)
    {
        $this->selectedProjects = array_values(array_filter($this->selectedProjects, function($id) use ($projectId) {
            return $id !== $projectId;
        }));
    }

    public function updatedPredefinedTaskToAdd($value)
    {
        if (!empty($value)) {
            if ($value === 'Other') {
                // Show custom task field
                $this->showCustomTaskField = true;
            } else {
                // Add predefined task
                if (!in_array($value, $this->selectedPredefinedTasks)) {
                    $this->selectedPredefinedTasks[] = $value;
                }
            }
            $this->predefinedTaskToAdd = ''; // Reset the select
        }
    }

    public function removePredefinedTask($task)
    {
        $this->selectedPredefinedTasks = array_values(array_filter($this->selectedPredefinedTasks, function($t) use ($task) {
            return $t !== $task;
        }));
    }

    public function updatedTaskToAdd($value)
    {
        if (!empty($value)) {
            if ($value === 'create_new') {
                // Open the create task modal
                \Log::info('Opening create task modal from dropdown');
                $this->openCreateTaskModal();
                $this->taskToAdd = ''; // Reset the select
            } elseif (!in_array($value, $this->selectedTasks)) {
                $this->selectedTasks[] = $value;
                $this->taskToAdd = ''; // Reset the select
            }
        }
    }

    public function removeTask($taskId)
    {
        $this->selectedTasks = array_values(array_filter($this->selectedTasks, function($id) use ($taskId) {
            return $id !== $taskId;
        }));
    }

    public function loadUserTasks()
    {
        $this->userTasks = \App\Models\Task::where('user_id', auth()->id())
            ->whereIn('status', ['in-progress', 'on-hold'])
            ->get();
    }
    
    public function openCreateTaskModal()
    {
        \Log::info('openCreateTaskModal called');
        $this->resetTaskForm();
        // Set default start date to today
        $this->newTaskStartDate = now()->format('Y-m-d');
        $this->showCreateTaskModal = true;
        \Log::info('showCreateTaskModal set to: ' . ($this->showCreateTaskModal ? 'true' : 'false'));
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
            // Use the first selected project if available
            $projectId = !empty($this->selectedProjects) ? $this->selectedProjects[0] : null;
            
            $task = \App\Models\Task::create([
                'user_id' => auth()->id(),
                'title' => $this->newTaskTitle,
                'description' => $this->newTaskDescription ?: null,
                'project_id' => $projectId,
                'start_date' => $this->newTaskStartDate,
                'end_date' => $this->newTaskEndDate ?: null,
                'status' => 'in-progress',
            ]);
            
            // Add the newly created task to selected tasks
            if (!in_array($task->id, $this->selectedTasks)) {
                $this->selectedTasks[] = $task->id;
            }
            
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

    public function loadDashboardData()
    {
        try {
            $this->dashboardData = $this->dashboardService->getUserDashboard(auth()->id());
            $this->attendanceStatus = $this->dashboardData['attendance_status'] ?? [
                'clocked_in' => false,
                'in_time' => null,
                'in_message' => null,
            ];
            $this->chartData = $this->dashboardData['chart_data'] ?? [];
            $this->holidays = $this->dashboardData['holidays'] ?? collect([]);
            $this->notices = $this->dashboardData['notices'] ?? collect([]);
        } catch (\Exception $e) {
            // Set safe defaults on error
            $this->dashboardData = [
                'attendance_status' => [
                    'clocked_in' => false,
                    'in_time' => null,
                    'in_message' => null,
                ],
                'stats' => [
                    'leave_this_year' => 0,
                    'last_30_days_formatted' => '00:00:00',
                ],
                'work_duration' => '00:00:00',
                'recent_attendance' => collect([]),
            ];
            $this->attendanceStatus = $this->dashboardData['attendance_status'];
            $this->chartData = [];
            $this->holidays = collect([]);
            $this->notices = collect([]);
            
            $this->dispatch('toast', [
                'message' => 'Failed to load dashboard data: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function clockIn()
    {
        $this->validate([
            'selectedProjects' => 'required|array|min:1',
            'selectedProjects.*' => 'exists:projects,id',
            'selectedPredefinedTasks' => 'nullable|array',
            'selectedTasks' => 'nullable|array',
            'selectedTasks.*' => 'exists:tasks,id',
            'clockMessage' => 'nullable|string|max:500',
        ]);

        $this->isLoading = true;
        
        try {
            // Combine predefined tasks and custom tasks
            $allTasks = $this->selectedTasks;
            
            // Store predefined tasks in the clock message if any
            if (!empty($this->selectedPredefinedTasks)) {
                $predefinedTasksText = 'Tasks: ' . implode(', ', $this->selectedPredefinedTasks);
                $this->clockMessage = trim($predefinedTasksText . ($this->clockMessage ? ' | ' . $this->clockMessage : ''));
            }
            
            $this->attendanceService->clockIn(
                auth()->id(), 
                $this->clockMessage,
                $this->selectedProjects,
                !empty($allTasks) ? $allTasks : null
            );
            
            $this->clockMessage = '';
            $this->selectedProjects = [];
            $this->selectedTasks = [];
            $this->selectedPredefinedTasks = [];
            $this->projectToAdd = '';
            $this->taskToAdd = '';
            $this->predefinedTaskToAdd = '';
            $this->showCustomTaskField = false;
            $this->loadDashboardData();
            
            $this->dispatch('toast', [
                'message' => 'Clocked in successfully!',
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

    public function openPunchOutModal()
    {
        $this->showPunchOutModal = true;
    }

    public function closePunchOutModal()
    {
        $this->showPunchOutModal = false;
        $this->clockMessage = '';
        $this->taskStatuses = [];
    }

    public function openPunchOutModalWithData()
    {
        // Initialize task statuses for all tasks in current attendance
        if (isset($this->attendanceStatus['attendance']) && $this->attendanceStatus['attendance']->tasks) {
            foreach ($this->attendanceStatus['attendance']->tasks as $task) {
                $this->taskStatuses[$task->id] = $task->pivot->status ?? 'in-progress';
            }
        }
        $this->showPunchOutModal = true;
    }

    public function confirmClockOut()
    {
        $this->validate([
            'clockMessage' => 'nullable|string|max:500',
            'taskStatuses' => 'nullable|array',
            'taskStatuses.*' => 'in:in-progress,on-hold,completed',
        ]);

        $this->isLoading = true;
        
        try {
            // If no task statuses set but tasks exist, default all to completed
            if (empty($this->taskStatuses) && 
                isset($this->attendanceStatus['attendance']) && 
                $this->attendanceStatus['attendance']->tasks && 
                $this->attendanceStatus['attendance']->tasks->isNotEmpty()) {
                
                foreach ($this->attendanceStatus['attendance']->tasks as $task) {
                    $this->taskStatuses[$task->id] = 'completed';
                }
            }

            $this->attendanceService->clockOut(
                auth()->id(), 
                $this->clockMessage,
                !empty($this->taskStatuses) ? $this->taskStatuses : null
            );
            
            $this->clockMessage = '';
            $this->taskStatuses = [];
            $this->showPunchOutModal = false;
            $this->loadDashboardData();
            
            $this->dispatch('toast', [
                'message' => 'Clocked out successfully!',
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

    public function refreshDashboard()
    {
        $this->loadDashboardData();
        
        $this->dispatch('toast', [
            'message' => 'Dashboard refreshed',
            'variant' => 'info'
        ]);
    }

    public function getIsClockedInProperty()
    {
        return isset($this->attendanceStatus) && 
               is_array($this->attendanceStatus) && 
               ($this->attendanceStatus['clocked_in'] ?? false);
    }

    public function render()
    {
        return view('livewire.dashboard.user-dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard']);
    }
}
