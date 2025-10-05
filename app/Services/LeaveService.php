<?php

namespace App\Services;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\LeaveStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeaveService
{
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

        // Create notification for the user
        $this->createLeaveNotification($leave, 'approved', $reviewerId, $comments);

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

        // Create notification for the user
        $this->createLeaveNotification($leave, 'rejected', $reviewerId, $comments);

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

    /**
     * Create notification for leave action.
     *
     * @param Leave $leave
     * @param string $action
     * @param string $reviewerId
     * @param string|null $comments
     * @return void
     */
    private function createLeaveNotification(Leave $leave, string $action, string $reviewerId, ?string $comments = null): void
    {
        $reviewer = User::find($reviewerId);
        $message = "Your leave request for {$leave->date->format('Y-m-d')} has been {$action}";
        
        if ($comments) {
            $message .= ". Comments: {$comments}";
        }

        DB::table('notifications')->insert([
            'id' => Str::uuid()->toString(),
            'notifiable_id' => $leave->user_id,
            'notifiable_type' => 'App\Models\User',
            'type' => 'leave_' . $action,
            'data' => json_encode([
                'title' => 'Leave ' . ucfirst($action),
                'message' => $message,
                'leave_id' => $leave->id,
                'reviewer' => $reviewer ? $reviewer->name : 'System',
                'comments' => $comments,
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

