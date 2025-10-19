<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <!-- <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Summary Attendance Report</h1>
                    <p class="text-sm text-gray-600 mt-1">Generate aggregated attendance report for multiple users</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('reports.index') }}">
                        <x-ui.button variant="outline" size="sm">
                            Back to Reports
                        </x-ui.button>
                    </a>
                </div>
            </div>
        </div>
    </div> -->

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters Card -->
        <x-ui.card class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Report Filters</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Start Date -->
                <div>
                    <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="startDate"
                        wire:model="startDate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                    @error('startDate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">
                        End Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="endDate"
                        wire:model="endDate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                    @error('endDate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- User Filter -->
                <div>
                    <label for="userId" class="block text-sm font-medium text-gray-700 mb-1">
                        User (Optional)
                    </label>
                    <select 
                        id="userId"
                        wire:model="userId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Filter -->
                <div>
                    <label for="departmentId" class="block text-sm font-medium text-gray-700 mb-1">
                        Department (Optional)
                    </label>
                    <select 
                        id="departmentId"
                        wire:model="departmentId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Project Filter -->
                <div>
                    <label for="projectId" class="block text-sm font-medium text-gray-700 mb-1">
                        Project (Optional)
                    </label>
                    <select 
                        id="projectId"
                        wire:model="projectId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <x-ui.button wire:click="clearFilters" variant="outline" size="sm">
                    Clear Filters
                </x-ui.button>
                @if($reportData)
                    <x-ui.button wire:click="clearReport" variant="outline" size="sm">
                        Clear Report
                    </x-ui.button>
                @endif
                <x-ui.button wire:click="generateReport" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Generate Report
                </x-ui.button>
            </div>
        </x-ui.card>

        <!-- Report Results -->
        @if($reportData)
            <!-- Overall Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <x-ui.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['overall_statistics']['total_users'] }}</p>
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Hours</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['overall_statistics']['total_hours'] }}</p>
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Avg Hours/User</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['overall_statistics']['average_hours_per_user'] }}</p>
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Late Arrivals</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['overall_statistics']['total_late_arrivals'] }}</p>
                        </div>
                    </div>
                </x-ui.card>
            </div>

            <!-- Visual Statistics -->
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Overall Performance</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Attendance Distribution</h4>
                        <div class="space-y-3">
                            <x-ui.stat-bar 
                                label="Total Days Present" 
                                :value="$reportData['overall_statistics']['total_days_present']" 
                                :max="$reportData['overall_statistics']['total_days_present'] + 100"
                                color="green"
                            />
                            <x-ui.stat-bar 
                                label="Late Arrivals" 
                                :value="$reportData['overall_statistics']['total_late_arrivals']" 
                                :max="$reportData['overall_statistics']['total_days_present']"
                                color="yellow"
                            />
                            <x-ui.stat-bar 
                                label="Early Departures" 
                                :value="$reportData['overall_statistics']['total_early_departures']" 
                                :max="$reportData['overall_statistics']['total_days_present']"
                                color="purple"
                            />
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Hours Distribution</h4>
                        <div class="space-y-3">
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Total Hours Worked</span>
                                    <span class="text-2xl font-bold text-blue-600">{{ $reportData['overall_statistics']['total_hours'] }}</span>
                                </div>
                                <div class="text-xs text-gray-600">Across all users</div>
                            </div>
                            <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Average Hours per User</span>
                                    <span class="text-2xl font-bold text-green-600">{{ $reportData['overall_statistics']['average_hours_per_user'] }}</span>
                                </div>
                                <div class="text-xs text-gray-600">For {{ $reportData['overall_statistics']['total_users'] }} users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <!-- Export Buttons -->
            <x-ui.card class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Export Report</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Report Period: {{ \Carbon\Carbon::parse($reportData['period']['start_date'])->format('M d, Y') }} - 
                            {{ \Carbon\Carbon::parse($reportData['period']['end_date'])->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <x-ui.button wire:click="exportPdf" variant="danger" size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Export PDF
                        </x-ui.button>
                        <x-ui.button wire:click="exportExcel" variant="success" size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Excel
                        </x-ui.button>
                        <x-ui.button wire:click="exportCsv" variant="outline" size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export CSV
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>

            <!-- User Summary Table -->
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">User Summary</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Department
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Days Present
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Hours
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Avg Hours/Day
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Late Arrivals
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Attendance Rate
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reportData['summary'] as $userSummary)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $userSummary['user']['name'] }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $userSummary['user']['email'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $userSummary['user']['department'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $userSummary['statistics']['days_present'] }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            of {{ $userSummary['statistics']['total_days'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $userSummary['statistics']['total_hours'] }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $userSummary['statistics']['total_hours_formatted'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                        {{ $userSummary['statistics']['average_hours_per_day'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                        {{ $userSummary['statistics']['late_arrivals'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $userSummary['statistics']['attendance_rate'] }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <x-ui.empty-state 
                                            title="No data found"
                                            description="No attendance data for the selected filters"
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        @endif
    </div>
</div>
