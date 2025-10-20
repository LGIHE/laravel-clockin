<div class="space-y-6">
    <!-- Leave Balance Summary -->
    @if(count($leaveBalances) > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Balance</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($leaveBalances as $categoryId => $balance)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $balance['category'] }}</h4>
                        <div class="flex items-baseline space-x-2">
                            <span class="text-2xl font-bold text-gray-900">{{ $balance['remaining'] }}</span>
                            <span class="text-sm text-gray-500">/ {{ $balance['total_available'] ?? $balance['total'] }} days</span>
                        </div>
                        @if(isset($balance['carried_forward']) && $balance['carried_forward'] > 0)
                            <div class="mt-1 text-xs text-blue-600">
                                Includes {{ $balance['carried_forward'] }} carried forward
                                @if(isset($balance['carryforward_expires_at']))
                                    (expires {{ $balance['carryforward_expires_at'] }})
                                @endif
                            </div>
                        @endif
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>Used: {{ $balance['used'] }}</span>
                                @if($balance['total'] > 0)
                                    <span>{{ round(($balance['used'] / $balance['total']) * 100) }}%</span>
                                @else
                                    <span>-</span>
                                @endif
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @if($balance['total'] > 0)
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(($balance['used'] / $balance['total']) * 100, 100) }}%"></div>
                                @else
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Apply Leave Button -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Apply for Leave</h3>
                <p class="text-sm text-gray-500 mt-1">Submit a new leave request</p>
            </div>
            <button
                wire:click="openDialog"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Apply for Leave
            </button>
        </div>
    </div>

    <!-- Leave History -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave History</h3>
        
        <!-- Filters -->
        <div class="flex space-x-4 mb-4">
            <select 
                wire:model.live="statusFilter"
                class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            >
                <option value="">All Status</option>
                <option value="PENDING">Pending</option>
                <option value="APPROVED">Approved</option>
                <option value="REJECTED">Rejected</option>
            </select>
        </div>

        @if($leaves->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied On</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($leaves as $leave)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $leave->category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($leave->date)->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $leave->end_date ? \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') : \Carbon\Carbon::parse($leave->date)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $leave->end_date ? \Carbon\Carbon::parse($leave->date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 : 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusName = strtolower($leave->status->name);
                                    @endphp
                                    @if(in_array($statusName, ['approved', 'granted']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500 text-white">Approved</span>
                                    @elseif($statusName === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white">Rejected</span>
                                    @elseif($statusName === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500 text-white">Pending</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500 text-white">{{ ucfirst($statusName) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 max-w-xs truncate text-sm text-gray-900" title="{{ $leave->description }}">
                                    {{ $leave->description ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $leave->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <p class="text-lg">No leave history found</p>
                <p class="text-sm mt-2">Your leave applications will appear here</p>
            </div>
        @endif
    </div>

    <!-- Leave Application Dialog -->
    @if($showDialog)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showDialog') }" x-show="show" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="submit">
                        <div class="px-6 pt-5 pb-4 bg-white">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Apply for Leave</h3>
                                    <p class="mt-1 text-sm text-gray-500">Fill in the details for your leave request</p>
                                </div>
                                <button type="button" wire:click="closeDialog" class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                <!-- Leave Category -->
                                <div>
                                    <label for="leaveCategoryId" class="block text-sm font-medium text-gray-700 mb-1">
                                        Leave Category <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        id="leaveCategoryId"
                                        wire:model="leaveCategoryId"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('leaveCategoryId') border-red-500 @enderror"
                                        required
                                    >
                                        <option value="">Select a category</option>
                                        @foreach($leaveCategories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }} ({{ $leaveBalances[$category->id]['remaining'] ?? 0 }} days remaining)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('leaveCategoryId')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date Range -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">
                                            Start Date <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="date" 
                                            id="startDate"
                                            wire:model="startDate"
                                            min="{{ now()->addDay()->format('Y-m-d') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('startDate') border-red-500 @enderror"
                                            required
                                        >
                                        @error('startDate')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">
                                            End Date <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="date" 
                                            id="endDate"
                                            wire:model="endDate"
                                            min="{{ now()->addDay()->format('Y-m-d') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('endDate') border-red-500 @enderror"
                                            required
                                        >
                                        @error('endDate')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                @if($totalDays > 0)
                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                        <p class="text-sm text-blue-800">
                                            <span class="font-medium">Total days:</span> {{ $totalDays }} day{{ $totalDays > 1 ? 's' : '' }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                        Description (Optional)
                                    </label>
                                    <textarea 
                                        id="description"
                                        wire:model="description"
                                        rows="3"
                                        maxlength="500"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                        placeholder="Reason for leave..."
                                    ></textarea>
                                    <div class="flex justify-between mt-1">
                                        @error('description')
                                            <p class="text-sm text-red-600">{{ $message }}</p>
                                        @else
                                            <p class="text-xs text-gray-500">Provide a brief reason for your leave request</p>
                                        @enderror
                                        <p class="text-xs text-gray-500">{{ strlen($description) }}/500</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-3 bg-gray-50 sm:flex sm:flex-row-reverse gap-2">
                            <button
                                type="submit"
                                :disabled="$isLoading"
                                class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                @if($isLoading)
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Submitting...
                                @else
                                    Submit Application
                                @endif
                            </button>
                            <button
                                type="button"
                                wire:click="closeDialog"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
