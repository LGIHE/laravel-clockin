<?php

namespace App\Services;

use App\Mail\LeaveApprovedMail;
use App\Mail\LeaveRejectedMail;
use App\Mail\LeaveRequestMail;
use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\LeaveStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LeaveService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Apply for leave.
     *
     * @param string $userId
     * @param array $data
     * @return Leave
     * @throws \Exception
     */
    public function applyLeave(string $userId, array $data): Leave
    {
        // Validate leave limit
        $this->validateLeaveLimit($userId, $data['leave_category_id'], date('Y', strtotime($data['date'])));

        // Get pending status
        $pendingStatus = LeaveStatus::where('name', 'pending')->first();
        if (!$pendingStatus) {
            throw new \Exception('Pending status not found');
        }

        // Create leave
        $leave = Leave::create([
            'id' => Str::uuid()->toString(),
            'user_id' => $userId,
            'leave_category_id' => $data['leave_category_id'],
            'leave_status_id' => $pendingStatus->id,
            'date' => $data['date'],
            'description' => $data['description'] ?? null,
        ]);

        // Notify supervisors about new leave request
        try {
            $applicant = User::with('supervisors')->find($userId);
            if ($applicant && $applicant->supervisors) {
                // Send in-app notification
                $this->notificationService->notifyLeaveRequest($leave, $applicant);
                
                // Send email notifications to supervisors
                foreach ($applicant->supervisors as $supervisor) {
                    if ($supervisor && $supervisor->email) {
                        Mail::to($supervisor->email)->send(new LeaveRequestMail($leave, $applicant, $supervisor));
                    }
                }
            }
        } catch (\Exception $e) {
            // Log but don't fail the leave creation if notification fails
            \Log::warning('Failed to send leave request notification', [
                'error' => $e->getMessage(),
                'leave_id' => $leave->id,
                'user_id' => $userId
            ]);
        }

        return $leave->load(['user', 'category', 'status']);
    }

    /**
     * Approve leave.
     *
     * @param string $leaveId
     * @param string $reviewerId
     * @param string|null $comments
     * @return Leave
     * @throws \Exception
     */
    public function approveLeave(string $leaveId, string $reviewerId, ?string $comments = null): Leave
    {
        $leave = Leave::findOrFail($leaveId);

        // Check if leave is pending
        $pendingStatus = LeaveStatus::where('name', 'pending')->first();
        if ($leave->leave_status_id !== $pendingStatus->id) {
            throw new \Exception('Only pending leaves can be approved');
        }

        // Get approved status
        $approvedStatus = LeaveStatus::where('name', 'approved')->first();
        if (!$approvedStatus) {
            throw new \Exception('Approved status not found');
        }

        // Update leave status
        $leave->leave_status_id = $approvedStatus->id;
        $leave->save();

        // Notify the applicant
        $applicant = User::find($leave->user_id);
        $approver = User::find($reviewerId);
        if ($applicant && $approver) {
            // Send in-app notification
            $this->notificationService->notifyLeaveApproved($leave, $applicant, $approver);
            
            // Send email notification
            if ($applicant->email) {
                Mail::to($applicant->email)->send(new LeaveApprovedMail($leave, $applicant, $approver));
            }
        }

        return $leave->load(['user', 'category', 'status']);
    }

    /**
     * Reject leave.
     *
     * @param string $leaveId
     * @param string $reviewerId
     * @param string|null $comments
     * @return Leave
     * @throws \Exception
     */
    public function rejectLeave(string $leaveId, string $reviewerId, ?string $comments = null): Leave
    {
        $leave = Leave::findOrFail($leaveId);

        // Check if leave is pending
        $pendingStatus = LeaveStatus::where('name', 'pending')->first();
        if ($leave->leave_status_id !== $pendingStatus->id) {
            throw new \Exception('Only pending leaves can be rejected');
        }

        // Get rejected status
        $rejectedStatus = LeaveStatus::where('name', 'rejected')->first();
        if (!$rejectedStatus) {
            throw new \Exception('Rejected status not found');
        }

        // Update leave status
        $leave->leave_status_id = $rejectedStatus->id;
        $leave->save();

        // Notify the applicant
        $applicant = User::find($leave->user_id);
        $rejector = User::find($reviewerId);
        if ($applicant && $rejector) {
            // Send in-app notification
            $this->notificationService->notifyLeaveRejected($leave, $applicant, $rejector);
            
            // Send email notification
            if ($applicant->email) {
                Mail::to($applicant->email)->send(new LeaveRejectedMail($leave, $applicant, $rejector));
            }
        }

        return $leave->load(['user', 'category', 'status']);
    }

    /**
     * Validate leave limit.
     *
     * @param string $userId
     * @param string $categoryId
     * @param int $year
     * @return void
     * @throws \Exception
     */
    public function validateLeaveLimit(string $userId, string $categoryId, int $year): void
    {
        $category = LeaveCategory::findOrFail($categoryId);
        
        // Get approved leaves count for the year
        $approvedStatus = LeaveStatus::where('name', 'approved')->first();
        $leavesCount = Leave::where('user_id', $userId)
            ->where('leave_category_id', $categoryId)
            ->where('leave_status_id', $approvedStatus->id)
            ->whereYear('date', $year)
            ->count();

        if ($leavesCount >= $category->max_in_year) {
            throw new \Exception("Leave limit exceeded. Maximum {$category->max_in_year} leaves allowed per year for {$category->name}");
        }
    }

    /**
     * Get leave balance.
     *
     * @param string $userId
     * @param string $categoryId
     * @param int $year
     * @return array
     */
    public function getLeaveBalance(string $userId, string $categoryId, int $year): array
    {
        $category = LeaveCategory::findOrFail($categoryId);
        
        // Get approved leaves count for the year
        $approvedStatus = LeaveStatus::where('name', 'approved')->first();
        $usedLeaves = Leave::where('user_id', $userId)
            ->where('leave_category_id', $categoryId)
            ->where('leave_status_id', $approvedStatus->id)
            ->whereYear('date', $year)
            ->count();

        return [
            'category' => $category->name,
            'total' => $category->max_in_year,
            'used' => $usedLeaves,
            'remaining' => $category->max_in_year - $usedLeaves,
        ];
    }
}

