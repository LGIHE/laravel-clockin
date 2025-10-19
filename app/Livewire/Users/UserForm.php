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
    public $gender = '';
    public $password = '';
    public $password_confirmation = '';
    public $userLevelId = '';
    public $departmentId = '';
    public $designationId = '';
    public $primarySupervisorId = '';
    public $secondarySupervisorId = '';
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
        // Load user levels - exclude Admin role for non-admin users
        $this->userLevels = UserLevel::orderBy('name')
            ->when(auth()->user()->role !== 'ADMIN', function($query) {
                // Non-admin users cannot see or assign the Admin role
                $query->where('name', '!=', 'Admin');
            })
            ->get();
            
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
            $this->gender = $user->gender;
            $this->userLevelId = $user->user_level_id;
            $this->departmentId = $user->department_id;
            $this->designationId = $user->designation_id;
            
            // Load primary and secondary supervisors
            foreach ($user->supervisors as $supervisor) {
                if ($supervisor->pivot->supervisor_type === 'primary') {
                    $this->primarySupervisorId = $supervisor->id;
                } elseif ($supervisor->pivot->supervisor_type === 'secondary') {
                    $this->secondarySupervisorId = $supervisor->id;
                }
            }
            
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
            'gender' => 'required|in:male,female',
            'userLevelId' => [
                'required',
                'exists:user_levels,id',
                function ($attribute, $value, $fail) {
                    // Only admins can assign the Admin role
                    if (auth()->user()->role !== 'ADMIN') {
                        $userLevel = UserLevel::find($value);
                        if ($userLevel && strtoupper($userLevel->name) === 'ADMIN') {
                            $fail('You do not have permission to assign the Admin role.');
                        }
                    }
                },
            ],
            'departmentId' => 'nullable|exists:departments,id',
            'designationId' => 'nullable|exists:designations,id',
            'primarySupervisorId' => 'nullable|exists:users,id',
            'secondarySupervisorId' => 'nullable|exists:users,id|different:primarySupervisorId',
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
            'gender.required' => 'Gender is required',
            'gender.in' => 'Please select a valid gender',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'userLevelId.required' => 'User role is required',
            'userLevelId.exists' => 'Selected role is invalid',
            'secondarySupervisorId.different' => 'Secondary supervisor must be different from primary supervisor',
        ];
    }

    public function save()
    {
        \Log::info('🔥 UserForm save() method called!', [
            'isEditMode' => $this->isEditMode,
            'name' => $this->name,
            'email' => $this->email,
            'has_password' => !empty($this->password)
        ]);
        
        $this->validate();
        
        \Log::info('✅ Validation passed!');

        try {
            // Additional admin role protection
            $finalUserLevelId = $this->userLevelId;
            
            // If not admin, apply additional checks
            if (auth()->user()->role !== 'ADMIN') {
                $selectedLevel = UserLevel::find($this->userLevelId);
                
                // If trying to assign admin role, default to User role instead
                if ($selectedLevel && strtoupper($selectedLevel->name) === 'ADMIN') {
                    $userRole = UserLevel::where('name', 'User')->first();
                    $finalUserLevelId = $userRole ? $userRole->id : $this->userLevelId;
                    
                    \Log::warning('Non-admin attempted to assign admin role, defaulted to User', [
                        'actor' => auth()->user()->id,
                        'target' => $this->userId ?? 'new user'
                    ]);
                }
                
                // If editing, check if user is currently admin
                if ($this->isEditMode) {
                    $existingUser = User::find($this->userId);
                    if ($existingUser && $existingUser->userLevel && strtoupper($existingUser->userLevel->name) === 'ADMIN') {
                        // Non-admin cannot change admin user's role - keep it as admin
                        $finalUserLevelId = $existingUser->user_level_id;
                        
                        \Log::warning('Non-admin attempted to change admin user role, skipped', [
                            'actor' => auth()->user()->id,
                            'target' => $this->userId
                        ]);
                    }
                }
            }
            
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'gender' => $this->gender,
                'user_level_id' => $finalUserLevelId,
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
                $this->userService->assignSupervisor($this->userId, [
                    'primary' => $this->primarySupervisorId,
                    'secondary' => $this->secondarySupervisorId,
                ]);
                
                $message = 'User updated successfully';
            } else {
                // Create new user
                \Log::info('UserForm: Creating new user via UI', [
                    'name' => $this->name,
                    'email' => $this->email,
                    'user_level_id' => $finalUserLevelId
                ]);
                
                $data['password'] = $this->password;
                
                $user = $this->userService->createUser($data);
                
                \Log::info('UserForm: User created successfully via UI', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                // Assign supervisors if provided
                if (!empty($this->primarySupervisorId) || !empty($this->secondarySupervisorId)) {
                    $this->userService->assignSupervisor($user->id, [
                        'primary' => $this->primarySupervisorId,
                        'secondary' => $this->secondarySupervisorId,
                    ]);
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
                    'name', 'email', 'gender', 'password', 'password_confirmation',
                    'userLevelId', 'departmentId', 'designationId',
                    'primarySupervisorId', 'secondarySupervisorId', 'selectedProjects', 'status'
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
        return view('livewire.users.user-form')
            ->layout('components.layouts.app', ['title' => 'User Form']);
    }
}
