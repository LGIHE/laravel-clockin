<?php

namespace App\Services;

use App\Models\CompensationLeaveRequest;
use App\Models\User;
use Illuminate\Support\Str;

class CompensationLeaveService
{
    protected LeaveBalanceService $leaveBalanceService;
    protected NotificationService $notificationService;

    public function __construct(
        LeaveBalanceService $leaveBalanceService,
        NotificationService $notificationService
    ) {
        $this->leaveBalanceService = $leaveBalanceService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create a compensation leave request
     */
    public function createRequest(string $userId, array $data): CompensationLeaveRequest
    {
        $request = CompensationLeaveRequest::create([
            'id' => Str::uuid()->toString(),
            'user_id' => $userId,
            'work_date' => $data['work_date'],
            'work_type' => $data['work_type'],
            'days_requested' => $data['days_requested'],
            'description' => $data['description'] ?? null,
            'status' => 'pending',
        ]);

        // Notify primary supervisor
        $user = User::with('primarySupervisor')->find($userId);
        if ($user && $user->primarySupervisor) {
            $supervisor = $user->primarySupervisor()->first();
            if ($supervisor) {
                $this->notificationService->notifyCompensationLeaveRequest($request, $user, $supervisor);
            }
        }

        return $request->load(['user', 'supervisorApprover', 'hrEffector']);
    }

    /**
     * Supervisor approves the request
     */
    public function supervisorApprove(string $requestId, string $supervisorId): CompensationLeaveRequest
    {
        $request = CompensationLeaveRequest::findOrFail($requestId);

        if ($request->status !== 'pending') {
            throw new \Exception('Only pending requests can be approved by supervisor');
        }

        $request->update([
            'status' => 'supervisor_approved',
            'supervisor_approved_by' => $supervisorId,
            'supervisor_approved_at' => now(),
        ]);

        // Notify HR users
        $hrUsers = User::whereHas('userLevel', function ($query) {
            $query->where('name', 'LIKE', '%human%resource%')
                  ->orWhere('name', 'LIKE', '%hr%');
        })->get();

        foreach ($hrUsers as $hrUser) {
            $this->notificationService->notifyCompensationLeaveForHR($request, $hrUser);
        }

        // Notify requester
        $this->notificationService->notifyCompensationLeaveSupervisorApproved($request);

        return $request->load(['user', 'supervisorApprover', 'hrEffector']);
    }

    /**
     * HR effects the request (adds days to balance)
     */
    public function hrEffect(string $requestId, string $hrUserId): CompensationLeaveRequest
    {
        $request = CompensationLeaveRequest::findOrFail($requestId);

        if ($request->status !== 'supervisor_approved') {
            throw new \Exception('Request must be supervisor approved before HR can effect it');
        }

        $request->update([
            'status' => 'hr_effected',
            'hr_effected_by' => $hrUserId,
            'hr_effected_at' => now(),
        ]);

        // Add compensation days to user's balance
        $this->leaveBalanceService->addCompensationDays(
            $request->user_id,
            $request->days_requested
        );

        // Notify requester
        $this->notificationService->notifyCompensationLeaveEffected($request);

        return $request->load(['user', 'supervisorApprover', 'hrEffector']);
    }

    /**
     * Reject the request
     */
    public function reject(string $requestId, string $rejectorId, string $reason): CompensationLeaveRequest
    {
        $request = CompensationLeaveRequest::findOrFail($requestId);

        if (!in_array($request->status, ['pending', 'supervisor_approved'])) {
            throw new \Exception('Cannot reject a request that has already been effected');
        }

        $request->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        // Notify requester
        $this->notificationService->notifyCompensationLeaveRejected($request, $reason);

        return $request->load(['user', 'supervisorApprover', 'hrEffector']);
    }
}
