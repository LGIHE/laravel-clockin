<?php

namespace App\Livewire\LeaveCategories;

use App\Models\LeaveCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class LeaveCategoryList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $perPage = 15;
    
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    public $categoryId = null;
    public $name = '';
    public $max_in_year = '';
    
    public $selectedCategory = null;
    public $isAdmin = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'max_in_year' => 'required|integer|min:1|max:365',
    ];

    protected $messages = [
        'name.required' => 'Leave category name is required',
        'name.max' => 'Leave category name cannot exceed 255 characters',
        'max_in_year.required' => 'Maximum days per year is required',
        'max_in_year.integer' => 'Maximum days must be a number',
        'max_in_year.min' => 'Maximum days must be at least 1',
        'max_in_year.max' => 'Maximum days cannot exceed 365',
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

    public function createCategory()
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
            $exists = LeaveCategory::where('name', $this->name)->exists();
            if ($exists) {
                $this->addError('name', 'A leave category with this name already exists');
                return;
            }

            LeaveCategory::create([
                'id' => Str::uuid()->toString(),
                'name' => $this->name,
                'max_in_year' => $this->max_in_year,
            ]);

            $this->dispatch('toast', [
                'message' => 'Leave category created successfully',
                'variant' => 'success'
            ]);

            $this->closeCreateModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error creating leave category: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openEditModal($categoryId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $category = LeaveCategory::find($categoryId);
        
        if ($category) {
            $this->categoryId = $category->id;
            $this->name = $category->name;
            $this->max_in_year = $category->max_in_year;
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateCategory()
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
            $category = LeaveCategory::findOrFail($this->categoryId);

            // Check for duplicate name (excluding current category)
            $exists = LeaveCategory::where('name', $this->name)
                ->where('id', '!=', $this->categoryId)
                ->exists();
            
            if ($exists) {
                $this->addError('name', 'A leave category with this name already exists');
                return;
            }

            $category->update([
                'name' => $this->name,
                'max_in_year' => $this->max_in_year,
            ]);

            $this->dispatch('toast', [
                'message' => 'Leave category updated successfully',
                'variant' => 'success'
            ]);

            $this->closeEditModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error updating leave category: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function confirmDelete($categoryId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->selectedCategory = LeaveCategory::withCount('leaves')->find($categoryId);
        
        if ($this->selectedCategory) {
            $this->showDeleteModal = true;
        }
    }

    public function deleteCategory()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        try {
            if (!$this->selectedCategory) {
                throw new \Exception('Leave category not found');
            }

            // Check if category has active leaves
            if ($this->selectedCategory->leaves_count > 0) {
                $this->dispatch('toast', [
                    'message' => 'Cannot delete leave category with active leaves',
                    'variant' => 'danger'
                ]);
                $this->closeDeleteModal();
                return;
            }

            $this->selectedCategory->delete();

            $this->dispatch('toast', [
                'message' => 'Leave category deleted successfully',
                'variant' => 'success'
            ]);

            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error deleting leave category: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedCategory = null;
    }

    private function resetForm()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->max_in_year = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = LeaveCategory::withCount('leaves');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortOrder);

        // Paginate results
        $categories = $query->paginate($this->perPage);

        return view('livewire.leave-categories.leave-category-list', [
            'categories' => $categories
        ]);
    }
}
