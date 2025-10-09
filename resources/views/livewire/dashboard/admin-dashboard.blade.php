<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
    </div>

    <!-- Statistics Cards Grid (4 columns) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total User Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 flex flex-row justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-600">Total User</h3>
                    <p class="text-3xl font-bold mt-2">{{ $systemStats['total_users'] ?? 0 }}</p>
                    <div class="text-sm text-blue-500 mt-4 flex items-center gap-1">
                        <span class="inline-block w-2 h-2 bg-indigo-500 rounded-full"></span>
                        {{ $systemStats['active_users'] ?? 0 }} Active User
                    </div>
                </div>
                <div class="p-2 rounded-lg bg-indigo-100">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Holiday This Year Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 flex flex-row justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-600">Holiday This Year</h3>
                    <p class="text-3xl font-bold mt-2">{{ $holidays ? $holidays->count() : 0 }}</p>
                    <div class="text-sm text-blue-500 mt-4 flex items-center gap-1">
                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                        {{ $holidays ? $holidays->filter(fn($h) => \Carbon\Carbon::parse($h->date)->month === now()->month)->count() : 0 }} Holiday This Month
                    </div>
                </div>
                <div class="p-2 rounded-lg bg-red-100">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Leaves Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 flex flex-row justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-600">Pending Leaves</h3>
                    <p class="text-3xl font-bold mt-2">{{ $systemStats['pending_leaves'] ?? 0 }}</p>
                    <div class="text-sm text-blue-500 mt-4 flex items-center gap-1">
                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                        Awaiting Approval
                    </div>
                </div>
                <div class="p-2 rounded-lg bg-yellow-100">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Present Today Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 flex flex-row justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-600">Present Today</h3>
                    <p class="text-3xl font-bold mt-2">{{ $systemStats['present_today'] ?? 0 }}</p>
                    <div class="text-sm text-blue-500 mt-4 flex items-center gap-1">
                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                        {{ $systemStats['absent_today'] ?? 0 }} Users Absent
                    </div>
                </div>
                <div class="p-2 rounded-lg bg-purple-100">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Monthly Attendance Table -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Recent Activity Card -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Recent Activity</h3>
            </div>
            <div class="p-6">
                <!-- Clock In/Out Form -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <div class="space-y-4">
                        @if(isset($attendanceStatus) && is_array($attendanceStatus) && ($attendanceStatus['clocked_in'] ?? false))
                            <!-- Clocked In Status -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                    <span class="text-sm font-semibold text-green-700">Currently Clocked In</span>
                                </div>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <div>
                                        <span class="font-medium">Clocked in at: </span>
                                        {{ \Carbon\Carbon::parse($attendanceStatus['in_time'])->format('h:i A') }}
                                    </div>
                                    @if(isset($attendanceStatus['attendance']) && $attendanceStatus['attendance']->in_message)
                                        <div>
                                            <span class="font-medium">Note: </span>
                                            {{ $attendanceStatus['attendance']->in_message }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-medium">Duration: </span>
                                        <span class="font-semibold">
                                            {{ sprintf('%02d:%02d', floor(($attendanceStatus['duration'] ?? 0) / 3600), floor((($attendanceStatus['duration'] ?? 0) % 3600) / 60)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Not Clocked In -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-2 h-2 bg-yellow-500 rounded-full"></span>
                                    <span class="text-sm font-medium text-yellow-700">Not Clocked In</span>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Message Input -->
                        <div>
                            <label for="clockMessage" class="block text-sm font-medium text-gray-700 mb-1">
                                Message (Optional)
                            </label>
                            <input 
                                type="text" 
                                id="clockMessage"
                                wire:model="clockMessage"
                                placeholder="Add a note..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                @if($isLoading) disabled @endif
                            >
                        </div>
                        
                        <!-- Clock In/Out Button -->
                        @php
                            $isClockedIn = isset($attendanceStatus) && is_array($attendanceStatus) && ($attendanceStatus['clocked_in'] ?? false);
                        @endphp
                        <button 
                            wire:click="{{ $isClockedIn ? 'clockOut' : 'clockIn' }}"
                            class="w-full px-4 py-2 text-white rounded-md font-medium transition text-sm {{ $isClockedIn ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}"
                            @if($isLoading) disabled @endif
                        >
                            @if($isLoading)
                                <svg class="animate-spin inline-block -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            @endif
                            {{ $isClockedIn ? 'Punch Out' : 'Punch In' }}
                        </button>
                    </div>
                </div>
                
                <!-- Recent Activities List -->
                <div class="space-y-1 max-h-64 overflow-y-auto">
                    @if($recentActivities && count($recentActivities) > 0)
                        @foreach($recentActivities as $index => $activity)
                            @if($index < 3)
                                <div class="border-b border-gray-200 py-2 flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $activity['action'] === 'clocked_in' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $activity['action'] === 'clocked_in' ? 'Punch In' : 'Punch Out' }}
                                    </span>
                                    <span class="text-gray-600 text-sm">
                                        {{ \Carbon\Carbon::parse($activity['time'])->format('M d, Y h:i a') }}
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center text-gray-500 py-4">
                            No recent activities
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Working Hours Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">
                        {{ $monthlyAttendance['month'] ?? \Carbon\Carbon::now()->format('F Y') }} Attendance
                    </h3>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left text-xs font-medium text-gray-500 uppercase pb-2 w-10">#</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase pb-2">Name</th>
                                <th class="text-right text-xs font-medium text-gray-500 uppercase pb-2">Worked</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($monthlyAttendance['user_reports']) && count($monthlyAttendance['user_reports']) > 0)
                                @foreach($monthlyAttendance['user_reports'] as $index => $report)
                                    @if($index < 5)
                                        <tr class="border-b border-gray-100">
                                            <td class="py-3">{{ $index + 1 }}</td>
                                            <td class="py-3">{{ $report['user']['name'] }}</td>
                                            <td class="py-3 text-right">{{ $report['statistics']['total_hours_formatted'] }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center py-8 text-gray-500">
                                        No attendance data available for this month
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Users Pie Chart and Recent Notices -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Active User Chart Placeholder -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Active User</h3>
            </div>
            <div class="p-6 flex flex-col items-center">
                <div class="h-64 w-64 flex items-center justify-center">
                    <!-- Placeholder for pie chart -->
                    <div class="text-center">
                        <div class="w-40 h-40 mx-auto mb-4 rounded-full border-8 border-indigo-500 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-3xl font-bold">{{ $systemStats['active_users'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Active</div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $systemStats['inactive_users'] ?? 0 }} Inactive
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notices Card -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 flex flex-row items-center justify-between">
                <h3 class="text-lg font-semibold">Recent Notices</h3>
                <a href="{{ route('notices.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                    View All →
                </a>
            </div>
            <div class="p-6 min-h-64">
                @if($notices && $notices->count() > 0)
                    <div class="space-y-3">
                        @foreach($notices->take(5) as $notice)
                            <div class="border-b pb-3 last:border-b-0 hover:bg-gray-50 p-2 rounded transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-sm">{{ $notice->title }}</h4>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit($notice->content, 100) }}</p>
                                        <span class="text-xs text-gray-400 mt-1 block">
                                            {{ \Carbon\Carbon::parse($notice->created_at)->format('M d, Y h:i a') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 py-8">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <p class="text-sm">No notices available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Upcoming Holidays Widget -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b border-gray-200 flex flex-row items-center justify-between">
            <h3 class="text-lg font-semibold">Upcoming Holidays</h3>
            <a href="{{ route('holidays.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                View Calendar →
            </a>
        </div>
        <div class="p-6">
            @php
                $upcomingHolidays = $holidays ? $holidays->filter(fn($h) => \Carbon\Carbon::parse($h->date) >= now())->sortBy('date')->take(5) : collect([]);
            @endphp
            @if($upcomingHolidays->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($upcomingHolidays as $holiday)
                        @php
                            $holidayDate = \Carbon\Carbon::parse($holiday->date);
                            $today = now();
                            $daysUntil = (int) ceil($today->diffInDays($holidayDate, false));
                        @endphp
                        <div class="border border-red-200 bg-red-50 p-4 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs bg-red-200 text-red-700 px-2 py-1 rounded">
                                    @if($daysUntil === 0)
                                        Today
                                    @elseif($daysUntil === 1)
                                        Tomorrow
                                    @else
                                        {{ $daysUntil }} days
                                    @endif
                                </span>
                            </div>
                            <h4 class="font-semibold text-sm text-red-700 mb-1">{{ $holiday->name }}</h4>
                            <p class="text-xs text-gray-600">
                                {{ $holidayDate->format('l, M d') }}
                            </p>
                            @if($holiday->description)
                                <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $holiday->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-500 py-8">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm">No upcoming holidays</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-6 text-center text-sm text-gray-500">
        © 2025 lgf & made with ❤️
    </div>
</div>
