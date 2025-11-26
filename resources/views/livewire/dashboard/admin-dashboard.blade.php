<div>
    <!-- Page Header -->
    <!-- <div class="mb-6">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
    </div> -->

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
                        
                        @if(!(isset($attendanceStatus) && is_array($attendanceStatus) && ($attendanceStatus['clocked_in'] ?? false)))
                            <!-- Project Selection -->
                            <div>
                                <label for="projectSelect" class="block text-sm font-medium text-gray-700 mb-1">
                                    Select Projects <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="projectSelect"
                                    wire:model.live="projectToAdd"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                    @if($isLoading) disabled @endif
                                >
                                    <option value="">Choose a project to add...</option>
                                    @foreach($userProjects as $project)
                                        @if(!in_array($project->id, $selectedProjects))
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('selectedProjects') 
                                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> 
                                @enderror
                                
                                <!-- Selected Projects List -->
                                @if(!empty($selectedProjects))
                                    <div class="mt-2">
                                        <div class="text-xs font-medium text-gray-600 mb-1.5">Selected Projects:</div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($selectedProjects as $projectId)
                                                @php
                                                    $project = $userProjects->firstWhere('id', $projectId);
                                                @endphp
                                                @if($project)
                                                    <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 rounded-full px-3 py-1.5">
                                                        <span class="text-sm text-blue-900">{{ $project->name }}</span>
                                                        <button 
                                                            type="button"
                                                            wire:click="removeProject('{{ $projectId }}')"
                                                            class="text-blue-600 hover:text-blue-800 focus:outline-none"
                                                            @if($isLoading) disabled @endif
                                                        >
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-2 text-xs text-gray-500 italic">No projects selected yet</div>
                                @endif
                            </div>

                            <!-- Predefined Task Selection -->
                            <div>
                                <label for="predefinedTaskSelect" class="block text-sm font-medium text-gray-700 mb-1">
                                    Select Tasks (Optional)
                                </label>
                                <select 
                                    id="predefinedTaskSelect"
                                    wire:model.live="predefinedTaskToAdd"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                    @if($isLoading) disabled @endif
                                >
                                    <option value="">Choose a task to add...</option>
                                    @foreach($predefinedTaskOptions as $option)
                                        @if($option !== 'Other' || !in_array($option, $selectedPredefinedTasks))
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('selectedPredefinedTasks') 
                                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> 
                                @enderror
                                
                                <!-- Selected Predefined Tasks List -->
                                @if(!empty($selectedPredefinedTasks))
                                    <div class="mt-2">
                                        <div class="text-xs font-medium text-gray-600 mb-1.5">Selected Tasks:</div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($selectedPredefinedTasks as $task)
                                                <div class="inline-flex items-center gap-2 bg-purple-50 border border-purple-200 rounded-full px-3 py-1.5">
                                                    <span class="text-sm text-purple-900">{{ $task }}</span>
                                                    <button 
                                                        type="button"
                                                        wire:click="removePredefinedTask('{{ $task }}')"
                                                        class="text-purple-600 hover:text-purple-800 focus:outline-none"
                                                        @if($isLoading) disabled @endif
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Custom Task Selection (shown when "Other" is selected) -->
                            @if($showCustomTaskField)
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label for="taskSelect" class="block text-sm font-medium text-gray-700">
                                            Select Custom Tasks
                                        </label>
                                        <button 
                                            type="button"
                                            wire:click="openCreateTaskModal"
                                            class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1"
                                            @if($isLoading) disabled @endif
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            New Task
                                        </button>
                                    </div>
                                    <select 
                                        id="taskSelect"
                                        wire:model.live="taskToAdd"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                        @if($isLoading) disabled @endif
                                    >
                                        <option value="">Choose a custom task to add...</option>
                                        @foreach($userTasks as $task)
                                            @if(!in_array($task->id, $selectedTasks))
                                                <option value="{{ $task->id }}">
                                                    {{ $task->title }}
                                                    @if($task->project)
                                                        ({{ $task->project->name }})
                                                    @endif
                                                </option>
                                            @endif
                                        @endforeach
                                        <option value="create_new">➕ Create new task</option>
                                    </select>
                                    @error('selectedTasks') 
                                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> 
                                    @enderror
                                    
                                    <!-- Selected Custom Tasks List -->
                                    @if(!empty($selectedTasks))
                                        <div class="mt-2">
                                            <div class="text-xs font-medium text-gray-600 mb-1.5">Selected Custom Tasks:</div>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($selectedTasks as $taskId)
                                                    @php
                                                        $task = $userTasks->firstWhere('id', $taskId);
                                                    @endphp
                                                    @if($task)
                                                        <div class="inline-flex items-center gap-2 bg-indigo-50 border border-indigo-200 rounded-full px-3 py-1.5">
                                                            <div class="flex flex-col">
                                                                <span class="text-sm text-indigo-900 leading-tight">{{ $task->title }}</span>
                                                                @if($task->project)
                                                                    <span class="text-xs text-indigo-600 leading-tight">{{ $task->project->name }}</span>
                                                                @endif
                                                            </div>
                                                            <button 
                                                                type="button"
                                                                wire:click="removeTask('{{ $taskId }}')"
                                                                class="text-indigo-600 hover:text-indigo-800 focus:outline-none"
                                                                @if($isLoading) disabled @endif
                                                            >
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
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
                            @error('clockMessage') 
                                <span class="text-xs text-red-600 mt-1">{{ $message }}</span> 
                            @enderror
                        </div>
                        
                        <!-- Clock In/Out Button -->
                        @php
                            $isClockedIn = isset($attendanceStatus) && is_array($attendanceStatus) && ($attendanceStatus['clocked_in'] ?? false);
                        @endphp
                        
                        {{-- Debug: Show current state --}}
                        {{-- <div class="text-xs text-gray-500 mb-1">
                            Debug: isClockedIn = {{ $isClockedIn ? 'true' : 'false' }}, 
                            showModal = {{ $showPunchOutModal ? 'true' : 'false' }}
                        </div> --}}
                        
                        <button 
                            @if($isClockedIn)
                                wire:click="openPunchOutModalWithData"
                            @else
                                wire:click="clockIn"
                            @endif
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
                                    @if($index < 10)
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
        <!-- Active User Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Active User</h3>
            </div>
            <div class="p-6 flex flex-col items-center">
                <div class="h-64 w-64">
                    <canvas id="activeUserChart"></canvas>
                </div>
                <div class="mt-4 space-y-2 w-full max-w-xs">
                    <div class="flex justify-between items-center text-sm">
                        <div class="flex items-center gap-2">
                            <span class="block w-3 h-3 rounded-sm" style="background-color: #6366f1;"></span>
                            <span class="text-gray-700">Active</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $systemStats['active_users'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <div class="flex items-center gap-2">
                            <span class="block w-3 h-3 rounded-sm" style="background-color: #ef4444;"></span>
                            <span class="text-gray-700">Inactive</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $systemStats['inactive_users'] ?? 0 }}</span>
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

    <!-- Punch Out Modal -->
    @if($showPunchOutModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closePunchOutModal"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                Punch Out
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- Project and Task Info -->
                                @if(isset($attendanceStatus['attendance']))
                                    <div class="bg-gray-50 rounded-lg p-3 space-y-2 text-sm">
                                        @if($attendanceStatus['attendance']->projects && $attendanceStatus['attendance']->projects->isNotEmpty())
                                            <div>
                                                <span class="font-semibold">Projects: </span>
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    @foreach($attendanceStatus['attendance']->projects as $project)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $project->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        @if($attendanceStatus['attendance']->tasks && $attendanceStatus['attendance']->tasks->isNotEmpty())
                                            <div>
                                                <span class="font-semibold">Tasks: </span>
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    @foreach($attendanceStatus['attendance']->tasks as $task)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                            {{ $task->title }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Description Field -->
                                <div>
                                    <label for="punchOutMessage" class="block text-sm font-medium text-gray-700 mb-1">
                                        Description (Optional)
                                    </label>
                                    <textarea
                                        id="punchOutMessage"
                                        wire:model="clockMessage"
                                        rows="3"
                                        placeholder="What did you accomplish today? Add notes about your work..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                    ></textarea>
                                    @error('clockMessage') 
                                        <span class="text-xs text-red-600 mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Task Status Section -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Task Status
                                    </label>
                                    
                                    @if(isset($attendanceStatus['attendance']) && $attendanceStatus['attendance']->tasks && $attendanceStatus['attendance']->tasks->isNotEmpty())
                                        <!-- Show task status options for each task -->
                                        <div class="space-y-3">
                                            @foreach($attendanceStatus['attendance']->tasks as $task)
                                                <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                                                    <div class="font-medium text-sm text-gray-900 mb-2">{{ $task->title }}</div>
                                                    <div class="space-y-1.5">
                                                        <label class="flex items-center space-x-2 cursor-pointer">
                                                            <input 
                                                                type="radio" 
                                                                wire:model="taskStatuses.{{ $task->id }}" 
                                                                value="completed"
                                                                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                                            >
                                                            <span class="text-sm text-gray-700">✓ Completed</span>
                                                        </label>
                                                        <label class="flex items-center space-x-2 cursor-pointer">
                                                            <input 
                                                                type="radio" 
                                                                wire:model="taskStatuses.{{ $task->id }}" 
                                                                value="in-progress"
                                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                            >
                                                            <span class="text-sm text-gray-700">⟳ In Progress</span>
                                                        </label>
                                                        <label class="flex items-center space-x-2 cursor-pointer">
                                                            <input 
                                                                type="radio" 
                                                                wire:model="taskStatuses.{{ $task->id }}" 
                                                                value="on-hold"
                                                                class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300"
                                                            >
                                                            <span class="text-sm text-gray-700">⏸ On Hold</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <!-- Show message when no task is selected -->
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-center gap-2 text-gray-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="text-sm">No tasks were selected for this session</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        type="button" 
                        wire:click="confirmClockOut"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                        @if($isLoading) disabled @endif
                    >
                        @if($isLoading)
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        @endif
                        Confirm Punch Out
                    </button>
                    <button 
                        type="button" 
                        wire:click="closePunchOutModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Create Task Modal -->
    @if($showCreateTaskModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" wire:click="closeCreateTaskModal"></div>

            <!-- Modal panel -->
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modal-title">
                                Create New Task
                            </h3>
                            <div class="mt-2 space-y-4">
                                <!-- Task Title -->
                                <div>
                                    <label for="newTaskTitle" class="block text-sm font-medium text-gray-700 mb-1">
                                        Task Title <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="newTaskTitle"
                                        wire:model="newTaskTitle"
                                        placeholder="Enter task title"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('newTaskTitle') 
                                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Task Description -->
                                <div>
                                    <label for="newTaskDescription" class="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <textarea 
                                        id="newTaskDescription"
                                        wire:model="newTaskDescription"
                                        placeholder="Enter task description (optional)"
                                        rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    ></textarea>
                                    @error('newTaskDescription') 
                                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Start Date -->
                                <div>
                                    <label for="newTaskStartDate" class="block text-sm font-medium text-gray-700 mb-1">
                                        Start Date <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="date" 
                                        id="newTaskStartDate"
                                        wire:model="newTaskStartDate"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('newTaskStartDate') 
                                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div>
                                    <label for="newTaskEndDate" class="block text-sm font-medium text-gray-700 mb-1">
                                        End Date
                                    </label>
                                    <input 
                                        type="date" 
                                        id="newTaskEndDate"
                                        wire:model="newTaskEndDate"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('newTaskEndDate') 
                                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        type="button"
                        wire:click="createTask"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Create Task
                    </button>
                    <button 
                        type="button"
                        wire:click="closeCreateTaskModal"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('activeUserChart');
    if (ctx) {
        const activeUsers = {{ $systemStats['active_users'] ?? 0 }};
        const inactiveUsers = {{ $systemStats['inactive_users'] ?? 0 }};
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: [activeUsers, inactiveUsers],
                    backgroundColor: [
                        '#6366f1', // Indigo for active
                        '#ef4444'  // Red for inactive
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = activeUsers + inactiveUsers;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
});

// Listen for Livewire updates to refresh the chart
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el, component }) => {
        if (component.name === 'dashboard.admin-dashboard') {
            const ctx = document.getElementById('activeUserChart');
            if (ctx) {
                // Destroy existing chart if it exists
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }
                
                // Recreate the chart with new data
                const activeUsers = {{ $systemStats['active_users'] ?? 0 }};
                const inactiveUsers = {{ $systemStats['inactive_users'] ?? 0 }};
                
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active', 'Inactive'],
                        datasets: [{
                            data: [activeUsers, inactiveUsers],
                            backgroundColor: ['#6366f1', '#ef4444'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed;
                                        const total = activeUsers + inactiveUsers;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        }
    });
});
</script>
@endpush
