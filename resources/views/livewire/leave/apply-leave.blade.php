<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Apply for Leave</h3>
    
    <!-- Leave Balance Summary -->
    @if(count($leaveBalances) > 0)
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($leaveBalances as $categoryId => $balance)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $balance['category'] }}</h4>
                    <div class="flex items-baseline space-x-2">
                        <span class="text-2xl font-bold text-gray-900">{{ $balance['remaining'] }}</span>
                        <span class="text-sm text-gray-500">/ {{ $balance['total'] }} days</span>
                    </div>
                    <div class="mt-2">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Used: {{ $balance['used'] }}</span>
                            <span>{{ round(($balance['used'] / $balance['total']) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(($balance['used'] / $balance['total']) * 100, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Leave Application Form -->
    <form wire:submit.prevent="submit" class="space-y-4">
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

        <!-- Date -->
        <div>
            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                Leave Date <span class="text-red-500">*</span>
            </label>
            <input 
                type="date" 
                id="date"
                wire:model="date"
                min="{{ now()->addDay()->format('Y-m-d') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('date') border-red-500 @enderror"
                required
            >
            @error('date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

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

        <!-- Submit Button -->
        <div class="flex justify-end">
            <x-ui.button 
                type="submit" 
                variant="primary"
                :disabled="$isLoading"
            >
                @if($isLoading)
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Submitting...
                @else
                    Submit Leave Application
                @endif
            </x-ui.button>
        </div>
    </form>
</div>
