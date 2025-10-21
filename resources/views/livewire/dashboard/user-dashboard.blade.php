<div>
    <!-- Page Header -->
    <!-- <div class="mb-6">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
    </div> -->

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
        <div class="bg-green-50 rounded-lg shadow border border-green-200 mb-6">
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
                    @if(!$this->isClockedIn)
                        <!-- Punch In View -->
                        <div class="space-y-3">
                            <!-- Project Selection -->
                            <div>
                                <label for="projectSelect" class="block text-sm font-medium text-gray-700 mb-1">
                                    Select Projects <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="projectSelect"
                                    wire:model.live="projectToAdd"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                                    <label for="taskSelect" class="block text-sm font-medium text-gray-700 mb-1">
                                        Select Custom Tasks
                                    </label>
                                    <select 
                                        id="taskSelect"
                                        wire:model.live="taskToAdd"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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

                            <!-- Comment -->
                            <div>
                                <label for="clockMessage" class="block text-sm font-medium text-gray-700 mb-1">
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
                                @error('clockMessage') 
                                    <span class="text-xs text-red-600 mt-1">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    @else
                        <!-- Punch Out View -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                <span class="text-sm font-semibold text-green-700">Currently Clocked In</span>
                            </div>
                            @if($attendanceStatus['in_time'] ?? null)
                                <div class="space-y-2 text-sm text-gray-700">
                                    <div>
                                        <span class="font-medium">Clocked in at: </span>
                                        <span>{{ \Carbon\Carbon::parse($attendanceStatus['in_time'])->format('h:i a') }}</span>
                                    </div>
                                    @if(isset($attendanceStatus['attendance']))
                                        @if($attendanceStatus['attendance']->projects && $attendanceStatus['attendance']->projects->isNotEmpty())
                                            <div>
                                                <span class="font-medium">Projects: </span>
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
                                                <span class="font-medium">Tasks: </span>
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    @foreach($attendanceStatus['attendance']->tasks as $task)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                            {{ $task->title }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <div class="space-y-3">
                            <!-- Work Summary -->
                            <div>
                                <label for="clockMessage" class="block text-sm font-medium text-gray-700 mb-1">
                                    Work Summary (Optional)
                                </label>
                                <textarea
                                    id="clockMessage"
                                    wire:model="clockMessage"
                                    rows="3"
                                    placeholder="What did you accomplish today? Add notes about your work..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                    @if($isLoading) disabled @endif
                                ></textarea>
                            </div>


                        </div>
                    @endif

                    <button
                        @if($this->isClockedIn)
                            wire:click="openPunchOutModalWithData"
                        @else
                            wire:click="clockIn"
                        @endif
                        class="w-full px-4 py-2 text-white rounded-md font-medium transition {{ $this->isClockedIn ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600' }}"
                        @if($isLoading) disabled @endif
                    >
                        @if($isLoading)
                            <svg class="animate-spin inline-block -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        @endif
                        {{ $this->isClockedIn ? 'Punch Out' : 'Punch In' }}
                    </button>
                </div>

                <div class="space-y-1 max-h-64 overflow-y-auto">
                    @if(isset($dashboardData['recent_attendance']) && count($dashboardData['recent_attendance']) > 0)
                        @foreach($dashboardData['recent_attendance']->take(2) as $attendance)
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
            <div class="p-6">
                @if($chartData && count($chartData) > 0)
                    @php
                        $maxHours = max(array_column($chartData, 'hours'));
                        $totalHours = array_sum(array_column($chartData, 'hours'));
                        $maxHours = $maxHours > 0 ? ceil($maxHours) : 8; // Round up for better display
                        
                        // Calculate SVG points for the line
                        $pointsCount = count($chartData);
                        $svgWidth = 100;
                        $svgHeight = 100;
                        $points = [];
                        $pathCoordinates = [];
                        
                        foreach ($chartData as $index => $day) {
                            $x = ($index / ($pointsCount - 1)) * $svgWidth;
                            $y = $day['hours'] > 0 ? ($svgHeight - (($day['hours'] / $maxHours) * $svgHeight)) : $svgHeight;
                            $points[] = compact('x', 'y');
                            $pathCoordinates[] = ($index === 0 ? 'M' : 'L') . " $x,$y";
                        }
                        
                        $linePath = implode(' ', $pathCoordinates);
                        
                        // Create area path (same as line but closes at bottom)
                        $areaPath = $linePath . " L $svgWidth,$svgHeight L 0,$svgHeight Z";
                    @endphp
                    
                    <!-- Chart Container -->
                    <div class="h-[320px] relative">
                        <!-- SVG Chart -->
                        <svg 
                            viewBox="0 0 100 100" 
                            preserveAspectRatio="none"
                            class="absolute inset-0 w-full h-full"
                            style="height: calc(100% - 40px);"
                        >
                            <!-- Grid lines -->
                            <defs>
                                <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
                                    <path d="M 20 0 L 0 0 0 20" fill="none" stroke="#e5e7eb" stroke-width="0.5"/>
                                </pattern>
                                <linearGradient id="areaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" style="stop-color:#10b981;stop-opacity:0.3" />
                                    <stop offset="100%" style="stop-color:#10b981;stop-opacity:0.05" />
                                </linearGradient>
                            </defs>
                            
                            <!-- Horizontal grid lines -->
                            @for($i = 0; $i <= 4; $i++)
                                @php $yPos = ($i / 4) * 100; @endphp
                                <line 
                                    x1="0" 
                                    y1="{{ $yPos }}" 
                                    x2="100" 
                                    y2="{{ $yPos }}" 
                                    stroke="#e5e7eb" 
                                    stroke-width="0.3"
                                    stroke-dasharray="3,3"
                                />
                            @endfor
                            
                            @if($totalHours > 0)
                                <!-- Area under the line -->
                                <path 
                                    d="{{ $areaPath }}" 
                                    fill="url(#areaGradient)"
                                />
                                
                                <!-- The line -->
                                <path 
                                    d="{{ $linePath }}" 
                                    fill="none" 
                                    stroke="#10b981" 
                                    stroke-width="0.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                
                                <!-- Data points -->
                                @foreach($points as $point)
                                    <circle 
                                        cx="{{ $point['x'] }}" 
                                        cy="{{ $point['y'] }}" 
                                        r="1.5" 
                                        fill="#10b981"
                                        stroke="white"
                                        stroke-width="0.5"
                                    />
                                @endforeach
                            @endif
                        </svg>
                        
                        <!-- Y-axis labels -->
                        <div class="absolute left-0 top-0 flex flex-col justify-between text-xs text-gray-500 pr-2" style="height: calc(100% - 40px);">
                            @for($i = 0; $i <= 4; $i++)
                                <span class="-mt-2">{{ $maxHours - ($i * ($maxHours / 4)) }}</span>
                            @endfor
                        </div>
                        
                        <!-- X-axis labels -->
                        <div class="absolute bottom-0 left-0 right-0 flex justify-between text-xs text-gray-700 font-medium px-8">
                            @foreach($chartData as $day)
                                <div class="flex-1 text-center">{{ $day['name'] }}</div>
                            @endforeach
                        </div>
                        
                        <!-- Interactive overlay for tooltips -->
                        <div class="absolute inset-0 flex" style="height: calc(100% - 40px); margin-left: 2rem;">
                            @foreach($chartData as $index => $day)
                                <div class="flex-1 relative group cursor-pointer">
                                    @if($day['hours'] > 0)
                                        <!-- Tooltip -->
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                            <div class="bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                                {{ number_format($day['hours'], 1) }}h
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                                    <div class="border-4 border-transparent border-t-gray-900"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    @if($totalHours == 0)
                        <div class="mt-4 text-center text-sm text-gray-500">
                            <p>No attendance recorded in the last 7 days</p>
                        </div>
                    @endif
                @else
                    <div class="h-[320px] flex items-center justify-center text-gray-500">
                        <div class="text-center">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <p class="text-sm">No data available</p>
                            <p class="text-xs text-gray-400 mt-1">Start tracking your hours to see the chart</p>
                        </div>
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
                                        <h4 class="font-medium text-sm">{{ $notice->subject }}</h4>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit($notice->message, 100) }}</p>
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
</div>
