<div class="space-y-6 p-6">
    <!-- Tab buttons (Dashboard/Attendance) -->
    <div class="flex justify-between items-center">
        <div class="space-x-2">
            <a href="{{ route('dashboard') }}">
                <button class="px-4 py-2 border border-gray-300 rounded-md bg-white hover:bg-gray-50">
                    Dashboard
                </button>
            </a>
            <button class="px-4 py-2 border border-blue-600 rounded-md bg-blue-600 text-white">
                Attendance
            </button>
        </div>
    </div>

    <!-- Main content card -->
    <div class="bg-white p-6 rounded-md shadow-sm">
        <!-- Header with user selector and stats -->
        <div class="flex justify-between items-center mb-6">
            <!-- User selector (disabled, showing current user only) -->
            <div class="w-1/3">
                <select 
                    class="w-full p-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed"
                    disabled
                >
                    <option>{{ $user->name }}</option>
                </select>
            </div>

            <!-- Statistics display -->
            <div class="flex items-center gap-2">
                @if(count($statistics) > 0)
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ $statistics['totalHours'] }}</span> hours worked
                        •
                        <span class="font-medium">{{ $statistics['daysWorked'] }}</span> days
                        •
                        <span class="font-medium">{{ $statistics['attendancePercentage'] }}%</span> attendance
                    </div>
                @endif
            </div>
        </div>

        <!-- Controls section -->
        <div class="flex flex-col space-y-6">
            <div class="flex justify-between items-center">
                <!-- Entries per page selector -->
                <div class="flex items-center">
                    <span class="mr-2">Show</span>
                    <select 
                        wire:model.live="pageSize"
                        class="border border-gray-300 rounded-md p-1"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="ml-2">entries</span>
                </div>

                <!-- Export buttons -->
                <div class="flex space-x-2">
                    <button 
                        wire:click="exportCsv"
                        class="flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white hover:bg-gray-50"
                        @if($isLoading || $totalRecords == 0) disabled @endif
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        CSV
                    </button>
                    <button 
                        wire:click="exportJson"
                        class="flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white hover:bg-gray-50"
                        @if($isLoading || $totalRecords == 0) disabled @endif
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        JSON
                    </button>
                    <button 
                        wire:click="exportPdf"
                        class="flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white hover:bg-gray-50"
                        @if($isLoading || $totalRecords == 0) disabled @endif
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        PDF
                    </button>
                    <button 
                        class="flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white hover:bg-gray-50"
                        @if($isLoading || $totalRecords == 0) disabled @endif
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Timesheet
                    </button>
                </div>

                <!-- Date range picker -->
                <div class="flex items-center gap-2">
                    <input 
                        type="date" 
                        wire:model.blur="startDate"
                        class="px-3 py-2 border border-gray-300 rounded-md"
                    >
                    <span class="text-gray-500">-</span>
                    <input 
                        type="date" 
                        wire:model.blur="endDate"
                        class="px-3 py-2 border border-gray-300 rounded-md"
                    >
                </div>
            </div>

            <!-- Loading state -->
            @if($isLoading)
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                    <p class="mt-2 text-gray-600">Loading report data...</p>
                </div>
            @elseif(count($attendances) == 0)
                <!-- Empty state -->
                <div class="text-center py-8 text-gray-500">
                    No attendance records found for the selected period.
                </div>
            @else
                <!-- Attendance table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                    #
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    In Time
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Out Time
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Worked
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($attendances as $index => $record)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ (($currentPage - 1) * $pageSize) + $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $record['date'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center gap-1">
                                            {{ $record['inTime'] }}
                                            @if($record['inMessage'])
                                                <span class="relative group">
                                                    <svg class="w-3.5 h-3.5 text-green-500 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                    </svg>
                                                    <div class="hidden group-hover:block absolute z-10 w-48 p-2 bg-gray-900 text-white text-xs rounded shadow-lg bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                                        {{ $record['inMessage'] }}
                                                    </div>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center gap-1">
                                            {{ $record['outTime'] ?? '-' }}
                                            @if($record['outMessage'])
                                                <span class="relative group">
                                                    <svg class="w-3.5 h-3.5 text-green-500 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                    </svg>
                                                    <div class="hidden group-hover:block absolute z-10 w-48 p-2 bg-gray-900 text-white text-xs rounded shadow-lg bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                                        {{ $record['outMessage'] }}
                                                    </div>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $record['worked'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $record['status'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-between items-center mt-4">
                    <div class="text-sm text-gray-600">
                        Showing {{ (($currentPage - 1) * $pageSize) + 1 }} to {{ min($currentPage * $pageSize, $totalRecords) }} of {{ $totalRecords }} entries
                    </div>
                    
                    @if($totalPages > 1)
                        <div class="flex items-center gap-2">
                            <!-- Previous button -->
                            <button 
                                wire:click="previousPage"
                                @if($currentPage == 1) disabled @endif
                                class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Previous
                            </button>

                            <!-- Page numbers -->
                            @for($i = 1; $i <= min(5, $totalPages); $i++)
                                <button 
                                    wire:click="setPage({{ $i }})"
                                    class="px-3 py-1 border rounded-md {{ $currentPage == $i ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-50' }}"
                                >
                                    {{ $i }}
                                </button>
                            @endfor

                            <!-- Next button -->
                            <button 
                                wire:click="nextPage"
                                @if($currentPage == $totalPages) disabled @endif
                                class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Next
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
