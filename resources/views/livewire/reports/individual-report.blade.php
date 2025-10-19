<div class="space-y-6 p-6">
    <!-- Breadcrumb Navigation -->
    <!-- <div class="flex justify-between items-center">
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
    </div> -->

    <!-- Main Content Card -->
    <div class="bg-white p-6 rounded-md shadow-sm">
        <!-- Header Row: User Selector, Statistics, Date Range -->
        <div class="flex justify-between items-center mb-6">
            <!-- User Selector (Left) - Always show dropdown -->
            <div class="w-1/3">
                <select 
                    wire:model.live="userId"
                    class="w-full p-2 border border-gray-300 rounded-md text-sm"
                >
                    <option value="">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
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
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div style="height: 350px; position: relative;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
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
                <button 
                    wire:click="generateTimesheet"
                    class="px-3 py-1.5 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50 flex items-center gap-1"
                    @if(!$reportData) disabled @endif
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Timesheet
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
        (function() {
            let attendanceChartInstance = null;

            function initAttendanceChart() {
                const ctx = document.getElementById('attendanceChart');
                if (!ctx) {
                    console.log('Chart canvas not found');
                    return;
                }

                // Destroy existing chart if it exists
                if (attendanceChartInstance) {
                    attendanceChartInstance.destroy();
                    attendanceChartInstance = null;
                }

                const attendances = @json($reportData['attendances']);
                const startDateStr = '{{ $startDate }}';
                const endDateStr = '{{ $endDate }}';
                
                console.log('Initializing chart with', attendances.length, 'attendance records');
                
                // Create a map of dates to hours worked
                const attendanceMap = {};
                attendances.forEach(att => {
                    if (!att.in_time) return;
                    const date = new Date(att.in_time);
                    const dateKey = date.toISOString().split('T')[0];
                    const hours = att.worked ? parseFloat((att.worked / 3600).toFixed(2)) : 0;
                    
                    // If multiple entries on same day, sum them
                    if (attendanceMap[dateKey]) {
                        attendanceMap[dateKey] += hours;
                    } else {
                        attendanceMap[dateKey] = hours;
                    }
                });

                // Generate all dates in range with attendance data
                const chartData = [];
                const labels = [];
                const startDate = new Date(startDateStr + 'T00:00:00');
                const endDate = new Date(endDateStr + 'T23:59:59');
                const currentDate = new Date(startDate);
                
                while (currentDate <= endDate) {
                    const dateKey = currentDate.toISOString().split('T')[0];
                    const hours = attendanceMap[dateKey] || 0;
                    
                    chartData.push(hours);
                    labels.push(dateKey);
                    
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                console.log('Chart data points:', chartData.length);

                // Calculate date range in days
                const daysDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
                
                // Calculate max Y value for better scaling
                const maxHours = Math.max(...chartData, 8);
                const yAxisMax = Math.ceil(maxHours / 2) * 2; // Round up to nearest even number

                // Format labels based on date range
                const formattedLabels = labels.map((dateStr, index) => {
                    const date = new Date(dateStr + 'T00:00:00');
                    if (daysDiff <= 7) {
                        // Show day name and date for week view
                        return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                    } else if (daysDiff <= 31) {
                        // Show month and day for month view
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    } else {
                        // Show every nth label for longer ranges
                        const skipFactor = Math.ceil(daysDiff / 20);
                        if (index % skipFactor === 0) {
                            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        }
                        return '';
                    }
                });

                attendanceChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: formattedLabels,
                        datasets: [{
                            label: 'Hours Worked',
                            data: chartData,
                            backgroundColor: chartData.map(value => {
                                if (value === 0) return '#e5e7eb'; // gray for no attendance
                                if (value < 4) return '#fbbf24'; // yellow for low hours
                                if (value < 8) return '#60a5fa'; // blue for medium hours
                                return '#10b981'; // green for full day
                            }),
                            borderColor: chartData.map(value => {
                                if (value === 0) return '#d1d5db';
                                if (value < 4) return '#f59e0b';
                                if (value < 8) return '#3b82f6';
                                return '#059669';
                            }),
                            borderWidth: 1,
                            borderRadius: 4,
                            maxBarThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                align: 'end'
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                callbacks: {
                                    title: function(context) {
                                        const dateStr = labels[context[0].dataIndex];
                                        const date = new Date(dateStr + 'T00:00:00');
                                        return date.toLocaleDateString('en-US', { 
                                            weekday: 'long', 
                                            year: 'numeric', 
                                            month: 'long', 
                                            day: 'numeric' 
                                        });
                                    },
                                    label: function(context) {
                                        const hours = context.parsed.y;
                                        if (hours === 0) {
                                            return 'No attendance recorded';
                                        }
                                        const h = Math.floor(hours);
                                        const m = Math.round((hours - h) * 60);
                                        return `Hours Worked: ${h}h ${m}m`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 0,
                                    autoSkip: false,
                                    font: {
                                        size: 10
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Date',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    padding: { top: 10 }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                max: yAxisMax,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    stepSize: 2,
                                    callback: function(value) {
                                        return value + 'h';
                                    },
                                    font: {
                                        size: 11
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Hours Worked',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    padding: { bottom: 10 }
                                }
                            }
                        }
                    }
                });

                console.log('Chart initialized successfully');
            }

            // Initialize chart on page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAttendanceChart);
            } else {
                initAttendanceChart();
            }
            
            // Reinitialize chart after Livewire updates
            document.addEventListener('livewire:navigated', function() {
                setTimeout(initAttendanceChart, 100);
            });
            
            // Listen for Livewire component updates
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('morph.updated', function({ el, component }) {
                    setTimeout(initAttendanceChart, 100);
                });
            }
        })();
    </script>
    @endpush
@endif

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-url', (event) => {
            window.open(event.url, '_blank');
        });
    });
</script>
@endpush
