<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Compensation Leave Requests</h3>
                <p class="text-sm text-gray-500 mt-1">Request compensation days for working on weekends or holidays</p>
            </div>
            <button
                wire:click="openRequestModal"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
            >
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Request Compensation Leave
            </button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6">
                <button 
                    wire:click="$set('activeTab', 'my-requests')"
                    class="@if($activeTab === 'my-requests') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    My Requests
                </button>
                @if($isSupervisor)
                    <button 
                        wire:click="$set('activeTab', 'pending-approvals')"
                        class="@if($activeTab === 'pending-approvals') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    >
                        Pending Approvals
                        @if($pendingApprovals->count() > 0)
                            <span class="ml-2 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs">{{ $pendingApprovals->count() }}</span>
                        @endif
                    </button>
                @endif
                @if($isHR)
                    <button 
                        wire:click="$set('activeTab', 'hr-actions')"
                        class="@if($activeTab === 'hr-actions') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    >
                        HR Actions Required
                        @if($pendingHRActions->count() > 0)
                            <span class="ml-2 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs">{{ $pendingHRActions->count() }}</span>
                        @endif
                    </button>
                @endif
            </nav>
        </div>

        <div class="p-6">
            @if($activeTab === 'my-requests')
                <!-- My Requests Tab -->
                <div class="mb-4">
                    <select wire:model.live="statusFilter" class="px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="supervisor_approved">Supervisor Approved</option>
                        <option value="hr_effected">Effected</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                @if($myRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Work Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested On</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($myRequests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $request->work_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ ucfirst($request->work_type) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $request->days_requested }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($request->status === 'pending')
                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                            @elseif($request->status === 'supervisor_approved')
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Supervisor Approved</span>
                                            @elseif($request->status === 'hr_effected')
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Effected</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm max-w-xs truncate">{{ $request->description ?: '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $myRequests->links() }}
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <p>No compensation leave requests found</p>
                    </div>
                @endif
            @endif

            @if($activeTab === 'pending-approvals' && $isSupervisor)
                <!-- Pending Approvals Tab -->
                @if($pendingApprovals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Work Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingApprovals as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $request->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $request->work_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ ucfirst($request->work_type) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $request->days_requested }}</td>
                                        <td class="px-6 py-4 text-sm max-w-xs truncate">{{ $request->description ?: '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                            <button wire:click="openApproveModal('{{ $request->id }}')" class="text-green-600 hover:text-green-900">Approve</button>
                                            <button wire:click="openRejectModal('{{ $request->id }}')" class="text-red-600 hover:text-red-900">Reject</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $pendingApprovals->links() }}
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <p>No pending approvals</p>
                    </div>
                @endif
            @endif

            @if($activeTab === 'hr-actions' && $isHR)
                <!-- HR Actions Tab -->
                @if($pendingHRActions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Work Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingHRActions as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $request->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $request->work_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $request->days_requested }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $request->supervisorApprover->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                            <button wire:click="openEffectModal('{{ $request->id }}')" class="text-blue-600 hover:text-blue-900">Effect</button>
                                            <button wire:click="openRejectModal('{{ $request->id }}')" class="text-red-600 hover:text-red-900">Reject</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $pendingHRActions->links() }}
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <p>No pending HR actions</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Request Modal -->
    @if($showRequestModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeRequestModal"></div>
                <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                    <h3 class="text-lg font-medium mb-4">Request Compensation Leave</h3>
                    <form wire:submit.prevent="submitRequest" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Work Date *</label>
                            <input type="date" wire:model="workDate" max="{{ now()->format('Y-m-d') }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            @error('workDate') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Work Type *</label>
                            <select wire:model="workType" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="weekend">Weekend</option>
                                <option value="holiday">Public Holiday</option>
                            </select>
                            @error('workType') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Days Requested *</label>
                            <select wire:model="daysRequested" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="0.5">0.5 days</option>
                                <option value="1.0">1.0 day</option>
                                <option value="1.5">1.5 days</option>
                            </select>
                            @error('daysRequested') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model="description" rows="3" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Describe the work done..."></textarea>
                            @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex justify-end space-x-2 mt-6">
                            <button type="button" wire:click="closeRequestModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Approve Modal -->
    @if($showApproveModal && $selectedRequest)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeApproveModal"></div>
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium mb-4">Approve Compensation Leave Request</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Approve {{ $selectedRequest->user->name }}'s request for {{ $selectedRequest->days_requested }} compensation day(s) for working on {{ $selectedRequest->work_date->format('M d, Y') }}?
                    </p>
                    <div class="flex justify-end space-x-2">
                        <button wire:click="closeApproveModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button wire:click="confirmApprove" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Approve</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Effect Modal -->
    @if($showEffectModal && $selectedRequest)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeEffectModal"></div>
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium mb-4">Effect Compensation Leave</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Add {{ $selectedRequest->days_requested }} compensation day(s) to {{ $selectedRequest->user->name }}'s leave balance?
                    </p>
                    <div class="flex justify-end space-x-2">
                        <button wire:click="closeEffectModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button wire:click="confirmEffect" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Effect</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    @if($showRejectModal && $selectedRequest)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeRejectModal"></div>
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-medium mb-4">Reject Request</h3>
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600">
                            Reject {{ $selectedRequest->user->name }}'s compensation leave request?
                        </p>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Rejection *</label>
                            <textarea wire:model="rejectionReason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md" required></textarea>
                            @error('rejectionReason') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 mt-6">
                        <button wire:click="closeRejectModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button wire:click="confirmReject" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Reject</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
