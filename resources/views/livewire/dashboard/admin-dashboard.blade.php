<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                    <p class="text-sm text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button 
                        wire:click="refreshDashboard" 
                        class="text-gray-600 hover:text-gray-900 transition"
                        title="Refresh Dashboard"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-ui.button type="submit" variant="outline" size="sm">
                            Logout
                        </x-ui.button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- System-wide Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $systemStats['total_users'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Active Users -->
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $systemStats['active_users'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Total Departments -->
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Departments</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $systemStats['total_departments'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Total Projects -->
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Projects</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $systemStats['total_projects'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Today's Attendance Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today's Attendance</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $systemStats['today_attendance'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500">Total check-ins today</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-teal-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Currently Clocked In</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $systemStats['currently_clocked_in'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500">Active right now</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Activities -->
            <x-ui.card>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
                    <p class="text-sm text-gray-600">Latest system activities</p>
                </div>
                
                @if($recentActivities->isEmpty())
                    <x-ui.empty-state 
                        title="No recent activities"
                        description="System activities will appear here"
                    />
                @else
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($recentActivities as $activity)
                            <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    @if($activity['action'] === 'clocked_in')
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $activity['user_name'] }}
                                        <span class="font-normal text-gray-600">
                                            {{ $activity['action'] === 'clocked_in' ? 'clocked in' : 'clocked out' }}
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                                    </p>
                                    @if($activity['message'])
                                        <p class="text-xs text-gray-600 mt-1">{{ $activity['message'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            <!-- Pending Approvals -->
            <x-ui.card>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Pending Leave Approvals</h3>
                    <p class="text-sm text-gray-600">Requires action</p>
                </div>
                
                @if($pendingApprovals->isEmpty())
                    <x-ui.empty-state 
                        title="No pending approvals"
                        description="All leave requests have been processed"
                    />
                @else
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($pendingApprovals as $leave)
                            <div class="p-3 bg-yellow-50 border border-yellow-100 rounded-lg">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $leave->user->name }}
                                            </p>
                                            <x-ui.badge variant="warning" size="sm">Pending</x-ui.badge>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ \Carbon\Carbon::parse($leave->date)->format('M d, Y') }}
                                        </p>
                                        <p class="text-sm text-gray-600">{{ $leave->category->name ?? 'N/A' }}</p>
                                        @if($leave->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $leave->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Quick Action Buttons -->
                                <div class="flex space-x-2 mt-3">
                                    <button
                                        wire:click="quickApprove('{{ $leave->id }}')"
                                        @if($isLoading) disabled @endif
                                        class="flex-1 px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition disabled:opacity-50"
                                    >
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Approve
                                    </button>
                                    <button
                                        wire:click="openApprovalModal('{{ $leave->id }}')"
                                        @if($isLoading) disabled @endif
                                        class="flex-1 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-md transition disabled:opacity-50"
                                    >
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                        Review
                                    </button>
                                    <button
                                        wire:click="quickReject('{{ $leave->id }}')"
                                        @if($isLoading) disabled @endif
                                        class="flex-1 px-3 py-1.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition disabled:opacity-50"
                                    >
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Reject
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
        </div>

        <!-- Department-wise Statistics -->
        <div class="mt-6">
            <x-ui.card>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Department Statistics</h3>
                    <p class="text-sm text-gray-600">Performance by department (this month)</p>
                </div>
                
                @if($departmentStats->isEmpty())
                    <x-ui.empty-state 
                        title="No departments found"
                        description="Create departments to see statistics"
                    />
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Department
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Active Users
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Hours
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Avg Hours/User
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($departmentStats as $dept)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $dept['name'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $dept['active_users'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $dept['total_hours_formatted'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($dept['active_users'] > 0)
                                                    {{ sprintf('%02d:%02d', floor($dept['total_hours_this_month'] / $dept['active_users'] / 3600), floor(($dept['total_hours_this_month'] / $dept['active_users'] % 3600) / 60)) }}
                                                @else
                                                    00:00
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>
        </div>

        <!-- Quick Action Buttons -->
        <div class="mt-8">
            <x-ui.card>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    <p class="text-sm text-gray-600">Common administrative tasks</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    <a href="{{ route('users.index') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                        <div class="flex-shrink-0 bg-blue-500 rounded-lg p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Manage Users</p>
                            <p class="text-xs text-gray-600">Add or edit users</p>
                        </div>
                    </a>

                    <a href="{{ route('departments.index') }}" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                        <div class="flex-shrink-0 bg-purple-500 rounded-lg p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Departments</p>
                            <p class="text-xs text-gray-600">Manage departments</p>
                        </div>
                    </a>

                    <a href="{{ route('projects.index') }}" class="flex items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition">
                        <div class="flex-shrink-0 bg-orange-500 rounded-lg p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Projects</p>
                            <p class="text-xs text-gray-600">Manage projects</p>
                        </div>
                    </a>

                    <a href="{{ route('leave-categories.index') }}" class="flex items-center p-4 bg-teal-50 hover:bg-teal-100 rounded-lg transition">
                        <div class="flex-shrink-0 bg-teal-500 rounded-lg p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Leave Categories</p>
                            <p class="text-xs text-gray-600">Manage leave types</p>
                        </div>
                    </a>

                    <a href="{{ route('reports.index') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition">
                        <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Reports</p>
                            <p class="text-xs text-gray-600">View reports</p>
                        </div>
                    </a>

                    <a href="{{ route('notices.index') }}" class="flex items-center p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-lg p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Notice Board</p>
                            <p class="text-xs text-gray-600">View announcements</p>
                        </div>
                    </a>
                </div>
            </x-ui.card>
        </div>
    </div>

    <!-- Approval Modal -->
    @if($showApprovalModal && $selectedLeave)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showApprovalModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="$wire.closeApprovalModal()"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Review Leave Request
                                </h3>
                                <div class="mt-4 space-y-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Employee:</p>
                                        <p class="text-sm text-gray-900">{{ $selectedLeave->user->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Date:</p>
                                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($selectedLeave->date)->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Category:</p>
                                        <p class="text-sm text-gray-900">{{ $selectedLeave->category->name ?? 'N/A' }}</p>
                                    </div>
                                    @if($selectedLeave->description)
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Description:</p>
                                            <p class="text-sm text-gray-900">{{ $selectedLeave->description }}</p>
                                        </div>
                                    @endif
                                    <div>
                                        <label for="approvalComments" class="block text-sm font-medium text-gray-700 mb-1">
                                            Comments (Optional)
                                        </label>
                                        <textarea
                                            id="approvalComments"
                                            wire:model="approvalComments"
                                            rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Add your comments..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse space-x-reverse space-x-2">
                        <button
                            wire:click="approveWithComments"
                            type="button"
                            @if($isLoading) disabled @endif
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                        >
                            Approve
                        </button>
                        <button
                            wire:click="rejectWithComments"
                            type="button"
                            @if($isLoading) disabled @endif
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                        >
                            Reject
                        </button>
                        <button
                            wire:click="closeApprovalModal"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
