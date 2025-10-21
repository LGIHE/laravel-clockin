<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public function create(string $userId, string $type, string $title, string $message, ?array $data = null, ?string $actionUrl = null): ?Notification
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'action_url' => $actionUrl,
                'read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification', [
                'user_id' => $userId,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create notification for new notice
     */
    public function notifyNewNotice($noticeId, $noticeTitle, $createdBy): void
    {
        try {
            // Get all active users except the creator
            $users = User::where('status', 1)
                ->where('id', '!=', $createdBy)
                ->get();

            foreach ($users as $user) {
                $this->create(
                    $user->id,
                    'notice',
                    'New Notice Published',
                    "A new notice has been published: {$noticeTitle}",
                    ['notice_id' => $noticeId],
                    url('/notices')
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to create notice notifications', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create notification for new leave request (to primary supervisor)
     */
    public function notifyLeaveRequest($leave, $applicant, $supervisor): void
    {
        try {
            // Check if supervisor is provided
            if (!$supervisor || !$supervisor->id) {
                Log::info('No supervisor provided for leave request notification', ['user_id' => $applicant->id]);
                return;
            }

            $startDate = is_string($leave->start_date) ? $leave->start_date : ($leave->start_date ? $leave->start_date->format('Y-m-d') : '');
            $endDate = is_string($leave->end_date) ? $leave->end_date : ($leave->end_date ? $leave->end_date->format('Y-m-d') : '');
            $leaveDate = is_string($leave->date) ? $leave->date : ($leave->date ? $leave->date->format('Y-m-d') : '');
            
            // Use appropriate date display
            $dateDisplay = $startDate && $endDate ? "from {$startDate} to {$endDate}" : "for {$leaveDate}";

            $this->create(
                $supervisor->id,
                'leave_pending',
                'New Leave Request',
                "{$applicant->name} has submitted a leave request {$dateDisplay}",
                [
                    'leave_id' => $leave->id,
                    'applicant_id' => $applicant->id,
                    'applicant_name' => $applicant->name,
                ],
                url('/leaves')
            );
        } catch (\Exception $e) {
            Log::error('Failed to create leave request notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create notification for leave approval
     */
    public function notifyLeaveApproved($leave, $applicant, $approver): void
    {
        try {
            $startDate = is_string($leave->start_date) ? $leave->start_date : ($leave->start_date ? $leave->start_date->format('Y-m-d') : '');
            $endDate = is_string($leave->end_date) ? $leave->end_date : ($leave->end_date ? $leave->end_date->format('Y-m-d') : '');
            $leaveDate = is_string($leave->date) ? $leave->date : ($leave->date ? $leave->date->format('Y-m-d') : '');
            
            // Use appropriate date display
            $dateDisplay = $startDate && $endDate ? "from {$startDate} to {$endDate}" : "for {$leaveDate}";
            
            $this->create(
                $applicant->id,
                'leave_approved',
                'Leave Request Approved',
                "Your leave request {$dateDisplay} has been approved by {$approver->name}",
                [
                    'leave_id' => $leave->id,
                    'approver_id' => $approver->id,
                    'approver_name' => $approver->name,
                ],
                url('/leaves')
            );
        } catch (\Exception $e) {
            Log::error('Failed to create leave approval notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create notification for leave rejection
     */
    public function notifyLeaveRejected($leave, $applicant, $rejector): void
    {
        try {
            $startDate = is_string($leave->start_date) ? $leave->start_date : ($leave->start_date ? $leave->start_date->format('Y-m-d') : '');
            $endDate = is_string($leave->end_date) ? $leave->end_date : ($leave->end_date ? $leave->end_date->format('Y-m-d') : '');
            $leaveDate = is_string($leave->date) ? $leave->date : ($leave->date ? $leave->date->format('Y-m-d') : '');
            
            // Use appropriate date display
            $dateDisplay = $startDate && $endDate ? "from {$startDate} to {$endDate}" : "for {$leaveDate}";
            
            $this->create(
                $applicant->id,
                'leave_rejected',
                'Leave Request Rejected',
                "Your leave request {$dateDisplay} has been rejected by {$rejector->name}",
                [
                    'leave_id' => $leave->id,
                    'rejector_id' => $rejector->id,
                    'rejector_name' => $rejector->name,
                ],
                url('/leaves')
            );
        } catch (\Exception $e) {
            Log::error('Failed to create leave rejection notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create notification for new holiday
     */
    public function notifyNewHoliday($holiday): void
    {
        try {
            // Get all active users
            $users = User::where('status', 1)->get();

            foreach ($users as $user) {
                $this->create(
                    $user->id,
                    'holiday',
                    'New Holiday Added',
                    "A new holiday has been added: {$holiday->name} on {$holiday->date}",
                    ['holiday_id' => $holiday->id],
                    url('/holidays')
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to create holiday notifications', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create notification for profile update
     */
    public function notifyProfileUpdate($userId, $updateType, $details): void
    {
        try {
            $this->create(
                $userId,
                'profile_update',
                'Profile Updated',
                "Your profile has been updated: {$details}",
                ['update_type' => $updateType],
                url('/profile')
            );
        } catch (\Exception $e) {
            Log::error('Failed to create profile update notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create notification for new user creation
     */
    public function notifyUserCreated($user): void
    {
        try {
            $this->create(
                $user->id,
                'user_created',
                'Welcome to ClockIn!',
                "Your account has been created. Please check your email for login credentials.",
                ['user_id' => $user->id],
                url('/dashboard')
            );
        } catch (\Exception $e) {
            Log::error('Failed to create user creation notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        try {
            $notification = Notification::find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(string $userId): bool
    {
        try {
            Notification::where('user_id', $userId)
                ->where('read', false)
                ->update([
                    'read' => true,
                    'read_at' => now(),
                ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(string $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }

    /**
     * Get recent notifications for a user
     */
    public function getRecent(string $userId, int $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Notify supervisor about compensation leave request
     */
    public function notifyCompensationLeaveRequest($request, $requester, $supervisor): void
    {
        $this->create(
            $supervisor->id,
            'compensation_leave_request',
            'New Compensation Leave Request',
            "{$requester->name} has requested {$request->days_requested} compensation day(s) for working on {$request->work_date->format('M d, Y')}",
            ['request_id' => $request->id],
            url('/compensation-leaves')
        );
    }

    /**
     * Notify HR about supervisor-approved compensation leave
     */
    public function notifyCompensationLeaveForHR($request, $hrUser): void
    {
        $this->create(
            $hrUser->id,
            'compensation_leave_hr',
            'Compensation Leave Awaiting HR Action',
            "{$request->user->name}'s compensation leave request has been approved by supervisor and needs HR to effect it",
            ['request_id' => $request->id],
            url('/compensation-leaves')
        );
    }

    /**
     * Notify requester that supervisor approved
     */
    public function notifyCompensationLeaveSupervisorApproved($request): void
    {
        $this->create(
            $request->user_id,
            'compensation_leave_approved',
            'Compensation Leave Approved by Supervisor',
            "Your compensation leave request for {$request->days_requested} day(s) has been approved by your supervisor. Awaiting HR to effect it.",
            ['request_id' => $request->id],
            url('/compensation-leaves')
        );
    }

    /**
     * Notify requester that HR effected the request
     */
    public function notifyCompensationLeaveEffected($request): void
    {
        $this->create(
            $request->user_id,
            'compensation_leave_effected',
            'Compensation Leave Days Added',
            "{$request->days_requested} compensation day(s) have been added to your leave balance",
            ['request_id' => $request->id],
            url('/leaves/apply')
        );
    }

    /**
     * Notify requester that request was rejected
     */
    public function notifyCompensationLeaveRejected($request, $reason): void
    {
        $this->create(
            $request->user_id,
            'compensation_leave_rejected',
            'Compensation Leave Request Rejected',
            "Your compensation leave request has been rejected. Reason: {$reason}",
            ['request_id' => $request->id],
            url('/compensation-leaves')
        );
    }
}
