<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\UserLevel;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Project;
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
    public $perPage = 10;
    
    public $selectedUser = null;
    public $showDetailModal = false;
    public $showDeleteModal = false;
    public $showBulkAssignModal = false;
    public $showAddUserModal = false;
    public $showEditUserModal = false;
    
    public $departments = [];
    public $userLevels = [];
    public $designations = [];
    public $supervisors = [];
    public $isAdmin = false;
    
    // For tabs
    public $activeTab = 'userList';
    
    // For bulk supervisor assignment
    public $selectedSupervisor = '';
    public $bulkDepartmentFilter = '';
    public $selectedUserIds = [];
    public $selectAll = false;
    public $filteredUsersForBulk = [];
    
    // For adding new user
    public $newUser = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'employee_code' => '',
        'user_level_id' => '',
        'department_id' => '',
        'designation_id' => '',
        'password' => '',
    ];

    // For editing user
    public $editUser = [
        'id' => '',
        'name' => '',
        'email' => '',
        'phone' => '',
        'employee_code' => '',
        'user_level_id' => '',
        'department_id' => '',
        'designation_id' => '',
        'supervisor_id' => '',
        'status' => 1,
        'password' => '',
        'password_confirmation' => '',
    ];
    
    public $editUserProjects = [];
    public $projects = [];
    public $showEditPassword = false;

    protected UserService $userService;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function mount()
    {
        // Check if user is admin, supervisor, or super_admin (case-insensitive)
        $userLevelName = strtolower(auth()->user()->userLevel->name);
        $this->isAdmin = in_array($userLevelName, ['admin', 'supervisor', 'super_admin', 'super admin']);
        
        // Load filter options
        $this->departments = Department::orderBy('name')->get();
        $this->userLevels = UserLevel::orderBy('name')->get();
        $this->designations = Designation::orderBy('name')->get();
        $this->projects = Project::where('status', 'ACTIVE')->orderBy('name')->get();
        
        // Load supervisors (users with admin or supervisor roles)
        $this->loadSupervisors();
        
        // Load filtered users for bulk assignment
        $this->loadFilteredUsersForBulk();
    }

    public function loadSupervisors()
    {
        $this->supervisors = User::whereHas('userLevel', function($query) {
            $query->whereIn('name', ['admin', 'supervisor', 'super_admin']);
        })->orderBy('name')->get();
    }

    public function loadFilteredUsersForBulk()
    {
        $query = User::with(['department']);
        
        if (!empty($this->bulkDepartmentFilter)) {
            $query->where('department_id', $this->bulkDepartmentFilter);
        }
        
        $this->filteredUsersForBulk = $query->get();
    }

    public function updatedBulkDepartmentFilter()
    {
        $this->loadFilteredUsersForBulk();
        $this->selectedUserIds = [];
        $this->selectAll = false;
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedUserIds = $this->filteredUsersForBulk->pluck('id')->toArray();
        } else {
            $this->selectedUserIds = [];
        }
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
    
    public function updatingPerPage()
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
    
    // Bulk supervisor assignment methods
    public function openBulkAssignModal()
    {
        $this->showBulkAssignModal = true;
        $this->loadFilteredUsersForBulk();
    }
    
    public function closeBulkAssignModal()
    {
        $this->showBulkAssignModal = false;
        $this->selectedSupervisor = '';
        $this->bulkDepartmentFilter = '';
        $this->selectedUserIds = [];
        $this->selectAll = false;
    }
    
    public function assignSupervisorToUsers()
    {
        if (empty($this->selectedSupervisor)) {
            $this->dispatch('toast', [
                'message' => 'Please select a supervisor to assign',
                'variant' => 'danger'
            ]);
            return;
        }
        
        if (count($this->selectedUserIds) === 0) {
            $this->dispatch('toast', [
                'message' => 'Please select at least one user',
                'variant' => 'danger'
            ]);
            return;
        }
        
        try {
            // Update supervisor for selected users
            User::whereIn('id', $this->selectedUserIds)->update([
                'supervisor_id' => $this->selectedSupervisor
            ]);
            
            $this->dispatch('toast', [
                'message' => "Assigned supervisor to " . count($this->selectedUserIds) . " user(s) successfully",
                'variant' => 'success'
            ]);
            
            $this->closeBulkAssignModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }
    
    // Additional action methods
    public function changeDepartment($userId)
    {
        $this->dispatch('toast', [
            'message' => 'Change department feature coming soon',
            'variant' => 'info'
        ]);
    }
    
    public function changeSupervisor($userId)
    {
        $this->dispatch('toast', [
            'message' => 'Change supervisor feature coming soon',
            'variant' => 'info'
        ]);
    }
    
    public function ipRestriction($userId)
    {
        $this->dispatch('toast', [
            'message' => 'IP restriction feature coming soon',
            'variant' => 'info'
        ]);
    }
    
    public function updatePassword($userId)
    {
        $this->dispatch('toast', [
            'message' => 'Update password feature coming soon',
            'variant' => 'info'
        ]);
    }
    
    public function updateDesignation($userId)
    {
        $this->dispatch('toast', [
            'message' => 'Update designation feature coming soon',
            'variant' => 'info'
        ]);
    }
    
    public function lastInTime($userId)
    {
        $this->dispatch('toast', [
            'message' => 'Last in time feature coming soon',
            'variant' => 'info'
        ]);
    }
    
    public function autoPunchOut($userId)
    {
        $this->dispatch('toast', [
            'message' => 'Auto punch out feature coming soon',
            'variant' => 'info'
        ]);
    }
    
    public function forcePunch($userId)
    {
        $this->dispatch('toast', [
            'message' => 'Force punch feature coming soon',
            'variant' => 'info'
        ]);
    }
    
    public function forceLogin($userId)
    {
        $this->dispatch('toast', [
            'message' => 'Force login feature coming soon',
            'variant' => 'info'
        ]);
    }
    
    public function openAddUserModal()
    {
        $this->showAddUserModal = true;
        $this->resetNewUserForm();
    }
    
    public function closeAddUserModal()
    {
        $this->showAddUserModal = false;
        $this->resetNewUserForm();
    }
    
    public function resetNewUserForm()
    {
        $this->newUser = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'employee_code' => '',
            'user_level_id' => '',
            'department_id' => '',
            'designation_id' => '',
            'password' => '',
        ];
        $this->resetErrorBag();
    }
    
    public function createUser()
    {
        $this->validate([
            'newUser.name' => 'required|string|max:255',
            'newUser.email' => 'required|email|unique:users,email',
            'newUser.phone' => 'nullable|string|max:20',
            'newUser.employee_code' => 'nullable|string|max:50',
            'newUser.user_level_id' => 'required|exists:user_levels,id',
            'newUser.department_id' => 'required|exists:departments,id',
            'newUser.designation_id' => 'nullable|exists:designations,id',
            'newUser.password' => 'required|string|min:8',
        ]);
        
        try {
            User::create([
                'name' => $this->newUser['name'],
                'email' => $this->newUser['email'],
                'phone' => $this->newUser['phone'] ?: null,
                'employee_code' => $this->newUser['employee_code'] ?: null,
                'user_level_id' => $this->newUser['user_level_id'],
                'department_id' => $this->newUser['department_id'],
                'designation_id' => $this->newUser['designation_id'] ?: null,
                'password' => bcrypt($this->newUser['password']),
                'status' => 1, // Active by default
            ]);
            
            $this->dispatch('toast', [
                'message' => 'User created successfully',
                'variant' => 'success'
            ]);
            
            $this->closeAddUserModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to create user: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openEditUserModal($userId)
    {
        try {
            $user = User::with(['userLevel', 'department', 'designation', 'supervisor', 'projects'])->find($userId);
            
            if ($user) {
                $this->editUser = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'employee_code' => $user->employee_code ?? '',
                    'user_level_id' => $user->user_level_id,
                    'department_id' => $user->department_id ?? '',
                    'designation_id' => $user->designation_id ?? '',
                    'supervisor_id' => $user->supervisor_id ?? '',
                    'status' => $user->status,
                    'password' => '',
                    'password_confirmation' => '',
                ];
                $this->editUserProjects = $user->projects ? $user->projects->pluck('id')->toArray() : [];
                $this->showEditUserModal = true;
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error loading user: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeEditUserModal()
    {
        $this->showEditUserModal = false;
        $this->showEditPassword = false;
        $this->editUser = [
            'id' => '',
            'name' => '',
            'email' => '',
            'phone' => '',
            'employee_code' => '',
            'user_level_id' => '',
            'department_id' => '',
            'designation_id' => '',
            'supervisor_id' => '',
            'status' => 1,
            'password' => '',
            'password_confirmation' => '',
        ];
        $this->editUserProjects = [];
        $this->resetErrorBag();
    }

    public function toggleEditProjectSelection($projectId)
    {
        if (in_array($projectId, $this->editUserProjects)) {
            $this->editUserProjects = array_diff($this->editUserProjects, [$projectId]);
        } else {
            $this->editUserProjects[] = $projectId;
        }
    }

    public function updateUser()
    {
        $rules = [
            'editUser.name' => 'required|string|max:255',
            'editUser.email' => 'required|email|unique:users,email,' . $this->editUser['id'],
            'editUser.phone' => 'nullable|string|max:20',
            'editUser.employee_code' => 'nullable|string|max:50',
            'editUser.user_level_id' => 'required|exists:user_levels,id',
            'editUser.department_id' => 'nullable|exists:departments,id',
            'editUser.designation_id' => 'nullable|exists:designations,id',
            'editUser.supervisor_id' => 'nullable|exists:users,id',
            'editUser.status' => 'required|in:0,1',
        ];

        // Only validate password if it's provided
        if (!empty($this->editUser['password'])) {
            $rules['editUser.password'] = 'required|string|min:6|confirmed';
            $rules['editUser.password_confirmation'] = 'required';
        }

        $this->validate($rules);
        
        try {
            $user = User::findOrFail($this->editUser['id']);
            
            // Convert empty strings to null for nullable foreign keys
            $updateData = [
                'name' => $this->editUser['name'],
                'email' => $this->editUser['email'],
                'phone' => $this->editUser['phone'] ?: null,
                'employee_code' => $this->editUser['employee_code'] ?: null,
                'user_level_id' => $this->editUser['user_level_id'],
                'department_id' => $this->editUser['department_id'] ?: null,
                'designation_id' => $this->editUser['designation_id'] ?: null,
                'supervisor_id' => $this->editUser['supervisor_id'] ?: null,
                'status' => $this->editUser['status'],
            ];

            // Only update password if it's provided
            if (!empty($this->editUser['password'])) {
                $updateData['password'] = bcrypt($this->editUser['password']);
            }

            $user->update($updateData);

            // Sync projects
            $user->projects()->sync($this->editUserProjects);
            
            $this->dispatch('toast', [
                'message' => 'User updated successfully',
                'variant' => 'success'
            ]);

            $this->closeEditUserModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to update user: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
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
        ])->layout('components.layouts.app', ['title' => 'User Management']);
    }
}
