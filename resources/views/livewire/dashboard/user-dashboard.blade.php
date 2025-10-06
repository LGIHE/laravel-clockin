<x-layouts.app title="Dashboard">
    <div>
        <!-- Clock In/Out Widget -->
        <div class="mb-8">
            <x-ui.card>
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Attendance Status</h2>
                        @if($attendanceStatus['clocked_in'])
                            <div class="flex items-center space-x-2">
                                <x-ui.badge variant="success">Clocked In</x-ui.badge>
                                <span class="text-sm text-gray-600">
                                    Since {{ \Carbon\Carbon::parse($attendanceStatus['in_time'])->format('h:i A') }}
                                </span>
                            </div>
                            @if($attendanceStatus['in_message'])
                                <p class="text-sm text-gray-500 mt-1">{{ $attendanceStatus['in_message'] }}</p>
                            @endif
                        @else
                            <x-ui.badge variant="warning">Not Clocked In</x-ui.badge>
                        @endif
                    </div>
                    
                    <div class="flex-1 md:max-w-md md:ml-8">
                        <div class="space-y-3">
                            <div>
                                <label for="clockMessage" class="block text-sm font-medium text-gray-700 mb-1">
                                    Message (Optional)
                                </label>
                                <input 
                                    type="text" 
                                    id="clockMessage"
                                    wire:model="clockMessage"
                                    placeholder="Add a note..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    @if($isLoading) disabled @endif
                                >
                            </div>
                            
                            <div class="flex space-x-3">
                                @if($attendanceStatus['clocked_in'])
                                    <x-ui.button 
                                        wire:click="clockOut" 
                                        variant="danger" 
                                        class="flex-1"
                                        :disabled="$isLoading"
                                    >
                                        @if($isLoading)
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        @endif
                                        Clock Out
                                    </x-ui.button>
                                @else
                                    <x-ui.button 
                                        wire:click="clockIn" 
                                        variant="success" 
                                        class="flex-1"
                                        :disabled="$isLoading"
                                    >
                                        @if($isLoading)
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        @endif
                                        Clock In
                                    </x-ui.button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Hours This Month -->
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Hours</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $dashboardData['stats']['total_hours_formatted'] ?? '00:00' }}
                        </p>
                        <p class="text-xs text-gray-500">This month</p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Total Days This Month -->
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Days Worked</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $dashboardData['stats']['total_days_this_month'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500">This month</p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Average Hours Per Day -->
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Average Hours</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $dashboardData['stats']['average_hours_formatted'] ?? '00:00' }}
                        </p>
                        <p class="text-xs text-gray-500">Per day</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Attendance -->
            <x-ui.card>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Attendance</h3>
                    <p class="text-sm text-gray-600">Last 7 days</p>
                </div>
                
                @if($dashboardData['recent_attendance']->isEmpty())
                    <x-ui.empty-state 
                        title="No attendance records found"
                        description="Your recent attendance will appear here"
                    />
                @else
                    <div class="space-y-3">
                        @foreach($dashboardData['recent_attendance'] as $attendance)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($attendance->in_time)->format('M d, Y') }}
                                        </p>
                                        @if($attendance->out_time)
                                            <x-ui.badge variant="success" size="sm">Completed</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="warning" size="sm">In Progress</x-ui.badge>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-4 mt-1 text-xs text-gray-600">
                                        <span>In: {{ \Carbon\Carbon::parse($attendance->in_time)->format('h:i A') }}</span>
                                        @if($attendance->out_time)
                                            <span>Out: {{ \Carbon\Carbon::parse($attendance->out_time)->format('h:i A') }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if($attendance->worked)
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ sprintf('%02d:%02d', floor($attendance->worked / 3600), floor(($attendance->worked % 3600) / 60)) }}
                                        </p>
                                        <p class="text-xs text-gray-500">hours</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            <!-- Upcoming Leaves -->
            <x-ui.card>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Upcoming Leaves</h3>
                    <p class="text-sm text-gray-600">Next 30 days</p>
                </div>
                
                @if($dashboardData['upcoming_leaves']->isEmpty())
                    <x-ui.empty-state 
                        title="No upcoming leaves"
                        description="Your scheduled leaves will appear here"
                    />
                @else
                    <div class="space-y-3">
                        @foreach($dashboardData['upcoming_leaves'] as $leave)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($leave->date)->format('M d, Y') }}
                                            </p>
                                            @php
                                                $statusVariant = match(strtolower($leave->status->name ?? '')) {
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                    'pending' => 'warning',
                                                    default => 'default'
                                                };
                                            @endphp
                                            <x-ui.badge :variant="$statusVariant" size="sm">
                                                {{ ucfirst($leave->status->name ?? 'Unknown') }}
                                            </x-ui.badge>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $leave->category->name ?? 'N/A' }}</p>
                                        @if($leave->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $leave->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
        </div>

        <!-- Notifications -->
        @if($dashboardData['notifications']->isNotEmpty())
            <div class="mt-6">
                <x-ui.card>
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Notifications</h3>
                        <p class="text-sm text-gray-600">Unread notifications</p>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($dashboardData['notifications'] as $notification)
                            <div class="flex items-start p-3 bg-blue-50 border border-blue-100 rounded-lg">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            </div>
        @endif
    </div>
</x-layouts.app>
