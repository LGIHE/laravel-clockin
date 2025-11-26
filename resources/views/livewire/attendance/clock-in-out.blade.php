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
                    @if(!$attendanceStatus['clocked_in'])
                        <div>
                            <label for="selectedProject" class="block text-sm font-medium text-gray-700 mb-1">
                                Project <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="selectedProject"
                                wire:model="selectedProject"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                @if($isLoading) disabled @endif
                            >
                                <option value="">Select a project...</option>
                                @foreach($userProjects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedProject') 
                                <span class="text-xs text-red-600 mt-1">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div>
                            <label for="selectedTask" class="block text-sm font-medium text-gray-700 mb-1">
                                Task (Optional)
                            </label>
                            <select 
                                id="selectedTask"
                                wire:model="selectedTask"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                @if($isLoading) disabled @endif
                            >
                                <option value="">Select a task...</option>
                                @foreach($userTasks as $task)
                                    <option value="{{ $task->id }}">{{ $task->title }}</option>
                                @endforeach
                                <option value="other">➕ Other - Create new task</option>
                            </select>
                            @error('selectedTask') 
                                <span class="text-xs text-red-600 mt-1">{{ $message }}</span> 
                            @enderror
                        </div>
                    @endif

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
                        @error('clockMessage') 
                            <span class="text-xs text-red-600 mt-1">{{ $message }}</span> 
                        @enderror
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

    <!-- Create Task Modal -->
    @if($showCreateTaskModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" wire:click="closeCreateTaskModal"></div>

            <!-- Modal panel -->
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modal-title">
                                Create New Task
                            </h3>
                            <div class="mt-2 space-y-4">
                                <!-- Task Title -->
                                <div>
                                    <label for="newTaskTitle" class="block text-sm font-medium text-gray-700 mb-1">
                                        Task Title <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="newTaskTitle"
                                        wire:model="newTaskTitle"
                                        placeholder="Enter task title"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('newTaskTitle') 
                                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Task Description -->
                                <div>
                                    <label for="newTaskDescription" class="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <textarea 
                                        id="newTaskDescription"
                                        wire:model="newTaskDescription"
                                        placeholder="Enter task description (optional)"
                                        rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    ></textarea>
                                    @error('newTaskDescription') 
                                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- Start Date -->
                                <div>
                                    <label for="newTaskStartDate" class="block text-sm font-medium text-gray-700 mb-1">
                                        Start Date <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="date" 
                                        id="newTaskStartDate"
                                        wire:model="newTaskStartDate"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('newTaskStartDate') 
                                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div>
                                    <label for="newTaskEndDate" class="block text-sm font-medium text-gray-700 mb-1">
                                        End Date
                                    </label>
                                    <input 
                                        type="date" 
                                        id="newTaskEndDate"
                                        wire:model="newTaskEndDate"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    @error('newTaskEndDate') 
                                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        type="button"
                        wire:click="createTask"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Create Task
                    </button>
                    <button 
                        type="button"
                        wire:click="closeCreateTaskModal"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            // Listen for changes on the task select dropdown
            document.getElementById('selectedTask')?.addEventListener('change', function(e) {
                if (e.target.value === 'other') {
                    // Reset the select to empty
                    e.target.value = '';
                    // Open the create task modal
                    @this.openCreateTaskModal();
                }
            });
        });
    </script>
</div>
