<div>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Supervisor Dashboard</h1>
    </div>

    <!-- Team Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Team Members -->
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Team Members</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $teamAttendance['total_team_members'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Clocked In -->
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Clocked In</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $teamAttendance['clocked_in'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Not Clocked In -->
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Not Clocked In</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $teamAttendance['not_clocked_in'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Pending Leaves -->
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Leaves</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $teamStats['pending_leave_requests'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Team Hours Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Team Hours</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $teamStats['total_team_hours_formatted'] ?? '00:00' }}
                        </p>
                        <p class="text-xs text-gray-500">This month</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-pink-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Average Hours Per Member</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $teamStats['average_hours_formatted'] ?? '00:00' }}
                        </p>
                        <p class="text-xs text-gray-500">This month</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Team Attendance Summary -->
            <x-ui.card>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Team Attendance Today</h3>
                    <p class="text-sm text-gray-600">Current attendance status</p>
                </div>
                
                @if(!$teamAttendance || !isset($teamAttendance['attendance_records']) || $teamAttendance['attendance_records']->isEmpty())
                    <x-ui.empty-state 
                        title="No attendance records"
                        description="No team members have clocked in today"
                    />
                @else
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($teamAttendance['attendance_records'] as $attendance)
                            @if($attendance->user)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $attendance->user->name }}
                                            </p>
                                            @if($attendance->out_time)
                                                <x-ui.badge variant="default" size="sm">Clocked Out</x-ui.badge>
                                            @else
                                                <x-ui.badge variant="success" size="sm">Clocked In</x-ui.badge>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-4 mt-1 text-xs text-gray-600">
                                            <span>In: {{ \Carbon\Carbon::parse($attendance->in_time)->format('h:i A') }}</span>
                                            @if($attendance->out_time)
                                            <span>Out: {{ \Carbon\Carbon::parse($attendance->out_time)->format('h:i A') }}</span>
                                        @endif
                                    </div>
                                    @if($attendance->user->department)
                                        <p class="text-xs text-gray-500 mt-1">{{ $attendance->user->department->name }}</p>
                                    @endif
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
                            @endif
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            <!-- Pending Leave Approvals -->
            <x-ui.card>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Pending Leave Approvals</h3>
                    <p class="text-sm text-gray-600">Requires your action</p>
                </div>
                
                @if($pendingLeaves->isEmpty())
                    <x-ui.empty-state 
                        title="No pending leaves"
                        description="All leave requests have been processed"
                    />
                @else
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($pendingLeaves as $leave)
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

        <!-- Team Members List -->
        <div class="mt-6">
            <x-ui.card>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Team Members</h3>
                    <p class="text-sm text-gray-600">Your direct reports</p>
                </div>
                
                @if($teamMembers->isEmpty())
                    <x-ui.empty-state 
                        title="No team members"
                        description="You don't have any direct reports assigned"
                    />
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Department
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Designation
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($teamMembers as $member)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $member->department->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $member->designation->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($member->status == 1)
                                                <x-ui.badge variant="success" size="sm">Active</x-ui.badge>
                                            @else
                                                <x-ui.badge variant="danger" size="sm">Inactive</x-ui.badge>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
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
    
    <!-- Footer -->
    <div class="mt-6 text-center text-sm text-gray-500">
        © 2025 lgf & made with ❤️
    </div>

