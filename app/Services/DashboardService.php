<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Notification;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get user dashboard data.
     *
     * @param string $userId
     * @return array
     */
    public function getUserDashboard(string $userId): array
    {
        // Cache user data for 5 minutes
        $user = Cache::remember("user:{$userId}", 300, function () use ($userId) {
            return User::with(['userLevel', 'department', 'designation'])->findOrFail($userId);
        });
        
        // Get attendance status (whether user is currently clocked in) - no cache for real-time data
        $currentAttendance = Attendance::where('user_id', $userId)
            ->whereNull('out_time')
            ->latest('in_time')
            ->first();
        
        $attendanceStatus = [
            'clocked_in' => $currentAttendance !== null,
            'in_time' => $currentAttendance ? $currentAttendance->in_time : null,
            'in_message' => $currentAttendance ? $currentAttendance->in_message : null,
        ];
        
        // Get recent attendance records (last 7 days)
        $recentAttendance = Attendance::where('user_id', $userId)
            ->whereBetween('in_time', [Carbon::now()->subDays(7), Carbon::now()])
            ->orderBy('in_time', 'desc')
            ->limit(10)
            ->get();
        
        // Get upcoming leaves (next 30 days)
        $upcomingLeaves = Leave::where('user_id', $userId)
            ->whereBetween('date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->with(['category', 'status'])
            ->orderBy('date', 'asc')
            ->get();
        
        // Get unread notifications
        $notifications = Notification::where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Calculate monthly statistics - cache for 10 minutes
        $cacheKey = "user_stats:{$userId}:" . Carbon::now()->format('Y-m');
        $stats = Cache::remember($cacheKey, 600, function () use ($userId) {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            
            $monthlyAttendance = Attendance::where('user_id', $userId)
                ->whereBetween('in_time', [$startOfMonth, $endOfMonth])
                ->whereNotNull('out_time')
                ->get();
            
            $totalHoursThisMonth = $monthlyAttendance->sum('worked');
            $totalDaysThisMonth = $monthlyAttendance->count();
            $averageHoursPerDay = $totalDaysThisMonth > 0 
                ? $totalHoursThisMonth / $totalDaysThisMonth 
                : 0;
            
            return [
                'total_hours_this_month' => $totalHoursThisMonth,
                'total_days_this_month' => $totalDaysThisMonth,
                'average_hours_per_day' => round($averageHoursPerDay, 2),
                'total_hours_formatted' => $this->formatSeconds($totalHoursThisMonth),
                'average_hours_formatted' => $this->formatSeconds($averageHoursPerDay),
            ];
        });
        
        return [
            'attendance_status' => $attendanceStatus,
            'recent_attendance' => $recentAttendance,
            'upcoming_leaves' => $upcomingLeaves,
            'notifications' => $notifications,
            'stats' => $stats,
        ];
    }
    
    /**
     * Get supervisor dashboard data.
     *
     * @param string $userId
     * @return array
     */
    public function getSupervisorDashboard(string $userId): array
    {
        // Get team members (users supervised by this user) - cache for 10 minutes
        $cacheKey = "supervisor_team:{$userId}";
        $teamMembers = Cache::remember($cacheKey, 600, function () use ($userId) {
            return User::where('supervisor_id', $userId)
                ->where('status', 1)
                ->with(['department', 'designation'])
                ->get();
        });
        
        $teamMemberIds = $teamMembers->pluck('id')->toArray();
        
        // Get team attendance summary for today - no cache for real-time data
        $today = Carbon::today();
        $teamAttendance = Attendance::whereIn('user_id', $teamMemberIds)
            ->whereDate('in_time', $today)
            ->with(['user.userLevel', 'user.department'])
            ->get();
        
        $clockedInCount = $teamAttendance->filter(function ($attendance) {
            return $attendance->out_time === null;
        })->count();
        
        $clockedOutCount = $teamAttendance->filter(function ($attendance) {
            return $attendance->out_time !== null;
        })->count();
        
        $notClockedInCount = $teamMembers->count() - $teamAttendance->count();
        
        $attendanceSummary = [
            'total_team_members' => $teamMembers->count(),
            'clocked_in' => $clockedInCount,
            'clocked_out' => $clockedOutCount,
            'not_clocked_in' => $notClockedInCount,
            'attendance_records' => $teamAttendance,
        ];
        
        // Get pending leave requests from team
        $pendingLeaves = Leave::whereIn('user_id', $teamMemberIds)
            ->whereHas('status', function ($query) {
                $query->where('name', 'pending');
            })
            ->with(['user', 'category', 'status'])
            ->orderBy('date', 'asc')
            ->get();
        
        // Calculate team statistics for current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $teamMonthlyAttendance = Attendance::whereIn('user_id', $teamMemberIds)
            ->whereBetween('in_time', [$startOfMonth, $endOfMonth])
            ->whereNotNull('out_time')
            ->get();
        
        $totalTeamHours = $teamMonthlyAttendance->sum('worked');
        $averageTeamHours = $teamMembers->count() > 0 
            ? $totalTeamHours / $teamMembers->count() 
            : 0;
        
        $teamStats = [
            'total_team_hours_this_month' => $totalTeamHours,
            'average_hours_per_member' => round($averageTeamHours, 2),
            'total_team_hours_formatted' => $this->formatSeconds($totalTeamHours),
            'average_hours_formatted' => $this->formatSeconds($averageTeamHours),
            'pending_leave_requests' => $pendingLeaves->count(),
        ];
        
        return [
            'team_attendance' => $attendanceSummary,
            'pending_leaves' => $pendingLeaves,
            'team_stats' => $teamStats,
            'team_members' => $teamMembers,
        ];
    }
    
    /**
     * Get admin dashboard data.
     *
     * @return array
     */
    public function getAdminDashboard(): array
    {
        // System-wide statistics - cache for 5 minutes
        $systemStats = Cache::remember('admin_system_stats', 300, function () {
            $totalUsers = User::count();
            $activeUsers = User::where('status', 1)->count();
            $inactiveUsers = User::where('status', 0)->count();
            $totalDepartments = Department::count();
            $totalProjects = Project::count();
        
            // Today's attendance statistics
            $today = Carbon::today();
            $todayAttendance = Attendance::whereDate('in_time', $today)->count();
            $currentlyClockedIn = Attendance::whereDate('in_time', $today)
                ->whereNull('out_time')
                ->count();
            
            return [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'inactive_users' => $inactiveUsers,
                'total_departments' => $totalDepartments,
                'total_projects' => $totalProjects,
                'today_attendance' => $todayAttendance,
                'currently_clocked_in' => $currentlyClockedIn,
            ];
        });
        
        // Recent activities (last 10 attendance records) - no cache for real-time data
        $recentActivities = Attendance::with(['user:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'user_name' => $attendance->user->name,
                    'action' => $attendance->out_time ? 'clocked_out' : 'clocked_in',
                    'time' => $attendance->out_time ?? $attendance->in_time,
                    'message' => $attendance->out_time 
                        ? $attendance->out_message 
                        : $attendance->in_message,
                ];
            });
        
        // Pending approvals (all pending leaves)
        $pendingApprovals = Leave::whereHas('status', function ($query) {
                $query->where('name', 'pending');
            })
            ->with(['user', 'category', 'status'])
            ->orderBy('date', 'asc')
            ->get();
        
        // Department-wise statistics
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $departmentStats = Department::withCount(['users' => function ($query) {
                $query->where('status', 1);
            }])
            ->get()
            ->map(function ($department) use ($startOfMonth, $endOfMonth) {
                $departmentUserIds = User::where('department_id', $department->id)
                    ->where('status', 1)
                    ->pluck('id');
                
                $totalHours = Attendance::whereIn('user_id', $departmentUserIds)
                    ->whereBetween('in_time', [$startOfMonth, $endOfMonth])
                    ->whereNotNull('out_time')
                    ->sum('worked');
                
                return [
                    'id' => $department->id,
                    'name' => $department->name,
                    'active_users' => $department->users_count,
                    'total_hours_this_month' => $totalHours,
                    'total_hours_formatted' => $this->formatSeconds($totalHours),
                ];
            });
        
        return [
            'system_stats' => $systemStats,
            'recent_activities' => $recentActivities,
            'pending_approvals' => $pendingApprovals,
            'department_stats' => $departmentStats,
        ];
    }
    
    /**
     * Format seconds to HH:MM format.
     *
     * @param float $seconds
     * @return string
     */
    private function formatSeconds(float $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
