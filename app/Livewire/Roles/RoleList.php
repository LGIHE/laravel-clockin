<?php

namespace App\Livewire\Roles;

use App\Models\UserLevel;
use Livewire\Component;
use Livewire\WithPagination;

class RoleList extends Component
{
    use WithPagination;

    public $search = '';
    public $showDeleteModal = false;
    public $roleToDelete = null;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($roleId)
    {
        $role = UserLevel::find($roleId);
        
        if ($role->users()->count() > 0) {
            session()->flash('error', 'Cannot delete role with assigned users.');
            return;
        }

        $this->roleToDelete = $roleId;
        $this->showDeleteModal = true;
    }

    public function deleteRole()
    {
        if ($this->roleToDelete) {
            UserLevel::find($this->roleToDelete)->delete();
            session()->flash('success', 'Role deleted successfully.');
        }

        $this->showDeleteModal = false;
        $this->roleToDelete = null;
    }

    public function render()
    {
        $roles = UserLevel::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->withCount('users')
            ->paginate(10);

        return view('livewire.roles.role-list', [
            'roles' => $roles,
        ]);
    }
}
