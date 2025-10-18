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
    public $userStatusFilter = 'active'; // Filter for active/deactivated/archived
    public $departmentId = '';
    public $designationId = '';
    public $userLevelId = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $perPage = 10;
    
    public $selectedUser = null;
    public $showDetailModal = false;
    public $showDeleteModal = false;
    public $showDeleteConfirmationModal = false;
    public $showBulkAssignModal = false;
    public $showAddUserModal = false;
    public $showEditUserModal = false;
    public $showChangeDepartmentModal = false;
    public $showChangeSupervisorModal = false;
    public $showIpRestrictionModal = false;
    public $showUpdatePasswordModal = false;
    public $showUpdateDesignationModal = false;
    public $showAutoPunchOutModal = false;
    public $showLastInTimeModal = false;
    
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
        'primary_supervisor_id' => '',
        'secondary_supervisor_id' => '',
        'status' => 1,
        'password' => '',
        'password_confirmation' => '',
    ];
    
    public $editUserProjects = [];
    public $projects = [];
    public $showEditPassword = false;

    // For Change Department Modal
    public $changeDepartmentData = [
        'user_id' => '',
        'user_name' => '',
        'current_department' => '',
        'new_department_id' => '',
    ];

    // For Change Supervisor Modal
    public $changeSupervisorData = [
        'user_id' => '',
        'user_name' => '',
        'current_primary_supervisor' => '',
        'current_secondary_supervisor' => '',
        'new_primary_supervisor_id' => '',
        'new_secondary_supervisor_id' => '',
    ];

    // For IP Restriction Modal
    public $ipRestrictionData = [
        'user_id' => '',
        'user_name' => '',
        'ip_address' => '',
        'restriction_type' => 'allow', // allow or deny
    ];

    // For Update Password Modal
    public $updatePasswordData = [
        'user_id' => '',
        'user_name' => '',
        'new_password' => '',
        'confirm_password' => '',
    ];

    // For Update Designation Modal
    public $updateDesignationData = [
        'user_id' => '',
        'user_name' => '',
        'current_designation' => '',
        'new_designation_id' => '',
    ];

    // For Auto Punch Out Modal
    public $autoPunchOutData = [
        'user_id' => '',
        'user_name' => '',
        'auto_punch_out_time' => '',
    ];

    // For Last In Time Modal
    public $lastInTimeData = [
        'user_id' => '',
        'user_name' => '',
        'last_punch_in' => '',
        'task' => '',
        'message' => '',
        'project' => '',
    ];

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
        // Load all users as potential supervisors (any user can be a supervisor)
        $this->supervisors = User::orderBy('name')->get();
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
                'message' => 'User archived successfully',
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

    public function openDeleteConfirmationModal($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->showDeleteConfirmationModal = true;
    }

    public function confirmArchiveUser()
    {
        try {
            if (!$this->selectedUser) {
                throw new \Exception('User not found');
            }
            
            $this->userService->deleteUser($this->selectedUser->id);
            
            $this->dispatch('toast', [
                'message' => 'User archived successfully',
                'variant' => 'success'
            ]);
            
            $this->closeDeleteConfirmationModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function confirmPermanentDelete()
    {
        try {
            if (!$this->selectedUser) {
                throw new \Exception('User not found');
            }
            
            $userName = $this->selectedUser->name;
            
            $this->userService->permanentDeleteUser($this->selectedUser->id);
            
            $this->dispatch('toast', [
                'message' => "User '{$userName}' and all associated records have been permanently deleted",
                'variant' => 'success'
            ]);
            
            $this->closeDeleteConfirmationModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeDeleteConfirmationModal()
    {
        $this->showDeleteConfirmationModal = false;
        $this->selectedUser = null;
    }

    public function unarchiveUser($userId)
    {
        try {
            $this->userService->unarchiveUser($userId);
            
            $this->dispatch('toast', [
                'message' => 'User unarchived successfully',
                'variant' => 'success'
            ]);
            
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
            $supervisorId = $this->selectedSupervisor;
            
            // Check if trying to assign a user as their own supervisor
            if (in_array($supervisorId, $this->selectedUserIds)) {
                $this->dispatch('toast', [
                    'message' => 'A user cannot be their own supervisor',
                    'variant' => 'danger'
                ]);
                return;
            }
            
            // Attach supervisor to selected users (many-to-many)
            foreach ($this->selectedUserIds as $userId) {
                $user = User::find($userId);
                
                // Check if this would create a circular relationship
                if ($this->wouldCreateCircularRelationship($userId, $supervisorId)) {
                    $userName = $user->name;
                    $this->dispatch('toast', [
                        'message' => "Cannot assign supervisor to {$userName}: This would create a circular supervisory relationship",
                        'variant' => 'danger'
                    ]);
                    continue;
                }
                
                // Attach supervisor if not already attached
                if (!$user->supervisors()->where('supervisor_id', $supervisorId)->exists()) {
                    $user->supervisors()->attach($supervisorId);
                }
            }
            
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
        try {
            $user = User::with('department')->findOrFail($userId);
            
            $this->changeDepartmentData = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'current_department' => $user->department ? $user->department->name : 'No Department',
                'new_department_id' => $user->department_id ?? '',
            ];
            
            $this->showChangeDepartmentModal = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error loading user: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }
    
    public function changeSupervisor($userId)
    {
        try {
            $user = User::with('supervisors')->findOrFail($userId);
            
            $primarySupervisor = null;
            $secondarySupervisor = null;
            
            foreach ($user->supervisors as $supervisor) {
                if ($supervisor->pivot->supervisor_type === 'primary') {
                    $primarySupervisor = $supervisor;
                } elseif ($supervisor->pivot->supervisor_type === 'secondary') {
                    $secondarySupervisor = $supervisor;
                }
            }
            
            $this->changeSupervisorData = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'current_primary_supervisor' => $primarySupervisor ? $primarySupervisor->name : 'No Primary Supervisor',
                'current_secondary_supervisor' => $secondarySupervisor ? $secondarySupervisor->name : 'No Secondary Supervisor',
                'new_primary_supervisor_id' => $primarySupervisor ? $primarySupervisor->id : '',
                'new_secondary_supervisor_id' => $secondarySupervisor ? $secondarySupervisor->id : '',
            ];
            
            $this->showChangeSupervisorModal = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error loading user: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }
    
    /**
     * Check if assigning a supervisor would create a circular relationship
     * A user cannot be a supervisor of their own supervisor
     */
    private function wouldCreateCircularRelationship($userId, $supervisorId)
    {
        // Get the user who would become the supervisor
        $supervisor = User::with('supervisors')->find($supervisorId);
        
        if (!$supervisor) {
            return false;
        }
        
        // Check if the user is already supervising the would-be supervisor (directly)
        $isSupervisorOfSupervisor = $supervisor->supervisors()
            ->where('supervisor_id', $userId)
            ->exists();
            
        if ($isSupervisorOfSupervisor) {
            return true;
        }
        
        // Check recursively for indirect relationships
        return $this->isIndirectSupervisor($userId, $supervisorId);
    }
    
    /**
     * Check if userId is an indirect supervisor of potentialSupervisorId
     */
    private function isIndirectSupervisor($userId, $potentialSupervisorId, $visited = [])
    {
        // Prevent infinite loops
        if (in_array($potentialSupervisorId, $visited)) {
            return false;
        }
        
        $visited[] = $potentialSupervisorId;
        
        // Get all supervisors of the potential supervisor
        $supervisors = User::find($potentialSupervisorId)->supervisors;
        
        foreach ($supervisors as $supervisor) {
            // If we find the user as a supervisor, we have a circular relationship
            if ($supervisor->id === $userId) {
                return true;
            }
            
            // Recursively check this supervisor's supervisors
            if ($this->isIndirectSupervisor($userId, $supervisor->id, $visited)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function ipRestriction($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            $this->ipRestrictionData = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'ip_address' => $user->allowed_ip ?? '',
                'restriction_type' => 'allow',
            ];
            
            $this->showIpRestrictionModal = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error loading user: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }
    
    public function updatePassword($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            $this->updatePasswordData = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'new_password' => '',
                'confirm_password' => '',
            ];
            
            $this->showUpdatePasswordModal = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error loading user: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }
    
    public function updateDesignation($userId)
    {
        try {
            $user = User::with('designation')->findOrFail($userId);
            
            $this->updateDesignationData = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'current_designation' => $user->designation ? $user->designation->name : 'No Designation',
                'new_designation_id' => $user->designation_id ?? '',
            ];
            
            $this->showUpdateDesignationModal = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error loading user: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }
    
    public function lastInTime($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            // Get the last punch in record (assuming you have an Attendance model)
            // Adjust this query based on your actual database structure
            $lastPunchIn = \DB::table('attendances')
                ->where('user_id', $userId)
                ->whereNotNull('punch_in')
                ->orderBy('punch_in', 'desc')
                ->first();

            $this->lastInTimeData = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'last_punch_in' => $lastPunchIn ? $lastPunchIn->punch_in : 'No punch in record',
                'task' => $lastPunchIn->task ?? 'N/A',
                'message' => $lastPunchIn->message ?? 'N/A',
                'project' => $lastPunchIn->project_id ? \App\Models\Project::find($lastPunchIn->project_id)->name ?? 'N/A' : 'N/A',
            ];
            
            $this->showLastInTimeModal = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error loading last in time: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }
    
    public function autoPunchOut($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            // Format the time properly for the time input (HH:mm)
            $autoTime = $user->auto_punch_out_time 
                ? $user->auto_punch_out_time->format('H:i') 
                : '18:00';
            
            $this->autoPunchOutData = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'auto_punch_out_time' => $autoTime,
            ];
            
            $this->showAutoPunchOutModal = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error loading user: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
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
        \Log::info('🔥 UserList::createUser() called!', [
            'name' => $this->newUser['name'] ?? null,
            'email' => $this->newUser['email'] ?? null,
        ]);
        
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
        
        \Log::info('✅ UserList validation passed!');
        
        try {
            // Use UserService to create user (which will send setup email)
            $userData = [
                'name' => $this->newUser['name'],
                'email' => $this->newUser['email'],
                'phone' => $this->newUser['phone'] ?: null,
                'employee_code' => $this->newUser['employee_code'] ?: null,
                'user_level_id' => $this->newUser['user_level_id'],
                'department_id' => $this->newUser['department_id'],
                'designation_id' => $this->newUser['designation_id'] ?: null,
                'password' => $this->newUser['password'], // UserService will hash it
                'status' => 1, // Active by default
            ];
            
            \Log::info('📧 Calling UserService::createUser() from UserList', [
                'email' => $userData['email']
            ]);
            
            $user = $this->userService->createUser($userData);
            
            \Log::info('✅ User created successfully via UserList', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            $this->dispatch('toast', [
                'message' => 'User created successfully. Account setup email sent!',
                'variant' => 'success'
            ]);
            
            $this->closeAddUserModal();
            $this->resetPage();
        } catch (\Exception $e) {
            \Log::error('❌ Failed to create user in UserList', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('toast', [
                'message' => 'Failed to create user: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openEditUserModal($userId)
    {
        try {
            $user = User::with(['userLevel', 'department', 'designation', 'supervisors', 'projects'])->find($userId);
            
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
                    'primary_supervisor_id' => '',
                    'secondary_supervisor_id' => '',
                    'status' => $user->status,
                    'password' => '',
                    'password_confirmation' => '',
                ];
                
                // Load primary and secondary supervisors
                foreach ($user->supervisors as $supervisor) {
                    if ($supervisor->pivot->supervisor_type === 'primary') {
                        $this->editUser['primary_supervisor_id'] = $supervisor->id;
                    } elseif ($supervisor->pivot->supervisor_type === 'secondary') {
                        $this->editUser['secondary_supervisor_id'] = $supervisor->id;
                    }
                }
                
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
            'primary_supervisor_id' => '',
            'secondary_supervisor_id' => '',
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
            'editUser.primary_supervisor_id' => 'nullable|exists:users,id',
            'editUser.secondary_supervisor_id' => 'nullable|exists:users,id|different:editUser.primary_supervisor_id',
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
            
            // Check if user is trying to be their own supervisor
            if ($this->editUser['primary_supervisor_id'] == $user->id || $this->editUser['secondary_supervisor_id'] == $user->id) {
                $this->dispatch('toast', [
                    'message' => 'A user cannot be their own supervisor',
                    'variant' => 'danger'
                ]);
                return;
            }
            
            // Convert empty strings to null for nullable foreign keys
            $updateData = [
                'name' => $this->editUser['name'],
                'email' => $this->editUser['email'],
                'phone' => $this->editUser['phone'] ?: null,
                'employee_code' => $this->editUser['employee_code'] ?: null,
                'user_level_id' => $this->editUser['user_level_id'],
                'department_id' => $this->editUser['department_id'] ?: null,
                'designation_id' => $this->editUser['designation_id'] ?: null,
                'status' => $this->editUser['status'],
            ];

            // Only update password if it's provided
            if (!empty($this->editUser['password'])) {
                $updateData['password'] = bcrypt($this->editUser['password']);
            }

            $user->update($updateData);

            // Update supervisors using UserService
            $this->userService->assignSupervisor($user->id, [
                'primary' => $this->editUser['primary_supervisor_id'],
                'secondary' => $this->editUser['secondary_supervisor_id'],
            ]);
            
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

    // Save methods for the new modals
    public function saveChangeDepartment()
    {
        $this->validate([
            'changeDepartmentData.new_department_id' => 'required|exists:departments,id',
        ]);

        try {
            $user = User::findOrFail($this->changeDepartmentData['user_id']);
            $user->update(['department_id' => $this->changeDepartmentData['new_department_id']]);

            $this->dispatch('toast', [
                'message' => 'Department changed successfully',
                'variant' => 'success'
            ]);

            $this->closeChangeDepartmentModal();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to change department: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeChangeDepartmentModal()
    {
        $this->showChangeDepartmentModal = false;
        $this->changeDepartmentData = [
            'user_id' => '',
            'user_name' => '',
            'current_department' => '',
            'new_department_id' => '',
        ];
        $this->resetErrorBag();
    }

    public function saveChangeSupervisor()
    {
        $this->validate([
            'changeSupervisorData.new_primary_supervisor_id' => 'nullable|exists:users,id',
            'changeSupervisorData.new_secondary_supervisor_id' => 'nullable|exists:users,id|different:changeSupervisorData.new_primary_supervisor_id',
        ]);

        try {
            $user = User::findOrFail($this->changeSupervisorData['user_id']);
            
            // Check if user is trying to be their own supervisor
            if ($this->changeSupervisorData['new_primary_supervisor_id'] == $user->id || 
                $this->changeSupervisorData['new_secondary_supervisor_id'] == $user->id) {
                $this->dispatch('toast', [
                    'message' => 'A user cannot be their own supervisor',
                    'variant' => 'danger'
                ]);
                return;
            }
            
            // Update supervisors using UserService
            $this->userService->assignSupervisor($user->id, [
                'primary' => $this->changeSupervisorData['new_primary_supervisor_id'],
                'secondary' => $this->changeSupervisorData['new_secondary_supervisor_id'],
            ]);

            $this->dispatch('toast', [
                'message' => 'Supervisors updated successfully',
                'variant' => 'success'
            ]);

            $this->closeChangeSupervisorModal();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to change supervisors: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeChangeSupervisorModal()
    {
        $this->showChangeSupervisorModal = false;
        $this->changeSupervisorData = [
            'user_id' => '',
            'user_name' => '',
            'current_primary_supervisor' => '',
            'current_secondary_supervisor' => '',
            'new_primary_supervisor_id' => '',
            'new_secondary_supervisor_id' => '',
        ];
        $this->resetErrorBag();
    }
    
    public function resetChangeSupervisorData()
    {
        $this->changeSupervisorData = [
            'user_id' => '',
            'user_name' => '',
            'current_supervisor' => '',
            'new_supervisor_ids' => [],
        ];
        $this->resetErrorBag();
    }

    public function saveIpRestriction()
    {
        $this->validate([
            'ipRestrictionData.ip_address' => 'nullable|ip',
        ]);

        try {
            $user = User::findOrFail($this->ipRestrictionData['user_id']);
            $user->update(['allowed_ip' => $this->ipRestrictionData['ip_address'] ?: null]);

            $this->dispatch('toast', [
                'message' => 'IP restriction updated successfully',
                'variant' => 'success'
            ]);

            $this->closeIpRestrictionModal();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to update IP restriction: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeIpRestrictionModal()
    {
        $this->showIpRestrictionModal = false;
        $this->ipRestrictionData = [
            'user_id' => '',
            'user_name' => '',
            'ip_address' => '',
            'restriction_type' => 'allow',
        ];
        $this->resetErrorBag();
    }

    public function saveUpdatePassword()
    {
        $this->validate([
            'updatePasswordData.new_password' => 'required|string|min:6',
            'updatePasswordData.confirm_password' => 'required|same:updatePasswordData.new_password',
        ]);

        try {
            $user = User::findOrFail($this->updatePasswordData['user_id']);
            $user->update(['password' => bcrypt($this->updatePasswordData['new_password'])]);

            $this->dispatch('toast', [
                'message' => 'Password updated successfully',
                'variant' => 'success'
            ]);

            $this->closeUpdatePasswordModal();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to update password: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeUpdatePasswordModal()
    {
        $this->showUpdatePasswordModal = false;
        $this->updatePasswordData = [
            'user_id' => '',
            'user_name' => '',
            'new_password' => '',
            'confirm_password' => '',
        ];
        $this->resetErrorBag();
    }

    public function saveUpdateDesignation()
    {
        $this->validate([
            'updateDesignationData.new_designation_id' => 'nullable|exists:designations,id',
        ]);

        try {
            $user = User::findOrFail($this->updateDesignationData['user_id']);
            $user->update(['designation_id' => $this->updateDesignationData['new_designation_id'] ?: null]);

            $this->dispatch('toast', [
                'message' => 'Designation updated successfully',
                'variant' => 'success'
            ]);

            $this->closeUpdateDesignationModal();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to update designation: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeUpdateDesignationModal()
    {
        $this->showUpdateDesignationModal = false;
        $this->updateDesignationData = [
            'user_id' => '',
            'user_name' => '',
            'current_designation' => '',
            'new_designation_id' => '',
        ];
        $this->resetErrorBag();
    }

    public function saveAutoPunchOut()
    {
        $this->validate([
            'autoPunchOutData.auto_punch_out_time' => ['nullable', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
        ], [
            'autoPunchOutData.auto_punch_out_time.regex' => 'The auto punch out time must be a valid time format (HH:MM).',
        ]);

        try {
            $user = User::findOrFail($this->autoPunchOutData['user_id']);
            
            // If auto_punch_out_time is empty, set it to null to remove the setting
            $autoTime = !empty($this->autoPunchOutData['auto_punch_out_time']) 
                ? $this->autoPunchOutData['auto_punch_out_time'] 
                : null;
            
            $user->update(['auto_punch_out_time' => $autoTime]);

            $message = $autoTime 
                ? "Auto punch out time set to {$autoTime}" 
                : 'Auto punch out time removed - user will use global setting';

            $this->dispatch('toast', [
                'message' => $message,
                'variant' => 'success'
            ]);

            $this->closeAutoPunchOutModal();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to update auto punch out time: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function removeAutoPunchOut()
    {
        try {
            $user = User::findOrFail($this->autoPunchOutData['user_id']);
            $user->update(['auto_punch_out_time' => null]);

            $this->dispatch('toast', [
                'message' => 'Auto punch out time removed - user will use global setting',
                'variant' => 'success'
            ]);

            $this->closeAutoPunchOutModal();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to remove auto punch out time: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeAutoPunchOutModal()
    {
        $this->showAutoPunchOutModal = false;
        $this->autoPunchOutData = [
            'user_id' => '',
            'user_name' => '',
            'auto_punch_out_time' => '',
        ];
        $this->resetErrorBag();
    }

    public function closeLastInTimeModal()
    {
        $this->showLastInTimeModal = false;
        $this->lastInTimeData = [
            'user_id' => '',
            'user_name' => '',
            'last_punch_in' => '',
            'task' => '',
            'message' => '',
            'project' => '',
        ];
    }

    protected $listeners = ['user-saved' => '$refresh'];

    public function render()
    {
        $query = User::with(['userLevel', 'department', 'designation', 'supervisors']);

        // Apply user status filter (active/deactivated/archived)
        $query->byStatus($this->userStatusFilter);

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

        // Apply designation filter
        if (!empty($this->designationId)) {
            $query->where('designation_id', $this->designationId);
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
