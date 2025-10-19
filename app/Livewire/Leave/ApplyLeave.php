<?php

namespace App\Livewire\Leave;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Services\LeaveService;
use Livewire\Component;
use Carbon\Carbon;

class ApplyLeave extends Component
{
    public $leaveCategoryId = '';
    public $startDate = '';
    public $endDate = '';
    public $description = '';
    public $isLoading = false;
    public $leaveCategories = [];
    public $leaveBalances = [];
    public $showDialog = false;
    public $statusFilter = '';
    public $totalDays = 0;

    protected LeaveService $leaveService;

    public function boot(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function mount()
    {
        $this->loadLeaveCategories();
        $tomorrow = now()->addDay()->format('Y-m-d');
        $this->startDate = $tomorrow;
        $this->endDate = $tomorrow;
    }

    public function loadLeaveCategories()
    {
        $userGender = auth()->user()->gender;
        
        // Filter leave categories based on user's gender
        $this->leaveCategories = LeaveCategory::orderBy('name')
            ->where(function($query) use ($userGender) {
                $query->where('gender_restriction', 'all')
                      ->orWhere('gender_restriction', $userGender);
            })
            ->get();
        
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

    public function updatedStartDate()
    {
        $this->calculateTotalDays();
    }

    public function updatedEndDate()
    {
        $this->calculateTotalDays();
    }

    public function calculateTotalDays()
    {
        if ($this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
            
            if ($end->gte($start)) {
                $this->totalDays = $start->diffInDays($end) + 1;
            } else {
                $this->totalDays = 0;
            }
        } else {
            $this->totalDays = 0;
        }
    }

    public function openDialog()
    {
        $this->showDialog = true;
    }

    public function closeDialog()
    {
        $this->showDialog = false;
    }

    public function rules()
    {
        return [
            'leaveCategoryId' => 'required|exists:leave_categories,id',
            'startDate' => 'required|date|after:today',
            'endDate' => 'required|date|after_or_equal:startDate',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'leaveCategoryId.required' => 'Please select a leave category',
            'leaveCategoryId.exists' => 'Invalid leave category selected',
            'startDate.required' => 'Please select a start date',
            'startDate.after' => 'Start date must be in the future',
            'endDate.required' => 'Please select an end date',
            'endDate.after_or_equal' => 'End date must be on or after start date',
            'description.max' => 'Description cannot exceed 500 characters',
        ];
    }

    public function submit()
    {
        $this->validate();
        
        $this->isLoading = true;
        
        try {
            // Apply leave for each day in the range
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
            
            $this->leaveService->applyLeaveRange(auth()->id(), [
                'leave_category_id' => $this->leaveCategoryId,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'description' => $this->description,
            ]);
            
            $this->dispatch('toast', [
                'message' => 'Leave application submitted successfully!',
                'variant' => 'success'
            ]);
            
            // Reset form
            $this->reset(['leaveCategoryId', 'description']);
            $tomorrow = now()->addDay()->format('Y-m-d');
            $this->startDate = $tomorrow;
            $this->endDate = $tomorrow;
            $this->totalDays = 0;
            
            // Close dialog
            $this->showDialog = false;
            
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
        $query = Leave::where('user_id', auth()->id())
            ->with(['category', 'status'])
            ->orderBy('created_at', 'desc');

        if ($this->statusFilter) {
            $query->whereHas('status', function ($q) {
                $q->where('name', strtolower($this->statusFilter));
            });
        }

        $leaves = $query->get();

        return view('livewire.leave.apply-leave', [
            'leaves' => $leaves,
        ])->layout('components.layouts.app', ['title' => 'Apply Leave']);
    }
}
