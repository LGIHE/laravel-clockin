<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Leave Management</h1>
                    <p class="text-sm text-gray-600 mt-1">View and manage leave requests</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}">
                        <x-ui.button variant="outline" size="sm">
                            Back to Dashboard
                        </x-ui.button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Apply Leave Section -->
        <div class="mb-6">
            <livewire:leave.apply-leave />
        </div>

        <!-- Filters -->
        <x-ui.card class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- User Filter (Admin/Supervisor only) -->
                @if($isAdmin || $isSupervisor)
                    <div>
                        <label for="userId" class="block text-sm font-medium text-gray-700 mb-1">
                            User
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

                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            Search
                        </label>
                        <input 
                            type="text" 
                            id="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search by name or email..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                @endif

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status
                    </label>
                    <select 
                        id="status"
                        wire:model="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Status</option>
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption->name }}">{{ ucfirst($statusOption->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="categoryId" class="block text-sm font-medium text-gray-700 mb-1">
                        Category
                    </label>
                    <select 
                        id="categoryId"
                        wire:model="categoryId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Date -->
                <div>
                    <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">
                        Start Date
                    </label>
                    <input 
                        type="date" 
                        id="startDate"
                        wire:model="startDate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <!-- End Date -->
                <div>
                    <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">
                        End Date
                    </label>
                    <input 
                        type="date" 
                        id="endDate"
                        wire:model="endDate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <!-- Per Page -->
                <div>
                    <label for="perPage" class="block text-sm font-medium text-gray-700 mb-1">
                        Per Page
                    </label>
                    <select 
                        id="perPage"
                        wire:model.live="perPage"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <x-ui.button wire:click="clearFilters" variant="outline" size="sm">
                    Clear Filters
                </x-ui.button>
                <x-ui.button wire:click="applyFilters" variant="primary" size="sm">
                    Apply Filters
                </x-ui.button>
            </div>
        </x-ui.card>

        <!-- Leave Table -->
        <x-ui.card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if($isAdmin || $isSupervisor)
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortByColumn('user_id')">
                                    <div class="flex items-center space-x-1">
                                        <span>User</span>
                                        @if($sortBy === 'user_id')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($sortOrder === 'asc')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                @endif
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                            @endif
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortByColumn('date')">
                                <div class="flex items-center space-x-1">
                                    <span>Leave Date</span>
                                    @if($sortBy === 'date')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortOrder === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortByColumn('created_at')">
                                <div class="flex items-center space-x-1">
                                    <span>Applied On</span>
                                    @if($sortBy === 'created_at')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortOrder === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">

                        @forelse($leaves as $leave)
                            <tr class="hover:bg-gray-50">
                                @if($isAdmin || $isSupervisor)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $leave->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $leave->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($leave->date)->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($leave->date)->format('l') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $leave->category->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($leave->description)
                                        <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $leave->description }}">
                                            {{ $leave->description }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusName = strtolower($leave->status->name);
                                        $badgeVariant = match($statusName) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'pending' => 'warning',
                                            default => 'default'
                                        };
                                    @endphp
                                    <x-ui.badge variant="{{ $badgeVariant }}" size="sm">
                                        {{ ucfirst($leave->status->name) }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($leave->created_at)->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($leave->created_at)->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <button 
                                            wire:click="viewDetails('{{ $leave->id }}')"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="View Details"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        
                                        @if(($isAdmin || $isSupervisor) && strtolower($leave->status->name) === 'pending')
                                            <button 
                                                wire:click="openApprovalModal('{{ $leave->id }}', 'approve')"
                                                class="text-green-600 hover:text-green-900"
                                                title="Approve"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button 
                                                wire:click="openApprovalModal('{{ $leave->id }}', 'reject')"
                                                class="text-red-600 hover:text-red-900"
                                                title="Reject"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif
                                        
                                        @if($leave->user_id === auth()->id() && strtolower($leave->status->name) === 'pending')
                                            <button 
                                                wire:click="deleteLeave('{{ $leave->id }}')"
                                                wire:confirm="Are you sure you want to delete this leave request?"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ ($isAdmin || $isSupervisor) ? 7 : 6 }}" class="px-6 py-12 text-center">
                                    <x-ui.empty-state 
                                        title="No leave requests found"
                                        description="Try adjusting your filters or apply for a new leave"
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($leaves->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $leaves->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedLeave)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showDetailModal') }" x-show="show" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>

                <!-- Modal panel -->
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Leave Request Details</h3>
                            <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- User Information -->
                            @if($isAdmin || $isSupervisor)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">User Information</h4>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-gray-600">Name:</span>
                                            <span class="ml-2 font-medium text-gray-900">{{ $selectedLeave->user->name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Email:</span>
                                            <span class="ml-2 font-medium text-gray-900">{{ $selectedLeave->user->email }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Department:</span>
                                            <span class="ml-2 font-medium text-gray-900">{{ $selectedLeave->user->department->name ?? 'N/A' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Designation:</span>
                                            <span class="ml-2 font-medium text-gray-900">{{ $selectedLeave->user->designation->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Leave Information -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Leave Information</h4>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Leave Date:</span>
                                        <span class="font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($selectedLeave->date)->format('l, F d, Y') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Category:</span>
                                        <span class="font-medium text-gray-900">{{ $selectedLeave->category->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span>
                                            @php
                                                $statusName = strtolower($selectedLeave->status->name);
                                                $badgeVariant = match($statusName) {
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                    'pending' => 'warning',
                                                    default => 'default'
                                                };
                                            @endphp
                                            <x-ui.badge variant="{{ $badgeVariant }}" size="sm">
                                                {{ ucfirst($selectedLeave->status->name) }}
                                            </x-ui.badge>
                                        </span>
                                    </div>
                                    @if($selectedLeave->description)
                                        <div>
                                            <span class="text-gray-600">Description:</span>
                                            <p class="mt-1 text-gray-900">{{ $selectedLeave->description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Timestamps -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Request Information</h4>
                                <div class="space-y-2 text-xs text-gray-600">
                                    <div class="flex justify-between">
                                        <span>Applied On:</span>
                                        <span>{{ \Carbon\Carbon::parse($selectedLeave->created_at)->format('M d, Y h:i A') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Last Updated:</span>
                                        <span>{{ \Carbon\Carbon::parse($selectedLeave->updated_at)->format('M d, Y h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                        <x-ui.button wire:click="closeDetailModal" variant="outline">
                            Close
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Approval/Rejection Modal -->
    @if($showApprovalModal && $selectedLeave)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showApprovalModal') }" x-show="show" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>

                <!-- Modal panel -->
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ ucfirst($approvalAction) }} Leave Request
                            </h3>
                            <button wire:click="closeApprovalModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Leave Summary -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">User:</span>
                                        <span class="font-medium text-gray-900">{{ $selectedLeave->user->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Leave Date:</span>
                                        <span class="font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($selectedLeave->date)->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Category:</span>
                                        <span class="font-medium text-gray-900">{{ $selectedLeave->category->name }}</span>
                                    </div>
                                    @if($selectedLeave->description)
                                        <div class="pt-2 border-t border-gray-200">
                                            <span class="text-gray-600">Description:</span>
                                            <p class="mt-1 text-gray-900">{{ $selectedLeave->description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Comments -->
                            <div>
                                <label for="approvalComments" class="block text-sm font-medium text-gray-700 mb-1">
                                    Comments (Optional)
                                </label>
                                <textarea 
                                    id="approvalComments"
                                    wire:model="approvalComments"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Add any comments or notes..."
                                ></textarea>
                            </div>

                            <!-- Confirmation Message -->
                            <div class="bg-{{ $approvalAction === 'approve' ? 'green' : 'red' }}-50 border border-{{ $approvalAction === 'approve' ? 'green' : 'red' }}-200 rounded-lg p-3">
                                <p class="text-sm text-{{ $approvalAction === 'approve' ? 'green' : 'red' }}-800">
                                    Are you sure you want to {{ $approvalAction }} this leave request?
                                    @if($approvalAction === 'approve')
                                        The user will be notified of the approval.
                                    @else
                                        The user will be notified of the rejection.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse space-x-3 space-x-reverse">
                        <x-ui.button 
                            wire:click="submitApproval" 
                            variant="{{ $approvalAction === 'approve' ? 'primary' : 'danger' }}"
                        >
                            {{ ucfirst($approvalAction) }}
                        </x-ui.button>
                        <x-ui.button wire:click="closeApprovalModal" variant="outline">
                            Cancel
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
