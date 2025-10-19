<?php

namespace App\Livewire\Designations;

use App\Models\Designation;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class DesignationList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $perPage = 15;
    
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    public $designationId = null;
    public $name = '';
    public $editName = '';
    
    public $selectedDesignation = null;
    public $isAdmin = false;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    protected $messages = [
        'name.required' => 'Designation name is required',
        'name.max' => 'Designation name cannot exceed 255 characters',
    ];

    public function mount()
    {
        $this->isAdmin = strtolower(auth()->user()->userLevel->name) === 'admin';
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

    public function createDesignation()
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
            $exists = Designation::where('name', $this->name)->exists();
            if ($exists) {
                $this->addError('name', 'A designation with this name already exists');
                return;
            }

            Designation::create([
                'id' => Str::uuid()->toString(),
                'name' => $this->name,
            ]);

            $this->dispatch('toast', [
                'message' => 'Designation created successfully',
                'variant' => 'success'
            ]);

            $this->closeCreateModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error creating designation: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openEditModal($designationId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $designation = Designation::find($designationId);
        
        if ($designation) {
            $this->designationId = $designation->id;
            $this->editName = $designation->name;
            $this->showEditModal = true;
        } else {
            $this->dispatch('toast', [
                'message' => 'Designation not found',
                'variant' => 'danger'
            ]);
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateDesignation()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->validate([
            'editName' => 'required|string|max:255',
        ]);

        try {
            $designation = Designation::findOrFail($this->designationId);

            // Check for duplicate name (excluding current designation)
            $exists = Designation::where('name', $this->editName)
                ->where('id', '!=', $this->designationId)
                ->exists();
            
            if ($exists) {
                $this->addError('editName', 'A designation with this name already exists');
                return;
            }

            $designation->update([
                'name' => $this->editName,
            ]);

            $this->dispatch('toast', [
                'message' => 'Designation updated successfully',
                'variant' => 'success'
            ]);

            $this->closeEditModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error updating designation: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function confirmDelete($designationId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->selectedDesignation = Designation::withCount('users')->find($designationId);
        
        if ($this->selectedDesignation) {
            $this->showDeleteModal = true;
        } else {
            $this->dispatch('toast', [
                'message' => 'Designation not found',
                'variant' => 'danger'
            ]);
        }
    }

    public function deleteDesignation()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        try {
            if (!$this->selectedDesignation) {
                throw new \Exception('Designation not found');
            }

            // Check if designation has active users
            if ($this->selectedDesignation->users_count > 0) {
                $this->dispatch('toast', [
                    'message' => 'Cannot delete designation with active users',
                    'variant' => 'danger'
                ]);
                $this->closeDeleteModal();
                return;
            }

            $this->selectedDesignation->delete();

            $this->dispatch('toast', [
                'message' => 'Designation deleted successfully',
                'variant' => 'success'
            ]);

            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error deleting designation: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedDesignation = null;
    }

    private function resetForm()
    {
        $this->designationId = null;
        $this->name = '';
        $this->editName = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Designation::withCount('users');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortOrder);

        // Paginate results
        $designations = $query->paginate($this->perPage);

        return view('livewire.designations.designation-list', [
            'designations' => $designations
        ])->layout('components.layouts.app');
    }
}
