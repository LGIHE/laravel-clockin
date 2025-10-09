<div class="space-y-6">
    <!-- Main Content Card -->
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <!-- Header with Year Selector -->
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-4">
                        <h2 class="text-xl font-semibold">Holidays</h2>
                        <select wire:model.live="selectedYear" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach($yearOptions as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($isAdmin)
                        <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Holiday
                        </button>
                    @endif
                </div>

                @if($holidays->count() === 0)
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-lg font-medium">No holidays found</p>
                        <p class="text-sm">There are no holidays added for {{ $selectedYear }}.</p>
                        @if($isAdmin)
                            <button wire:click="openCreateModal" class="mt-4 px-4 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Holiday
                            </button>
                        @endif
                    </div>
                @else
                    <div class="space-y-6">
                        <!-- Calendar -->
                        <div class="flex justify-center">
                            <div class="border rounded-md p-3 w-full max-w-md">
                                <div class="space-y-2">
                                    @foreach($calendarMonths as $month => $weeks)
                                        <div class="mb-4">
                                            <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ \Carbon\Carbon::create($selectedYear, $month, 1)->format('F Y') }}</h3>
                                            <div class="grid grid-cols-7 gap-1">
                                                @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $day)
                                                    <div class="text-center text-xs font-semibold text-gray-600 py-1">{{ $day }}</div>
                                                @endforeach
                                                @foreach($weeks as $week)
                                                    @foreach($week as $day)
                                                        @if($day)
                                                            @php
                                                                $date = \Carbon\Carbon::create($selectedYear, $month, $day);
                                                                $isHoliday = $holidays->contains(function($h) use ($date) {
                                                                    return $h->date->isSameDay($date);
                                                                });
                                                                $isToday = $date->isToday();
                                                                $isSelected = $selectedDate && $selectedDate->isSameDay($date);
                                                            @endphp
                                                            <button 
                                                                wire:click="selectDate('{{ $date->format('Y-m-d') }}')"
                                                                class="relative text-sm py-1.5 rounded-md transition-colors
                                                                    {{ $isSelected ? 'bg-blue-600 text-white' : ($isHoliday ? 'text-red-500 font-medium' : 'text-gray-900') }}
                                                                    {{ !$isSelected && !$isHoliday ? 'hover:bg-gray-100' : '' }}">
                                                                {{ $day }}
                                                                @if($isHoliday && !$isSelected)
                                                                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-red-500 rounded-sm"></div>
                                                                @endif
                                                            </button>
                                                        @else
                                                            <div></div>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Selected Date Holiday Details -->
                        @if($selectedDate && $selectedHoliday)
                            <div class="p-4 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-lg text-red-600">{{ $selectedHoliday->name ?? 'Holiday' }}</h3>
                                        <p class="text-gray-600 mt-1">Date: {{ $selectedDate->format('l, F j, Y') }}</p>
                                        @if($selectedHoliday->description)
                                            <p class="text-sm text-gray-500 mt-2">{{ $selectedHoliday->description }}</p>
                                        @endif
                                    </div>
                                    @if($isAdmin)
                                        <button wire:click="confirmDelete('{{ $selectedHoliday->id }}')" class="p-1 text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Upcoming/All Holidays List -->
                        <div class="border-t pt-6">
                            <h3 class="font-medium text-lg mb-4">
                                @if($selectedYear === now()->year)
                                    Upcoming Holidays
                                @else
                                    Holidays in {{ $selectedYear }}
                                @endif
                            </h3>
                            <div class="space-y-3">
                                @php
                                    $displayHolidays = $holidays;
                                    if ($selectedYear === now()->year) {
                                        $displayHolidays = $holidays->filter(function($h) {
                                            return $h->date >= now();
                                        })->take(5);
                                    } else {
                                        $displayHolidays = $holidays->sortBy('date')->take(20);
                                    }
                                @endphp
                                
                                @foreach($displayHolidays as $holiday)
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors">
                                        <div class="flex-1">
                                            <p class="font-medium">{{ $holiday->name ?? 'Holiday' }}</p>
                                            <p class="text-sm text-gray-600">{{ $holiday->date->format('F j, Y') }}</p>
                                            @if($holiday->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ $holiday->description }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm bg-gray-200 px-2 py-1 rounded">
                                                {{ $holiday->date->format('l') }}
                                            </div>
                                            @if($isAdmin)
                                                <button wire:click="confirmDelete('{{ $holiday->id }}')" class="p-1 text-red-500 hover:text-red-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Holiday Dialog -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showCreateModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="createHoliday">
                        <div class="bg-white px-6 pt-5 pb-4">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Create Holiday</h3>
                                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="create-name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Holiday Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="create-name"
                                           wire:model="name"
                                           placeholder="Enter holiday name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="create-description" class="block text-sm font-medium text-gray-700 mb-1">
                                        Description (Optional)
                                    </label>
                                    <textarea id="create-description"
                                              wire:model="description"
                                              rows="3"
                                              placeholder="Enter holiday description"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"></textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" 
                                           wire:model="date"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date') border-red-500 @enderror">
                                    @error('date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                            <button type="button" 
                                    @click="open = false"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                Create Holiday
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Dialog -->
    @if($showDeleteModal && $selectedHoliday)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showDeleteModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Holiday</h3>
                        <p class="text-sm text-gray-600">
                            Are you sure you want to delete this holiday? This action cannot be undone.
                        </p>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                        <button @click="open = false" 
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button wire:click="deleteHoliday" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
