<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Individual Attendance Report</h1>
                    <p class="text-sm text-gray-600 mt-1">Generate detailed attendance report for a specific user</p>
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
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters Card -->
        <x-ui.card class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Report Filters</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- User Selection -->
                @if($isAdmin)
                    <div>
                        <label for="userId" class="block text-sm font-medium text-gray-700 mb-1">
                            User <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="userId"
                            wire:model="userId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('userId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif

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
            </div>

            <div class="flex justify-end space-x-3 mt-4">
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
            <!-- User Information -->
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Name:</span>
                        <p class="font-medium text-gray-900">{{ $reportData['user']['name'] }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Email:</span>
                        <p class="font-medium text-gray-900">{{ $reportData['user']['email'] }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Department:</span>
                        <p class="font-medium text-gray-900">{{ $reportData['user']['department'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Designation:</span>
                        <p class="font-medium text-gray-900">{{ $reportData['user']['designation'] ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <span class="text-sm text-gray-600">Report Period:</span>
                    <p class="font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($reportData['period']['start_date'])->format('M d, Y') }} - 
                        {{ \Carbon\Carbon::parse($reportData['period']['end_date'])->format('M d, Y') }}
                    </p>
                </div>
            </x-ui.card>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <x-ui.card>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Days Present</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['statistics']['days_present'] }}</p>
                            <p class="text-xs text-gray-500">of {{ $reportData['statistics']['total_days'] }} days</p>
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
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['statistics']['total_hours'] }}</p>
                            <p class="text-xs text-gray-500">{{ $reportData['statistics']['total_hours_formatted'] }}</p>
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
                            <p class="text-sm font-medium text-gray-600">Avg Hours/Day</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['statistics']['average_hours_per_day'] }}</p>
                            <p class="text-xs text-gray-500">{{ $reportData['statistics']['average_hours_per_day_formatted'] }}</p>
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
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['statistics']['late_arrivals'] }}</p>
                            <p class="text-xs text-gray-500">After 9:00 AM</p>
                        </div>
                    </div>
                </x-ui.card>
            </div>

            <!-- Visual Statistics -->
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Overview</h3>
                <div class="space-y-4">
                    <x-ui.stat-bar 
                        label="Days Present" 
                        :value="$reportData['statistics']['days_present']" 
                        :max="$reportData['statistics']['total_days']"
                        color="green"
                    />
                    <x-ui.stat-bar 
                        label="Days Absent" 
                        :value="$reportData['statistics']['days_absent']" 
                        :max="$reportData['statistics']['total_days']"
                        color="red"
                    />
                    <x-ui.stat-bar 
                        label="Late Arrivals" 
                        :value="$reportData['statistics']['late_arrivals']" 
                        :max="$reportData['statistics']['days_present']"
                        color="yellow"
                    />
                    <x-ui.stat-bar 
                        label="Early Departures" 
                        :value="$reportData['statistics']['early_departures']" 
                        :max="$reportData['statistics']['days_present']"
                        color="purple"
                    />
                </div>
            </x-ui.card>

            <!-- Export Buttons -->
            <x-ui.card class="mb-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Export Report</h3>
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

            <!-- Attendance Records -->
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Records</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Clock In
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Clock Out
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hours Worked
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reportData['attendances'] as $attendance)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($attendance->in_time)->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($attendance->in_time)->format('l') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($attendance->in_time)->format('h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($attendance->out_time)
                                            {{ \Carbon\Carbon::parse($attendance->out_time)->format('h:i A') }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attendance->worked)
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ sprintf('%02d:%02d', floor($attendance->worked / 3600), floor(($attendance->worked % 3600) / 60)) }}
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">In Progress</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attendance->out_time)
                                            <x-ui.badge variant="success" size="sm">Completed</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="warning" size="sm">In Progress</x-ui.badge>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <x-ui.empty-state 
                                            title="No attendance records found"
                                            description="No attendance records for the selected period"
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
