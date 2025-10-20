<?php

namespace App\Livewire\Leave;

use App\Models\CompensationLeaveRequest;
use App\Models\Holiday;
use App\Services\CompensationLeaveService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class CompensationLeaveRequests extends Component
{
    use WithPagination;

    public $showRequestModal = false;
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $showEffectModal = false;
    
    public $workDate = '';
    public $workType = 'weekend';
    public $daysRequested = 1.0;
    public $description = '';
    
    public $selectedRequest = null;
    public $rejectionReason = '';
    
    public $activeTab = 'my-requests';
    public $statusFilter = '';
    
    public $isHR = false;
    public $isSupervisor = false;

    protected CompensationLeaveService $compensationLeaveService;

    public function boot(CompensationLeaveService $compensationLeaveService)
    {
        $this->compensationLeaveService = $compensationLeaveService;
    }

    public function mount()
    {
        $user = auth()->user();
        $userLevelName = strtolower($user->userLevel->name);
        
        $this->isHR = in_array($userLevelName, ['hr', 'human resource', 'human resources']);
        $this->isSupervisor = in_array($userLevelName, ['supervisor', 'admin', 'super_admin']);
        
        $this->workDate = now()->subDay()->format('Y-m-d');
    }

    public function rules()
    {
        return [
            'workDate' => 'required|date|before_or_equal:today',
            'workType' => 'required|in:weekend,holiday',
            'daysRequested' => 'required|numeric|min:0.5|max:1.5',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function openRequestModal()
    {
        $this->showRequestModal = true;
    }

    public function closeRequestModal()
    {
        $this->showRequestModal = false;
        $this->resetForm();
    }

    public function submitRequest()
    {
        $this->validate();

        try {
            // Validate work date is actually a weekend or holiday
            $date = Carbon::parse($this->workDate);
            
            if ($this->workType === 'weekend') {
                if (!$date->isWeekend()) {
                    throw new \Exception('Selected date is not a weekend');
                }
            } elseif ($this->workType === 'holiday') {
                $isHoliday = Holiday::whereDate('date', $date)->exists();
                if (!$isHoliday) {
                    throw new \Exception('Selected date is not a public holiday');
                }
            }

            $this->compensationLeaveService->createRequest(auth()->id(), [
                'work_date' => $this->workDate,
                'work_type' => $this->workType,
                'days_requested' => $this->daysRequested,
                'description' => $this->description,
            ]);

            $this->dispatch('toast', [
                'message' => 'Compensation leave request submitted successfully',
                'variant' => 'success'
            ]);

            $this->closeRequestModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openApproveModal($requestId)
    {
        $this->selectedRequest = CompensationLeaveRequest::with(['user'])->find($requestId);
        $this->showApproveModal = true;
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->selectedRequest = null;
    }

    public function confirmApprove()
    {
        try {
            $this->compensationLeaveService->supervisorApprove(
                $this->selectedRequest->id,
                auth()->id()
            );

            $this->dispatch('toast', [
                'message' => 'Request approved successfully',
                'variant' => 'success'
            ]);

            $this->closeApproveModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openEffectModal($requestId)
    {
        $this->selectedRequest = CompensationLeaveRequest::with(['user', 'supervisorApprover'])->find($requestId);
        $this->showEffectModal = true;
    }

    public function closeEffectModal()
    {
        $this->showEffectModal = false;
        $this->selectedRequest = null;
    }

    public function confirmEffect()
    {
        try {
            $this->compensationLeaveService->hrEffect(
                $this->selectedRequest->id,
                auth()->id()
            );

            $this->dispatch('toast', [
                'message' => 'Compensation days added to user balance',
                'variant' => 'success'
            ]);

            $this->closeEffectModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openRejectModal($requestId)
    {
        $this->selectedRequest = CompensationLeaveRequest::with(['user'])->find($requestId);
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->selectedRequest = null;
        $this->rejectionReason = '';
    }

    public function confirmReject()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5'
        ]);

        try {
            $this->compensationLeaveService->reject(
                $this->selectedRequest->id,
                auth()->id(),
                $this->rejectionReason
            );

            $this->dispatch('toast', [
                'message' => 'Request rejected',
                'variant' => 'success'
            ]);

            $this->closeRejectModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    private function resetForm()
    {
        $this->workDate = now()->subDay()->format('Y-m-d');
        $this->workType = 'weekend';
        $this->daysRequested = 1.0;
        $this->description = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $myRequests = CompensationLeaveRequest::where('user_id', auth()->id())
            ->with(['supervisorApprover', 'hrEffector'])
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $pendingApprovals = collect();
        $pendingHRActions = collect();

        if ($this->isSupervisor) {
            // Get requests from supervised users
            $supervisedUserIds = auth()->user()->supervisedUsers()->pluck('user_id');
            
            $pendingApprovals = CompensationLeaveRequest::whereIn('user_id', $supervisedUserIds)
                ->where('status', 'pending')
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        if ($this->isHR) {
            $pendingHRActions = CompensationLeaveRequest::where('status', 'supervisor_approved')
                ->with(['user', 'supervisorApprover'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('livewire.leave.compensation-leave-requests', [
            'myRequests' => $myRequests,
            'pendingApprovals' => $pendingApprovals,
            'pendingHRActions' => $pendingHRActions,
        ])->layout('components.layouts.app', ['title' => 'Compensation Leave Requests']);
    }
}
