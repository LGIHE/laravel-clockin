<div class="font-sans">
    <!-- Page Header -->
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-semibold"></h1>
        <x-ui.button wire:click="openCreateModal" variant="primary" size="sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Task
        </x-ui.button>
    </div>

    <!-- Tasks Card -->
    <div class="bg-white rounded-lg shadow">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">My Tasks</h2>
        </div>

        <!-- Card Content -->
        <div class="p-6">
            @if($tasks->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    No tasks created yet. Click "Create Task" to add your first task.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($tasks as $task)
                        <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-lg">{{ $task->title }}</h3>
                                        <!-- Status Badge -->
                                        @php
                                            $statusColors = [
                                                'in-progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'on-hold' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'completed' => 'bg-green-100 text-green-800 border-green-200',
                                            ];
                                            $statusColor = $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                            $statusLabel = ucfirst(str_replace('-', ' ', $task->status));
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded border {{ $statusColor }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    @if($task->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ $task->description }}</p>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <button 
                                        wire:click="openEditModal('{{ $task->id }}')"
                                        class="p-2 hover:bg-gray-100 rounded transition-colors"
                                        title="Edit Task"
                                    >
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="confirmDelete('{{ $task->id }}')"
                                        class="p-2 hover:bg-gray-100 rounded transition-colors"
                                        title="Delete Task"
                                    >
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600 mt-3">
                                <div class="flex items-center gap-1">
                                    <span class="font-medium">Project:</span>
                                    <span>{{ $task->project ? $task->project->name : 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>
                                        {{ $task->start_date->format('M d, Y') }}
                                        @if($task->end_date)
                                            - {{ $task->end_date->format('M d, Y') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Create Task Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeCreateModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Create New Task</h3>
                        </div>

                        <form wire:submit.prevent="createTask" class="space-y-4">
                            <!-- Task Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Task Title</label>
                                <input 
                                    type="text" 
                                    id="title"
                                    wire:model="title"
                                    placeholder="Enter task title"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea 
                                    id="description"
                                    wire:model="description"
                                    placeholder="Enter task description"
                                    rows="3"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none"
                                ></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Project -->
                            <div>
                                <label for="project_id" class="block text-sm font-medium text-gray-700">Project</label>
                                <select 
                                    id="project_id"
                                    wire:model="project_id"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                                    <option value="">Select a project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                                @error('project_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Date Range -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input 
                                        type="date" 
                                        id="start_date"
                                        wire:model="start_date"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    >
                                    @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date (Optional)</label>
                                    <input 
                                        type="date" 
                                        id="end_date"
                                        wire:model="end_date"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    >
                                    @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Modal Actions -->
                            <div class="flex justify-end gap-2 pt-4">
                                <x-ui.button type="button" wire:click="closeCreateModal" variant="outline" size="sm">
                                    Cancel
                                </x-ui.button>
                                <x-ui.button type="submit" variant="primary" size="sm">
                                    Create Task
                                </x-ui.button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Task Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeEditModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Edit Task</h3>
                        </div>

                        <form wire:submit.prevent="updateTask" class="space-y-4">
                            <!-- Task Title -->
                            <div>
                                <label for="edit_title" class="block text-sm font-medium text-gray-700">Task Title</label>
                                <input 
                                    type="text" 
                                    id="edit_title"
                                    wire:model="title"
                                    placeholder="Enter task title"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="edit_description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea 
                                    id="edit_description"
                                    wire:model="description"
                                    placeholder="Enter task description"
                                    rows="3"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none"
                                ></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Project -->
                            <div>
                                <label for="edit_project_id" class="block text-sm font-medium text-gray-700">Project</label>
                                <select 
                                    id="edit_project_id"
                                    wire:model="project_id"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                >
                                    <option value="">Select a project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                                @error('project_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Date Range -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input 
                                        type="date" 
                                        id="edit_start_date"
                                        wire:model="start_date"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    >
                                    @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_end_date" class="block text-sm font-medium text-gray-700">End Date (Optional)</label>
                                    <input 
                                        type="date" 
                                        id="edit_end_date"
                                        wire:model="end_date"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    >
                                    @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Modal Actions -->
                            <div class="flex justify-end gap-2 pt-4">
                                <x-ui.button type="button" wire:click="closeEditModal" variant="outline" size="sm">
                                    Cancel
                                </x-ui.button>
                                <x-ui.button type="submit" variant="primary" size="sm">
                                    Update Task
                                </x-ui.button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeDeleteModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Task</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete this task? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <x-ui.button type="button" wire:click="deleteTask" variant="danger" size="sm" class="ml-3">
                            Delete
                        </x-ui.button>
                        <x-ui.button type="button" wire:click="closeDeleteModal" variant="outline" size="sm">
                            Cancel
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
