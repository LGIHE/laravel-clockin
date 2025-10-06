<div>
    <x-ui.card>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Attendance Status</h2>
                @if($attendanceStatus['clocked_in'])
                    <div class="flex items-center space-x-2">
                        <x-ui.badge variant="success">Clocked In</x-ui.badge>
                        <span class="text-sm text-gray-600">
                            Since {{ \Carbon\Carbon::parse($attendanceStatus['in_time'])->format('h:i A') }}
                        </span>
                    </div>
                    @if($attendanceStatus['attendance'] && $attendanceStatus['attendance']->in_message)
                        <p class="text-sm text-gray-500 mt-1">{{ $attendanceStatus['attendance']->in_message }}</p>
                    @endif
                    <div class="mt-2">
                        <span class="text-xs text-gray-500">Duration: </span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ sprintf('%02d:%02d', floor($attendanceStatus['duration'] / 3600), floor(($attendanceStatus['duration'] % 3600) / 60)) }}
                        </span>
                    </div>
                @else
                    <x-ui.badge variant="warning">Not Clocked In</x-ui.badge>
                @endif
            </div>
            
            <div class="flex-1 md:max-w-md md:ml-8">
                <div class="space-y-3">
                    <div>
                        <label for="clockMessage" class="block text-sm font-medium text-gray-700 mb-1">
                            Message (Optional)
                        </label>
                        <input 
                            type="text" 
                            id="clockMessage"
                            wire:model="clockMessage"
                            placeholder="Add a note..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            @if($isLoading) disabled @endif
                        >
                    </div>
                    
                    <div class="flex space-x-3">
                        @if($attendanceStatus['clocked_in'])
                            <x-ui.button 
                                wire:click="clockOut" 
                                variant="danger" 
                                class="flex-1"
                                :disabled="$isLoading"
                            >
                                @if($isLoading)
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                @endif
                                Clock Out
                            </x-ui.button>
                        @else
                            <x-ui.button 
                                wire:click="clockIn" 
                                variant="success" 
                                class="flex-1"
                                :disabled="$isLoading"
                            >
                                @if($isLoading)
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                @endif
                                Clock In
                            </x-ui.button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>
</div>
