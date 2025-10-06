<?php

namespace App\Livewire\Departments;

use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class DepartmentList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $perPage = 15;
    
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    public $departmentId = null;
    public $name = '';
    public $description = '';
    
    public $selectedDepartment = null;
    public $isAdmin = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'name.required' => 'Department name is required',
        'name.max' => 'Department name cannot exceed 255 characters',
        'description.max' => 'Description cannot exceed 500 characters',
    ];

    public function mount()
    {
        $this->isAdmin = auth()->user()->userLevel->name === 'admin';
    }

    public function updatingSearch()
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

    public function createDepartment()
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
            // Check for duplicate name
            $exists = Department::where('name', $this->name)->exists();
            if ($exists) {
                $this->addError('name', 'A department with this name already exists');
                return;
            }

            Department::create([
                'id' => Str::uuid()->toString(),
                'name' => $this->name,
                'description' => $this->description,
            ]);

            $this->dispatch('toast', [
                'message' => 'Department created successfully',
                'variant' => 'success'
            ]);

            $this->closeCreateModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error creating department: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openEditModal($departmentId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $department = Department::find($departmentId);
        
        if ($department) {
            $this->departmentId = $department->id;
            $this->name = $department->name;
            $this->description = $department->description;
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateDepartment()
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
            $department = Department::findOrFail($this->departmentId);

            // Check for duplicate name (excluding current department)
            $exists = Department::where('name', $this->name)
                ->where('id', '!=', $this->departmentId)
                ->exists();
            
            if ($exists) {
                $this->addError('name', 'A department with this name already exists');
                return;
            }

            $department->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            $this->dispatch('toast', [
                'message' => 'Department updated successfully',
                'variant' => 'success'
            ]);

            $this->closeEditModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error updating department: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function confirmDelete($departmentId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->selectedDepartment = Department::withCount('users')->find($departmentId);
        
        if ($this->selectedDepartment) {
            $this->showDeleteModal = true;
        }
    }

    public function deleteDepartment()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        try {
            if (!$this->selectedDepartment) {
                throw new \Exception('Department not found');
            }

            // Check if department has active users
            if ($this->selectedDepartment->users_count > 0) {
                $this->dispatch('toast', [
                    'message' => 'Cannot delete department with active users',
                    'variant' => 'danger'
                ]);
                $this->closeDeleteModal();
                return;
            }

            $this->selectedDepartment->delete();

            $this->dispatch('toast', [
                'message' => 'Department deleted successfully',
                'variant' => 'success'
            ]);

            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error deleting department: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedDepartment = null;
    }

    private function resetForm()
    {
        $this->departmentId = null;
        $this->name = '';
        $this->description = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Department::withCount('users');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortOrder);

        // Paginate results
        $departments = $query->paginate($this->perPage);

        return view('livewire.departments.department-list', [
            'departments' => $departments
        ]);
    }
}
