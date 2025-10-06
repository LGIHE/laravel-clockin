<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\UserLevel;
use App\Models\Department;
use App\Models\Designation;
use App\Services\UserService;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $departmentId = '';
    public $userLevelId = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $perPage = 15;
    
    public $selectedUser = null;
    public $showDetailModal = false;
    public $showDeleteModal = false;
    
    public $departments = [];
    public $userLevels = [];
    public $isAdmin = false;

    protected UserService $userService;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function mount()
    {
        $this->isAdmin = auth()->user()->userLevel->name === 'admin';
        
        // Load filter options
        $this->departments = Department::orderBy('name')->get();
        $this->userLevels = UserLevel::orderBy('name')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingDepartmentId()
    {
        $this->resetPage();
    }

    public function updatingUserLevelId()
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

    public function applyFilters()
    {
        $this->resetPage();
        
        $this->dispatch('toast', [
            'message' => 'Filters applied',
            'variant' => 'info'
        ]);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->departmentId = '';
        $this->userLevelId = '';
        $this->sortBy = 'created_at';
        $this->sortOrder = 'desc';
        
        $this->resetPage();
        
        $this->dispatch('toast', [
            'message' => 'Filters cleared',
            'variant' => 'info'
        ]);
    }

    public function viewDetails($userId)
    {
        $this->selectedUser = User::with(['userLevel', 'department', 'designation', 'project'])
            ->find($userId);
        
        if ($this->selectedUser) {
            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedUser = null;
    }

    public function toggleStatus($userId)
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                throw new \Exception('User not found');
            }
            
            $newStatus = $user->status === 1 ? 0 : 1;
            $this->userService->changeStatus($userId, $newStatus);
            
            $statusText = $newStatus === 1 ? 'activated' : 'deactivated';
            
            $this->dispatch('toast', [
                'message' => "User {$statusText} successfully",
                'variant' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function confirmDelete($userId)
    {
        $this->selectedUser = User::find($userId);
        
        if ($this->selectedUser) {
            $this->showDeleteModal = true;
        }
    }

    public function deleteUser()
    {
        try {
            if (!$this->selectedUser) {
                throw new \Exception('User not found');
            }
            
            $this->userService->deleteUser($this->selectedUser->id);
            
            $this->dispatch('toast', [
                'message' => 'User deleted successfully',
                'variant' => 'success'
            ]);
            
            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedUser = null;
    }

    protected $listeners = ['user-saved' => '$refresh'];

    public function render()
    {
        $query = User::with(['userLevel', 'department', 'designation']);

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        // Apply department filter
        if (!empty($this->departmentId)) {
            $query->where('department_id', $this->departmentId);
        }

        // Apply user level filter
        if (!empty($this->userLevelId)) {
            $query->where('user_level_id', $this->userLevelId);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortOrder);

        // Paginate results
        $users = $query->paginate($this->perPage);

        return view('livewire.users.user-list', [
            'users' => $users
        ]);
    }
}
