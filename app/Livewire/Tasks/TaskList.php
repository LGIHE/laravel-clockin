<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Str;

class TaskList extends Component
{
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    public $taskId = null;
    public $title = '';
    public $description = '';
    public $project_id = '';
    public $start_date = '';
    public $end_date = '';
    public $status = 'in-progress';
    
    public $selectedTask = null;

    protected $rules = [
        'title' => 'required|string|max:100',
        'description' => 'nullable|string|max:500',
        'project_id' => 'required|exists:projects,id',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'nullable|in:in-progress,on-hold,completed',
    ];

    protected $messages = [
        'title.required' => 'Task title is required',
        'title.max' => 'Task title must be less than 100 characters',
        'description.max' => 'Description must be less than 500 characters',
        'project_id.required' => 'Please select a project',
        'project_id.exists' => 'Selected project does not exist',
        'start_date.required' => 'Start date is required',
        'start_date.date' => 'Start date must be a valid date',
        'end_date.date' => 'End date must be a valid date',
        'end_date.after_or_equal' => 'End date must be after or equal to start date',
        'status.in' => 'Invalid status selected',
    ];

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function createTask()
    {
        $this->validate();

        try {
            Task::create([
                'user_id' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description ?: null,
                'project_id' => $this->project_id ?: null,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date ?: null,
                'status' => $this->status ?: 'in-progress',
            ]);

            $this->dispatch('toast', [
                'message' => 'Task created successfully',
                'variant' => 'success'
            ]);

            $this->closeCreateModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error creating task: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openEditModal($taskId)
    {
        $task = Task::where('id', $taskId)
            ->where('user_id', auth()->id())
            ->first();
        
        if (!$task) {
            $this->dispatch('toast', [
                'message' => 'Task not found or you do not have permission to edit it',
                'variant' => 'danger'
            ]);
            return;
        }
        
        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->project_id = $task->project_id;
        $this->start_date = $task->start_date ? $task->start_date->format('Y-m-d') : '';
        $this->end_date = $task->end_date ? $task->end_date->format('Y-m-d') : '';
        $this->status = $task->status;
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateTask()
    {
        $this->validate();

        try {
            $task = Task::where('id', $this->taskId)
                ->where('user_id', auth()->id())
                ->first();

            if (!$task) {
                $this->dispatch('toast', [
                    'message' => 'Task not found or you do not have permission to edit it',
                    'variant' => 'danger'
                ]);
                return;
            }

            $task->update([
                'title' => $this->title,
                'description' => $this->description ?: null,
                'project_id' => $this->project_id ?: null,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date ?: null,
                'status' => $this->status,
            ]);

            $this->dispatch('toast', [
                'message' => 'Task updated successfully',
                'variant' => 'success'
            ]);

            $this->closeEditModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error updating task: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function confirmDelete($taskId)
    {
        $task = Task::where('id', $taskId)
            ->where('user_id', auth()->id())
            ->first();
        
        if (!$task) {
            $this->dispatch('toast', [
                'message' => 'Task not found or you do not have permission to delete it',
                'variant' => 'danger'
            ]);
            return;
        }
        
        $this->selectedTask = $task;
        $this->showDeleteModal = true;
    }

    public function deleteTask()
    {
        try {
            if ($this->selectedTask) {
                $this->selectedTask->delete();

                $this->dispatch('toast', [
                    'message' => 'Task deleted successfully',
                    'variant' => 'success'
                ]);
            }

            $this->showDeleteModal = false;
            $this->selectedTask = null;
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error deleting task: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedTask = null;
    }

    private function resetForm()
    {
        $this->taskId = null;
        $this->title = '';
        $this->description = '';
        $this->project_id = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->status = 'in-progress';
        $this->resetValidation();
    }

    public function render()
    {
        $tasks = Task::with('project')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $projects = Project::where('status', 'ACTIVE')
            ->orderBy('name')
            ->get();

        return view('livewire.tasks.task-list', [
            'tasks' => $tasks,
            'projects' => $projects,
        ]);
    }
}
