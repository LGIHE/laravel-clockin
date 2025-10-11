<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ProjectList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $perPage = 15;
    public $activeTab = 'project'; // Add activeTab property
    
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showAssignUsersModal = false;
    
    public $projectId = null;
    public $name = '';
    public $description = '';
    public $start_date = '';
    public $end_date = '';
    public $projectStatus = 'ACTIVE';
    
    public $selectedProject = null;
    public $availableUsers = [];
    public $selectedUserIds = [];
    public $isAdmin = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'projectStatus' => 'required|in:ACTIVE,COMPLETED,ON_HOLD',
    ];

    protected $messages = [
        'name.required' => 'Project name is required',
        'name.max' => 'Project name cannot exceed 255 characters',
        'description.max' => 'Description cannot exceed 1000 characters',
        'start_date.required' => 'Start date is required',
        'start_date.date' => 'Start date must be a valid date',
        'end_date.date' => 'End date must be a valid date',
        'end_date.after_or_equal' => 'End date must be after or equal to start date',
        'projectStatus.required' => 'Status is required',
        'projectStatus.in' => 'Invalid status selected',
    ];

    public function mount()
    {
        $this->isAdmin = strtolower(auth()->user()->userLevel->name) === 'admin';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
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

    public function createProject()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->validate();

        try {
            Project::create([
                'id' => Str::uuid()->toString(),
                'name' => $this->name,
                'description' => $this->description,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date ?: null,
                'status' => $this->projectStatus,
            ]);

            $this->dispatch('toast', [
                'message' => 'Project created successfully',
                'variant' => 'success'
            ]);

            $this->closeCreateModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error creating project: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openEditModal($projectId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $project = Project::find($projectId);
        
        if ($project) {
            $this->projectId = $project->id;
            $this->name = $project->name;
            $this->description = $project->description;
            $this->start_date = $project->start_date ? $project->start_date->format('Y-m-d') : '';
            $this->end_date = $project->end_date ? $project->end_date->format('Y-m-d') : '';
            $this->projectStatus = $project->status;
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateProject()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->validate();

        try {
            $project = Project::findOrFail($this->projectId);

            $project->update([
                'name' => $this->name,
                'description' => $this->description,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date ?: null,
                'status' => $this->projectStatus,
            ]);

            $this->dispatch('toast', [
                'message' => 'Project updated successfully',
                'variant' => 'success'
            ]);

            $this->closeEditModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error updating project: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openAssignUsersModal($projectId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->selectedProject = Project::with('users')->find($projectId);
        
        if ($this->selectedProject) {
            // Get all active users
            $this->availableUsers = User::with(['userLevel', 'department', 'designation'])
                ->where('status', 1)
                ->orderBy('name')
                ->get();
            
            // Get currently assigned user IDs
            $this->selectedUserIds = $this->selectedProject->users->pluck('id')->toArray();
            
            $this->showAssignUsersModal = true;
        }
    }

    public function closeAssignUsersModal()
    {
        $this->showAssignUsersModal = false;
        $this->selectedProject = null;
        $this->availableUsers = [];
        $this->selectedUserIds = [];
    }

    public function assignUsers()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        try {
            if (!$this->selectedProject) {
                throw new \Exception('Project not found');
            }

            // Get currently assigned users
            $currentUserIds = $this->selectedProject->users->pluck('id')->toArray();
            
            // Find users to add (in selected but not in current)
            $usersToAdd = array_diff($this->selectedUserIds, $currentUserIds);
            
            // Find users to remove (in current but not in selected)
            $usersToRemove = array_diff($currentUserIds, $this->selectedUserIds);
            
            // Add new users
            if (!empty($usersToAdd)) {
                User::whereIn('id', $usersToAdd)->update(['project_id' => $this->selectedProject->id]);
            }
            
            // Remove users
            if (!empty($usersToRemove)) {
                User::whereIn('id', $usersToRemove)
                    ->where('project_id', $this->selectedProject->id)
                    ->update(['project_id' => null]);
            }

            $this->dispatch('toast', [
                'message' => 'Users assigned successfully',
                'variant' => 'success'
            ]);

            $this->closeAssignUsersModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error assigning users: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function removeUserFromProject($projectId, $userId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        try {
            $user = User::findOrFail($userId);
            
            if ($user->project_id === $projectId) {
                $user->update(['project_id' => null]);
                
                $this->dispatch('toast', [
                    'message' => 'User removed from project successfully',
                    'variant' => 'success'
                ]);
            }
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error removing user: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function confirmDelete($projectId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->selectedProject = Project::withCount('users')->find($projectId);
        
        if ($this->selectedProject) {
            $this->showDeleteModal = true;
        }
    }

    public function deleteProject()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        try {
            if (!$this->selectedProject) {
                throw new \Exception('Project not found');
            }

            // Check if project has assigned users
            if ($this->selectedProject->users_count > 0) {
                $this->dispatch('toast', [
                    'message' => 'Cannot delete project with assigned users',
                    'variant' => 'danger'
                ]);
                $this->closeDeleteModal();
                return;
            }

            $this->selectedProject->delete();

            $this->dispatch('toast', [
                'message' => 'Project deleted successfully',
                'variant' => 'success'
            ]);

            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error deleting project: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedProject = null;
    }

    private function resetForm()
    {
        $this->projectId = null;
        $this->name = '';
        $this->description = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->projectStatus = 'ACTIVE';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Project::withCount('users');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortOrder);

        // Paginate results
        $projects = $query->paginate($this->perPage);

        return view('livewire.projects.project-list', [
            'projects' => $projects
        ]);
    }
}
