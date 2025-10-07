<div class="space-y-6 p-6">
    <!-- Breadcrumb Navigation -->
    <div class="flex justify-between items-center">
        <div class="space-x-2">
            <a href="{{ route('dashboard') }}" wire:navigate>
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                    Dashboard
                </button>
            </a>
            <a href="{{ route('reports.index') }}" wire:navigate>
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                    Report
                </button>
            </a>
            <button class="px-4 py-2 bg-lgf-blue text-white rounded-md text-sm">
                Individual
            </button>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white p-6 rounded-md shadow-sm">
        <!-- Header Row: User Selector, Statistics, Date Range -->
        <div class="flex justify-between items-center mb-6">
            <!-- User Selector (Left) -->
            <div class="w-1/3">
                @if($isAdmin)
                    <select 
                        wire:model.live="userId"
                        class="w-full p-2 border border-gray-300 rounded-md text-sm"
                    >
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                @else
                    <div class="p-2 font-medium text-sm">
                        {{ $reportData['user']['name'] ?? auth()->user()->name }}
                    </div>
                @endif
            </div>

            <!-- Statistics Summary (Center) -->
            <div class="flex items-center gap-2">
                @if($reportData)
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ number_format($reportData['statistics']['total_hours'], 2) }}</span> hours worked
                        <span class="mx-1">•</span>
                        <span class="font-medium">{{ $reportData['statistics']['days_present'] }}</span> days
                        <span class="mx-1">•</span>
                        <span class="font-medium">{{ number_format(($reportData['statistics']['days_present'] / max($reportData['statistics']['total_days'], 1)) * 100, 1) }}%</span> attendance
                    </div>
                @endif
            </div>
        </div>

        <!-- Attendance Trend Chart -->
        @if($reportData && count($reportData['attendances']) > 0)
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4">Attendance Trend</h3>
                <div class="h-64 bg-gray-50 rounded-md flex items-center justify-center">
                    <canvas id="attendanceChart" class="max-h-full"></canvas>
                </div>
            </div>
        @endif

        <!-- Controls Row: Pagination, Export Buttons, Date Range -->
        <div class="flex justify-between items-center mb-6">
            <!-- Show Entries -->
            <div class="flex items-center text-sm">
                <span class="mr-2">Show</span>
                <select 
                    wire:model.live="perPage"
                    class="border border-gray-300 rounded-md p-1 text-sm"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="ml-2">entries</span>
            </div>

            <!-- Export Buttons -->
            <div class="flex space-x-2">
                <button 
                    wire:click="exportCsv"
                    class="px-3 py-1.5 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50 flex items-center gap-1"
                    @if(!$reportData) disabled @endif
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    CSV
                </button>
                <button 
                    wire:click="exportJson"
                    class="px-3 py-1.5 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50 flex items-center gap-1"
                    @if(!$reportData) disabled @endif
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    JSON
                </button>
                <button 
                    wire:click="exportPdf"
                    class="px-3 py-1.5 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50 flex items-center gap-1"
                    @if(!$reportData) disabled @endif
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    PDF
                </button>
            </div>

            <!-- Date Range Picker -->
            <div class="flex items-center gap-2 text-sm">
                <input 
                    type="date" 
                    wire:model.live="startDate"
                    class="border border-gray-300 rounded-md p-1.5 text-sm"
                >
                <span>to</span>
                <input 
                    type="date" 
                    wire:model.live="endDate"
                    class="border border-gray-300 rounded-md p-1.5 text-sm"
                >
            </div>
        </div>

        <!-- Loading State -->
        <div wire:loading class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
            <p class="mt-2 text-gray-600 text-sm">Loading report data...</p>
        </div>

        <div wire:loading.remove>
        @if(!$reportData)
            <div class="text-center py-8 text-gray-500 text-sm">
                Please select a user and date range to generate the report.
            </div>
        @elseif(count($reportData['attendances']) === 0)
            <div class="text-center py-8 text-gray-500 text-sm">
                No attendance records found for the selected period.
            </div>
        @else
            <!-- Time Records Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">In Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Out Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Worked</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($paginatedAttendances['data'] as $index => $attendance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ ($paginatedAttendances['from'] + $index) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($attendance->in_time)->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($attendance->in_time)->format('h:i A') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @if($attendance->out_time)
                                        {{ \Carbon\Carbon::parse($attendance->out_time)->format('h:i A') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @if($attendance->worked)
                                        {{ sprintf('%02d:%02d', floor($attendance->worked / 3600), floor(($attendance->worked % 3600) / 60)) }}
                                    @else
                                        <span class="text-gray-400">In Progress</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($attendance->out_time)
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Complete</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800">In Progress</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
                <div class="text-sm text-gray-600">
                    Showing {{ $paginatedAttendances['from'] }} to {{ $paginatedAttendances['to'] }} of {{ $paginatedAttendances['total'] }} entries
                </div>
                
                @if($paginatedAttendances['last_page'] > 1)
                    <div class="flex items-center space-x-2">
                        <button 
                            wire:click="previousPage"
                            @if($currentPage === 1) disabled @endif
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Previous
                        </button>
                        
                        @for($i = 1; $i <= min(5, $paginatedAttendances['last_page']); $i++)
                            <button 
                                wire:click="gotoPage({{ $i }})"
                                class="px-3 py-1 border rounded-md text-sm {{ $currentPage === $i ? 'bg-lgf-blue text-white border-lgf-blue' : 'border-gray-300 hover:bg-gray-50' }}"
                            >
                                {{ $i }}
                            </button>
                        @endfor
                        
                        <button 
                            wire:click="nextPage"
                            @if($currentPage === $paginatedAttendances['last_page']) disabled @endif
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
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

@if($reportData && count($reportData['attendances']) > 0)
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:navigated', function() {
            const ctx = document.getElementById('attendanceChart');
            if (ctx) {
                const attendances = @json($reportData['attendances']);
                const labels = attendances.map(att => {
                    const date = new Date(att.in_time);
                    return date.toLocaleDateString('en-US', { weekday: 'short' });
                });
                const data = attendances.map(att => att.worked ? (att.worked / 3600).toFixed(2) : 0);
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Hours Worked',
                            data: data,
                            backgroundColor: '#10b981',
                            borderColor: '#10b981',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Hours'
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
@endif
