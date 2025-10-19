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
        // Validate gender restriction
        $this->validateGenderRestriction($userId, $data['leave_category_id']);
        
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

        // Load relationships needed for notifications
        $leave->load(['user', 'category', 'status']);

        // Notify primary supervisor about new leave request
        try {
            $applicant = User::with('primarySupervisor')->find($userId);
            if ($applicant) {
                $primarySupervisor = $applicant->primarySupervisor()->first();
                
                if ($primarySupervisor) {
                    // Send in-app notification to primary supervisor only
                    $this->notificationService->notifyLeaveRequest($leave, $applicant, $primarySupervisor);
                    
                    // Send email notification to primary supervisor only
                    if ($primarySupervisor->email) {
                        Mail::to($primarySupervisor->email)->send(new LeaveRequestMail($leave, $applicant, $primarySupervisor));
                    }
                } else {
                    \Log::info('No primary supervisor found for user', ['user_id' => $applicant->id]);
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

        return $leave;
    }

    /**
     * Apply for leave range (multiple days).
     *
     * @param string $userId
     * @param array $data
     * @return Leave
     * @throws \Exception
     */
    public function applyLeaveRange(string $userId, array $data): Leave
    {
        $startDate = new \DateTime($data['start_date']);
        $endDate = new \DateTime($data['end_date']);
        
        // Calculate total days
        $totalDays = $startDate->diff($endDate)->days + 1;
        
        // Validate gender restriction
        $this->validateGenderRestriction($userId, $data['leave_category_id']);
        
        // Validate leave limit for the range
        $this->validateLeaveLimitForRange($userId, $data['leave_category_id'], $startDate->format('Y'), $totalDays);

        // Get pending status
        $pendingStatus = LeaveStatus::where('name', 'pending')->first();
        if (!$pendingStatus) {
            throw new \Exception('Pending status not found');
        }

        // Create leave with date range
        $leave = Leave::create([
            'id' => Str::uuid()->toString(),
            'user_id' => $userId,
            'leave_category_id' => $data['leave_category_id'],
            'leave_status_id' => $pendingStatus->id,
            'date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'description' => $data['description'] ?? null,
        ]);

        // Load relationships needed for notifications
        $leave->load(['user', 'category', 'status']);

        // Notify primary supervisor about new leave request
        try {
            $applicant = User::with('primarySupervisor')->find($userId);
            if ($applicant) {
                $primarySupervisor = $applicant->primarySupervisor()->first();
                
                if ($primarySupervisor) {
                    // Send in-app notification to primary supervisor only
                    $this->notificationService->notifyLeaveRequest($leave, $applicant, $primarySupervisor);
                    
                    // Send email notification to primary supervisor only
                    if ($primarySupervisor->email) {
                        Mail::to($primarySupervisor->email)->send(new LeaveRequestMail($leave, $applicant, $primarySupervisor));
                    }
                } else {
                    \Log::info('No primary supervisor found for user', ['user_id' => $applicant->id]);
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

        return $leave;
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
        $approvedStatus = LeaveStatus::where('name', 'granted')->first();
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
        $approvedStatus = LeaveStatus::where('name', 'granted')->first();
        if (!$approvedStatus) {
            // If no approved status exists, no leaves have been approved yet
            return;
        }
        
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
     * Validate gender restriction for leave category.
     *
     * @param string $userId
     * @param string $categoryId
     * @return void
     * @throws \Exception
     */
    public function validateGenderRestriction(string $userId, string $categoryId): void
    {
        $user = User::findOrFail($userId);
        $category = LeaveCategory::findOrFail($categoryId);
        
        // Check if category has gender restriction
        if ($category->gender_restriction !== 'all') {
            if ($user->gender !== $category->gender_restriction) {
                $restrictionText = $category->gender_restriction === 'male' ? 'male employees' : 'female employees';
                throw new \Exception("{$category->name} is only available for {$restrictionText}.");
            }
        }
    }

    /**
     * Validate leave limit for a date range.
     *
     * @param string $userId
     * @param string $categoryId
     * @param int $year
     * @param int $requestedDays
     * @return void
     * @throws \Exception
     */
    public function validateLeaveLimitForRange(string $userId, string $categoryId, int $year, int $requestedDays): void
    {
        $category = LeaveCategory::findOrFail($categoryId);
        
        // Get approved and pending leaves count for the year
        $approvedStatus = LeaveStatus::where('name', 'granted')->first();
        $pendingStatus = LeaveStatus::where('name', 'pending')->first();
        
        $usedDays = 0;
        
        if ($approvedStatus) {
            // Count approved leaves
            $approvedLeaves = Leave::where('user_id', $userId)
                ->where('leave_category_id', $categoryId)
                ->where('leave_status_id', $approvedStatus->id)
                ->whereYear('date', $year)
                ->get();
            
            foreach ($approvedLeaves as $leave) {
                if ($leave->end_date) {
                    $start = new \DateTime($leave->date);
                    $end = new \DateTime($leave->end_date);
                    $usedDays += $start->diff($end)->days + 1;
                } else {
                    $usedDays += 1;
                }
            }
        }
        
        if ($pendingStatus) {
            // Count pending leaves
            $pendingLeaves = Leave::where('user_id', $userId)
                ->where('leave_category_id', $categoryId)
                ->where('leave_status_id', $pendingStatus->id)
                ->whereYear('date', $year)
                ->get();
            
            foreach ($pendingLeaves as $leave) {
                if ($leave->end_date) {
                    $start = new \DateTime($leave->date);
                    $end = new \DateTime($leave->end_date);
                    $usedDays += $start->diff($end)->days + 1;
                } else {
                    $usedDays += 1;
                }
            }
        }

        $totalDays = $usedDays + $requestedDays;
        
        if ($totalDays > $category->max_in_year) {
            $remaining = $category->max_in_year - $usedDays;
            throw new \Exception("Leave limit exceeded. You have {$remaining} day(s) remaining for {$category->name}. You requested {$requestedDays} day(s).");
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
        
        // Get approved leaves for the year
        $approvedStatus = LeaveStatus::where('name', 'granted')->first();
        $usedDays = 0;
        
        if ($approvedStatus) {
            $approvedLeaves = Leave::where('user_id', $userId)
                ->where('leave_category_id', $categoryId)
                ->where('leave_status_id', $approvedStatus->id)
                ->whereYear('date', $year)
                ->get();
            
            foreach ($approvedLeaves as $leave) {
                if ($leave->end_date) {
                    $start = new \DateTime($leave->date);
                    $end = new \DateTime($leave->end_date);
                    $usedDays += $start->diff($end)->days + 1;
                } else {
                    $usedDays += 1;
                }
            }
        }

        return [
            'category' => $category->name,
            'total' => $category->max_in_year,
            'used' => $usedDays,
            'remaining' => $category->max_in_year - $usedDays,
        ];
    }
}

