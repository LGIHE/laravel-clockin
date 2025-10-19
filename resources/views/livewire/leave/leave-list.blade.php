<div class="space-y-6 p-6">
    <!-- <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Leave Management</h1>
    </div> -->

    <!-- Leave Balance Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-md shadow-sm">
            <div class="text-sm text-gray-500">Approved Leaves</div>
            <div class="text-2xl font-bold text-green-600">{{ $approvedDays }} days</div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-sm">
            <div class="text-sm text-gray-500">Pending Leaves</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $pendingDays }} days</div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-sm">
            <div class="text-sm text-gray-500">
                {{ $isAdmin || $isSupervisor ? 'Pending Approvals' : 'Total Requests' }}
            </div>
            <div class="text-2xl font-bold text-blue-600">
                {{ $isAdmin || $isSupervisor ? $pendingCount : $totalRequests }}
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-md shadow-sm">
    <div class="bg-white p-6 rounded-md shadow-sm">
        @if($isAdmin || $isSupervisor)
            <!-- Tabs for admin/supervisor -->
            <div class="mb-4">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button
                            wire:click="$set('activeTab', 'my-leaves')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'my-leaves' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                        >
                            My Leaves
                        </button>
                        <button
                            wire:click="$set('activeTab', 'all-leaves')"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'all-leaves' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                        >
                            All Leaves
                            @if($pendingCount > 0)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        </button>
                    </nav>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Filters:</span>
            </div>

            <div class="flex space-x-4 flex-wrap gap-2">
                <select 
                    wire:model.live="categoryFilter"
                    class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 w-[180px]"
                >
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <select 
                    wire:model.live="statusFilter"
                    class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 w-[180px]"
                >
                    <option value="">All Status</option>
                    <option value="PENDING">Pending</option>
                    <option value="APPROVED">Approved</option>
                    <option value="REJECTED">Rejected</option>
                </select>

                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="searchTerm"
                    placeholder="Search..."
                    class="border border-gray-300 p-2 rounded-md w-64"
                />
            </div>
        </div>

        <!-- Table Content -->
        @if($isLoading)
            <div class="space-y-2">
                <div class="h-10 w-full bg-gray-200 rounded animate-pulse"></div>
                <div class="h-10 w-full bg-gray-200 rounded animate-pulse"></div>
                <div class="h-10 w-full bg-gray-200 rounded animate-pulse"></div>
            </div>
        @else
            @php
                $displayLeaves = $activeTab === 'my-leaves' || !($isAdmin || $isSupervisor) ? $myLeaves : $allLeaves;
                $showUserColumn = $activeTab === 'all-leaves' && ($isAdmin || $isSupervisor);
            @endphp

            @if($displayLeaves->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">#</th>
                                @if($showUserColumn)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($displayLeaves as $index => $leave)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                    @if($showUserColumn)
                                        <td class="px-6 py-4 whitespace-nowrap">User #{{ $leave->user_id }}</td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $leave->category->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($leave->date)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $leave->end_date ? \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') : \Carbon\Carbon::parse($leave->date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $leave->end_date ? \Carbon\Carbon::parse($leave->date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 : 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusName = strtolower($leave->status->name);
                                        @endphp
                                        @if(in_array($statusName, ['approved', 'granted']))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500 text-white hover:bg-green-600">Approved</span>
                                        @elseif($statusName === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white hover:bg-red-600">Rejected</span>
                                        @elseif($statusName === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500 text-white hover:bg-yellow-600">Pending</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500 text-white">{{ ucfirst($statusName) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 max-w-xs truncate" title="{{ $leave->description }}">
                                        {{ $leave->description ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex gap-2">
                                            @if(($isAdmin || $isSupervisor) && strtolower($leave->status->name) === 'pending')
                                                <button
                                                    wire:click="approveLeave('{{ $leave->id }}')"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-600 bg-white hover:bg-green-50 border-gray-300"
                                                >
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Approve
                                                </button>
                                                <button
                                                    wire:click="openRejectDialog('{{ $leave->id }}')"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-600 bg-white hover:bg-red-50 border-gray-300"
                                                >
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Reject
                                                </button>
                                            @endif
                                            @if($leave->user_id === auth()->id() && strtolower($leave->status->name) === 'pending')
                                                <div class="relative">
                                                    <button
                                                        onclick="document.getElementById('menu-{{ $leave->id }}').classList.toggle('hidden')"
                                                        class="inline-flex items-center px-2 py-1 text-gray-600 hover:text-gray-900"
                                                    >
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                        </svg>
                                                    </button>
                                                    <div id="menu-{{ $leave->id }}" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                        <div class="py-1">
                                                            <button
                                                                wire:click="openDeleteDialog('{{ $leave->id }}')"
                                                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
                                                            >
                                                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(!($isAdmin || $isSupervisor) && $leave->user_id !== auth()->id())
                                                <span class="text-sm text-gray-400">-</span>
                                            @elseif(strtolower($leave->status->name) !== 'pending' && $leave->user_id !== auth()->id())
                                                <span class="text-sm text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-sm text-gray-600">
                    Showing {{ $displayLeaves->count() }} entries
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <p class="text-lg">No leave requests found</p>
                    <p class="text-sm mt-2">Try adjusting your filters</p>
                </div>
            @endif
        @endif
    </div>

    <!-- Delete Confirmation Dialog -->
    @if($showDeleteDialog)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showDeleteDialog') }" x-show="show" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Are you sure?</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        This action cannot be undone. This will permanently delete your leave request.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button
                            wire:click="confirmDelete"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Delete
                        </button>
                        <button
                            wire:click="closeDeleteDialog"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Leave Dialog -->
    @if($showRejectDialog)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showRejectDialog') }" x-show="show" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Reject Leave Request</h3>
                                <p class="mt-1 text-sm text-gray-500">Please provide a reason for rejecting this leave request.</p>
                            </div>
                            <button wire:click="closeRejectDialog" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4 py-4">
                            <div class="space-y-2">
                                <label for="rejectionReason" class="block text-sm font-medium text-gray-700">Rejection Reason *</label>
                                <textarea 
                                    id="rejectionReason"
                                    wire:model="rejectionReason"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter reason for rejection..."
                                ></textarea>
                                @error('rejectionReason')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button
                            wire:click="confirmReject"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Reject Leave
                        </button>
                        <button
                            wire:click="closeRejectDialog"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

