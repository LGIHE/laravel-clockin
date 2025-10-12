<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\UserLevel;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Project;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserForm extends Component
{
    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $userLevelId = '';
    public $departmentId = '';
    public $designationId = '';
    public $selectedSupervisors = [];
    public $selectedProjects = [];
    public $status = 1;
    
    public $isEditMode = false;
    public $showPassword = false;
    
    public $userLevels = [];
    public $departments = [];
    public $designations = [];
    public $supervisors = [];
    public $projects = [];

    protected UserService $userService;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function mount($userId = null)
    {
        $this->loadFormData();
        
        if ($userId) {
            $this->isEditMode = true;
            $this->userId = $userId;
            $this->loadUser($userId);
        }
    }

    public function loadFormData()
    {
        $this->userLevels = UserLevel::orderBy('name')->get();
        $this->departments = Department::orderBy('name')->get();
        $this->designations = Designation::orderBy('name')->get();
        $this->projects = Project::where('status', 'ACTIVE')->orderBy('name')->get();
        
        // Load all active users as potential supervisors (excluding the current user)
        $this->supervisors = User::where('status', 1)
            ->when($this->userId, function($query) {
                // Exclude the current user from the supervisor list
                $query->where('id', '!=', $this->userId);
            })
            ->orderBy('name')
            ->get();
    }

    public function loadUser($userId)
    {
        try {
            $user = User::with(['userLevel', 'department', 'designation', 'supervisors'])->findOrFail($userId);
            
            $this->name = $user->name;
            $this->email = $user->email;
            $this->userLevelId = $user->user_level_id;
            $this->departmentId = $user->department_id;
            $this->designationId = $user->designation_id;
            $this->selectedSupervisors = $user->supervisors->pluck('id')->toArray();
            $this->status = $user->status;
            
            // Load project assignments
            if ($user->project_id) {
                $projectIds = json_decode($user->project_id, true);
                $this->selectedProjects = is_array($projectIds) ? $projectIds : [];
            }
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'User not found',
                'variant' => 'danger'
            ]);
            
            return redirect()->route('users.index');
        }
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userId)->whereNull('deleted_at')
            ],
            'userLevelId' => 'required|exists:user_levels,id',
            'departmentId' => 'nullable|exists:departments,id',
            'designationId' => 'nullable|exists:designations,id',
            'selectedSupervisors' => 'nullable|array',
            'selectedSupervisors.*' => 'exists:users,id',
            'selectedProjects' => 'nullable|array',
            'selectedProjects.*' => 'exists:projects,id',
            'status' => 'required|in:0,1',
        ];

        if (!$this->isEditMode) {
            $rules['password'] = 'required|string|min:6|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:6|confirmed';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'userLevelId.required' => 'User role is required',
            'userLevelId.exists' => 'Selected role is invalid',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'user_level_id' => $this->userLevelId,
                'department_id' => $this->departmentId ?: null,
                'designation_id' => $this->designationId ?: null,
                'status' => $this->status,
                'project_ids' => $this->selectedProjects,
            ];

            if ($this->isEditMode) {
                // Update existing user
                if (!empty($this->password)) {
                    $data['password'] = $this->password;
                }
                
                $user = $this->userService->updateUser($this->userId, $data);
                
                // Update supervisors
                $this->userService->assignSupervisor($this->userId, $this->selectedSupervisors);
                
                $message = 'User updated successfully';
            } else {
                // Create new user
                $data['password'] = $this->password;
                
                $user = $this->userService->createUser($data);
                
                // Assign supervisors if provided
                if (!empty($this->selectedSupervisors)) {
                    $this->userService->assignSupervisor($user->id, $this->selectedSupervisors);
                }
                
                $message = 'User created successfully';
            }

            $this->dispatch('toast', [
                'message' => $message,
                'variant' => 'success'
            ]);

            $this->dispatch('user-saved');
            
            // Reset form if creating new user
            if (!$this->isEditMode) {
                $this->reset([
                    'name', 'email', 'password', 'password_confirmation',
                    'userLevelId', 'departmentId', 'designationId',
                    'selectedSupervisors', 'selectedProjects', 'status'
                ]);
                $this->status = 1;
            }

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function toggleProjectSelection($projectId)
    {
        if (in_array($projectId, $this->selectedProjects)) {
            $this->selectedProjects = array_values(array_diff($this->selectedProjects, [$projectId]));
        } else {
            $this->selectedProjects[] = $projectId;
        }
    }

    public function cancel()
    {
        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.users.user-form');
    }
}
