<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
    </div>

    <!-- Statistics Cards Grid (3 columns) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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

        <!-- Leave This Year Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 flex flex-row justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-600">Leave This Year</h3>
                    <p class="text-3xl font-bold mt-2">{{ $dashboardData['stats']['leave_this_year'] ?? 0 }}</p>
                    <div class="text-sm text-blue-500 mt-4 flex items-center gap-1">
                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                        Check Leave Status
                    </div>
                </div>
                <div class="p-2 rounded-lg bg-yellow-100">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Last 30 Days Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 flex flex-row justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-600">Last 30 Days</h3>
                    <p class="text-3xl font-bold mt-2">{{ $dashboardData['stats']['last_30_days_formatted'] ?? '00:00:00' }}</p>
                    <div class="text-sm text-blue-500 mt-4 flex items-center gap-1">
                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                        {{ $dashboardData['work_duration'] ?? '00:00:00' }} Working Today
                    </div>
                </div>
                <div class="p-2 rounded-lg bg-green-100">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Attendance Status (if clocked in) -->
    @if(isset($attendanceStatus) && is_array($attendanceStatus) && ($attendanceStatus['clocked_in'] ?? false) && ($attendanceStatus['in_time'] ?? null))
        <div class="bg-white rounded-lg shadow border-green-200 bg-green-50 mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-500 rounded-full">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-green-800">Currently Clocked In</h3>
                            <p class="text-sm text-green-600">
                                Started at {{ \Carbon\Carbon::parse($attendanceStatus['in_time'])->format('h:i a') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-green-600 mb-1">Work Duration</p>
                        <p class="text-3xl font-bold text-green-800">{{ $dashboardData['work_duration'] ?? '00:00:00' }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Activity and Working Hour Analysis -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Recent Activity Card -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Recent Activity</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4 mb-4">
                    @if(!isset($attendanceStatus) || !is_array($attendanceStatus) || !($attendanceStatus['clocked_in'] ?? false))
                        <!-- Punch In View -->
                        <div class="space-y-2">
                            <label for="clockMessage" class="block text-sm font-medium text-gray-700">
                                Comment (Optional)
                            </label>
                            <input
                                type="text"
                                id="clockMessage"
                                wire:model="clockMessage"
                                placeholder="Optional Comment"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                @if($isLoading) disabled @endif
                            >
                        </div>
                    @else
                        <!-- Punch Out View -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                <span class="text-sm font-semibold text-green-700">Currently Clocked In</span>
                            </div>
                            @if($attendanceStatus['in_time'] ?? null)
                                <div class="space-y-1 text-sm text-gray-700">
                                    <div>
                                        <span class="font-medium">Clocked in at: </span>
                                        <span>{{ \Carbon\Carbon::parse($attendanceStatus['in_time'])->format('h:i a') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="space-y-2">
                            <label for="clockMessage" class="block text-sm font-medium text-gray-700">
                                Work Summary (Optional)
                            </label>
                            <textarea
                                id="clockMessage"
                                wire:model="clockMessage"
                                rows="3"
                                placeholder="What did you accomplish today? Add notes about your task..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                @if($isLoading) disabled @endif
                            ></textarea>
                        </div>
                    @endif

                    @php
                        $isClockedIn = isset($attendanceStatus) && is_array($attendanceStatus) && ($attendanceStatus['clocked_in'] ?? false);
                    @endphp
                    
                    <button
                        wire:click="{{ $isClockedIn ? 'clockOut' : 'clockIn' }}"
                        class="w-full px-4 py-2 text-white rounded-md font-medium transition {{ $isClockedIn ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600' }}"
                        @if($isLoading) disabled @endif
                    >
                        {{ $isClockedIn ? 'Punch Out' : 'Punch In' }}
                    </button>
                </div>

                <div class="space-y-1 max-h-64 overflow-y-auto">
                    @if(isset($dashboardData['recent_attendance']) && count($dashboardData['recent_attendance']) > 0)
                        @foreach($dashboardData['recent_attendance']->take(5) as $attendance)
                            @if($attendance->in_time)
                                <div class="border-b border-gray-200 py-2 flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                                        Punch In
                                    </span>
                                    <span class="text-gray-600 text-sm">
                                        {{ \Carbon\Carbon::parse($attendance->in_time)->format('M d, Y h:i a') }}
                                    </span>
                                </div>
                            @endif
                            @if($attendance->out_time)
                                <div class="border-b border-gray-200 py-2 flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-orange-100 text-orange-800">
                                        Punch Out
                                    </span>
                                    <span class="text-gray-600 text-sm">
                                        {{ \Carbon\Carbon::parse($attendance->out_time)->format('M d, Y h:i a') }}
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center text-gray-500 py-4">
                            No attendance records
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Working Hour Analysis Card -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Working Hour Analysis</h3>
            </div>
            <div class="p-6 h-[320px]">
                @if($chartData && count($chartData) > 0)
                    <!-- Simple Bar Chart -->
                    <div class="h-full flex items-end justify-between gap-2">
                        @foreach($chartData as $day)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-green-500 rounded-t" style="height: {{ $day['hours'] > 0 ? min(($day['hours'] / 12) * 100, 100) : 5 }}%"></div>
                                <div class="text-xs text-gray-600 mt-2">{{ $day['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($day['hours'], 1) }}h</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex items-center justify-center text-gray-500">
                        No data available
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Notices and Upcoming Holidays -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Recent Notices Card -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 flex flex-row items-center justify-between">
                <h3 class="text-lg font-semibold">Recent Notices</h3>
                <a href="{{ route('notices.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                    View All →
                </a>
            </div>
            <div class="p-6 min-h-20">
                @if($notices && $notices->count() > 0)
                    <div class="space-y-3">
                        @foreach($notices->take(3) as $notice)
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

        <!-- Upcoming Holidays Card -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 flex flex-row items-center justify-between">
                <h3 class="text-lg font-semibold">Upcoming Holidays</h3>
                <a href="{{ route('holidays.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                    View Calendar →
                </a>
            </div>
            <div class="p-6 min-h-20">
                @php
                    $upcomingHolidays = $holidays ? $holidays->filter(fn($h) => \Carbon\Carbon::parse($h->date) >= now())->sortBy('date')->take(3) : collect([]);
                @endphp
                @if($upcomingHolidays->count() > 0)
                    <div class="space-y-3">
                        @foreach($upcomingHolidays as $holiday)
                            @php
                                $holidayDate = \Carbon\Carbon::parse($holiday->date);
                                $today = now();
                                $daysUntil = (int) ceil($today->diffInDays($holidayDate, false));
                            @endphp
                            <div class="border-b pb-3 last:border-b-0 hover:bg-red-50 p-2 rounded transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-sm text-red-600">{{ $holiday->name }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">
                                            {{ $holidayDate->format('l, M d, Y') }}
                                        </p>
                                        @if($holiday->description)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $holiday->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded whitespace-nowrap ml-2">
                                        @if($daysUntil === 0)
                                            Today
                                        @elseif($daysUntil === 1)
                                            Tomorrow
                                        @else
                                            {{ $daysUntil }} days
                                        @endif
                                    </div>
                                </div>
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
    </div>

    <!-- Footer -->
    <div class="mt-6 text-center text-sm text-gray-500">
        © 2025 lgf & made with ❤️
    </div>
</div>
