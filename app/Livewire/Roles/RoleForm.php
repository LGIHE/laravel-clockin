<?php

namespace App\Livewire\Roles;

use App\Models\Permission;
use App\Models\UserLevel;
use Illuminate\Support\Str;
use Livewire\Component;

class RoleForm extends Component
{
    public $roleId;
    public $name = '';
    public $selectedPermissions = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'selectedPermissions' => 'array',
    ];

    public function mount($roleId = null)
    {
        if ($roleId) {
            $role = UserLevel::with('permissions')->findOrFail($roleId);
            $this->roleId = $role->id;
            $this->name = $role->name;
            $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        }
    }

    public function getPermissionsByCategoryProperty()
    {
        $permissions = Permission::orderBy('category')->orderBy('name')->get();
        return $permissions->groupBy('category');
    }

    public function toggleCategory($category)
    {
        $categoryPermissions = $this->permissionsByCategory[$category]->pluck('id')->toArray();
        $allSelected = empty(array_diff($categoryPermissions, $this->selectedPermissions));

        if ($allSelected) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $categoryPermissions));
        } else {
            $this->selectedPermissions = array_values(array_unique(array_merge($this->selectedPermissions, $categoryPermissions)));
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->roleId) {
            $role = UserLevel::findOrFail($this->roleId);
            $role->update(['name' => $this->name]);
            $message = 'Role updated successfully.';
        } else {
            $role = UserLevel::create([
                'id' => (string) Str::uuid(),
                'name' => $this->name,
            ]);
            $message = 'Role created successfully.';
        }

        $role->syncPermissions($this->selectedPermissions);

        session()->flash('success', $message);
        return redirect()->route('roles.index');
    }

    public function render()
    {
        return view('livewire.roles.role-form')
            ->layout('components.layouts.app', ['title' => 'User Role Form']);
    }
}
