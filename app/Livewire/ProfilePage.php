<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilePage extends Component
{
    // Basic Information
    public $name;
    public $email;
    public $phone;
    public $employee_code;
    public $password;
    public $password_confirmation;

    // Role & Organization (read-only)
    public $role;
    public $status;
    public $department;
    public $designation;
    public $supervisors;
    public $projects;

    public function mount()
    {
        // Load user with all required relationships
        $user = Auth::user()->load(['userLevel', 'department', 'designation', 'supervisors', 'projects']);
        
        // Basic Information
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->employee_code = $user->employee_code;
        
        // Role & Organization
        $this->role = $user->userLevel?->name;
        $this->status = $user->status == 1 ? 'Active' : 'Inactive';
        $this->department = $user->department?->name;
        $this->designation = $user->designation?->name;
        $this->supervisors = $user->supervisors;
        $this->projects = $user->projects;
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        
        if ($this->password) {
            $user->password = Hash::make($this->password);
        }
        
        $user->save();
        
        session()->flash('success', 'Profile updated successfully!');
        
        // Clear password fields
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function render()
    {
        return view('livewire.profile-page');
    }
}
