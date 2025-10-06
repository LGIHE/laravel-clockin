<div>
    <form wire:submit.prevent="submit">
        <div class="space-y-4">
            <!-- User Selection -->
            <div>
                <label for="userId" class="block text-sm font-medium text-gray-700 mb-1">
                    User <span class="text-red-500">*</span>
                </label>
                <select 
                    id="userId"
                    wire:model="userId"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('userId') border-red-500 @enderror"
                    @if($isLoading) disabled @endif
                >
                    <option value="">Select a user</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('userId')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Punch Type -->
            <div>
                <label for="punchType" class="block text-sm font-medium text-gray-700 mb-1">
                    Punch Type <span class="text-red-500">*</span>
                </label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input 
                            type="radio" 
                            wire:model="punchType" 
                            value="in"
                            class="form-radio text-blue-600"
                            @if($isLoading) disabled @endif
                        >
                        <span class="ml-2 text-sm text-gray-700">Clock In</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input 
                            type="radio" 
                            wire:model="punchType" 
                            value="out"
                            class="form-radio text-blue-600"
                            @if($isLoading) disabled @endif
                        >
                        <span class="ml-2 text-sm text-gray-700">Clock Out</span>
                    </label>
                </div>
                @error('punchType')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date and Time -->
            <div>
                <label for="punchTime" class="block text-sm font-medium text-gray-700 mb-1">
                    Date and Time <span class="text-red-500">*</span>
                </label>
                <input 
                    type="datetime-local" 
                    id="punchTime"
                    wire:model="punchTime"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('punchTime') border-red-500 @enderror"
                    @if($isLoading) disabled @endif
                >
                @error('punchTime')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                    Message (Optional)
                </label>
                <textarea 
                    id="message"
                    wire:model="message"
                    rows="3"
                    placeholder="Add a note about this force punch..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('message') border-red-500 @enderror"
                    @if($isLoading) disabled @endif
                ></textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Warning Message -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Warning
                        </h3>
                        <div class="mt-1 text-sm text-yellow-700">
                            <p>Force punch will override the user's attendance record. Use this feature carefully.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 mt-6">
            <x-ui.button 
                type="button"
                wire:click="$dispatch('close-force-punch-modal')" 
                variant="outline"
                :disabled="$isLoading"
            >
                Cancel
            </x-ui.button>
            <x-ui.button 
                type="submit"
                variant="primary"
                :disabled="$isLoading"
            >
                @if($isLoading)
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                @else
                    Submit Force Punch
                @endif
            </x-ui.button>
        </div>
    </form>
</div>
