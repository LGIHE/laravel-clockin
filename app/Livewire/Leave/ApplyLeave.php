<?php

namespace App\Livewire\Leave;

use App\Models\LeaveCategory;
use App\Services\LeaveService;
use Livewire\Component;

class ApplyLeave extends Component
{
    public $leaveCategoryId = '';
    public $date = '';
    public $description = '';
    public $isLoading = false;
    public $leaveCategories = [];
    public $leaveBalances = [];

    protected LeaveService $leaveService;

    public function boot(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function mount()
    {
        $this->loadLeaveCategories();
        $this->date = now()->addDay()->format('Y-m-d'); // Default to tomorrow
    }

    public function loadLeaveCategories()
    {
        $this->leaveCategories = LeaveCategory::orderBy('name')->get();
        
        // Load leave balances for current year
        $currentYear = now()->year;
        foreach ($this->leaveCategories as $category) {
            try {
                $this->leaveBalances[$category->id] = $this->leaveService->getLeaveBalance(
                    auth()->id(),
                    $category->id,
                    $currentYear
                );
            } catch (\Exception $e) {
                $this->leaveBalances[$category->id] = [
                    'category' => $category->name,
                    'total' => $category->max_in_year,
                    'used' => 0,
                    'remaining' => $category->max_in_year,
                ];
            }
        }
    }

    public function rules()
    {
        return [
            'leaveCategoryId' => 'required|exists:leave_categories,id',
            'date' => 'required|date|after:today',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'leaveCategoryId.required' => 'Please select a leave category',
            'leaveCategoryId.exists' => 'Invalid leave category selected',
            'date.required' => 'Please select a date',
            'date.after' => 'Leave date must be in the future',
            'description.max' => 'Description cannot exceed 500 characters',
        ];
    }

    public function submit()
    {
        $this->validate();
        
        $this->isLoading = true;
        
        try {
            $this->leaveService->applyLeave(auth()->id(), [
                'leave_category_id' => $this->leaveCategoryId,
                'date' => $this->date,
                'description' => $this->description,
            ]);
            
            $this->dispatch('toast', [
                'message' => 'Leave application submitted successfully!',
                'variant' => 'success'
            ]);
            
            // Reset form
            $this->reset(['leaveCategoryId', 'description']);
            $this->date = now()->addDay()->format('Y-m-d');
            
            // Reload balances
            $this->loadLeaveCategories();
            
            // Emit event to refresh leave list
            $this->dispatch('leave-applied');
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.leave.apply-leave');
    }
}
