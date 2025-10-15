<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Department Management</h1>
        @if($isAdmin)
            <button wire:click="openCreateModal" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#1976d2] hover:bg-[#2196f3] text-white text-sm font-medium rounded-md transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Department</span>
            </button>
        @endif
    </div>

    <!-- Departments Card -->
    <div class="bg-white rounded-lg shadow">
        <!-- Card Header -->
        <div class="px-6 pt-6 pb-3">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Departments</h2>
            <div class="flex items-center gap-4">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search departments..."
                       class="w-64 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-[#1976d2] focus:border-transparent">
            </div>
        </div>

        <!-- Card Content -->
        <div class="p-6 pt-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Options
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($departments as $department)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $department->name }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-500">{{ $department->description ?: '-' }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right">
                                        @if($isAdmin)
                                            <div class="relative inline-block text-left" x-data="{ open: false }" @click.away="open = false">
                                                <button @click="open = !open" 
                                                        type="button"
                                                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#1976d2] text-white text-sm rounded-md hover:bg-[#2196f3] transition-colors">
                                                    <span>Action</span>
                                                    <svg class="w-4 h-4" :class="{ 'rotate-180': open }" 
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                         style="transition: transform 0.2s;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>

                                                <div x-show="open" 
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="transform opacity-0 scale-95"
                                                     x-transition:enter-end="transform opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-75"
                                                     x-transition:leave-start="transform opacity-100 scale-100"
                                                     x-transition:leave-end="transform opacity-0 scale-95"
                                                     style="display: none;"
                                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                    <div class="py-1">
                                                        <button type="button"
                                                                wire:click="openEditModal('{{ $department->id }}')"
                                                                x-on:click="open = false"
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                            Edit
                                                        </button>
                            <button type="button"
                                wire:click="confirmDelete('{{ $department->id }}')"
                                x-on:click="open = false"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            <span class="text-red-500">Delete</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                    {{ $search ? 'No departments found matching your search' : 'No departments found' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
@if($showCreateModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeCreateModal"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createDepartment">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Department</h3>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="create-name" class="block text-sm font-medium text-gray-700">
                                    Department Name
                                </label>
                                <input type="text" 
                                       id="create-name"
                                       wire:model="name"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#1976d2] focus:border-transparent @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="create-description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <input type="text"
                                       id="create-description"
                                       wire:model="description"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#1976d2] focus:border-transparent @error('description') border-red-500 @enderror">
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                        <button type="button" 
                                wire:click="closeCreateModal"
                                class="px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 rounded-md transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-[#1976d2] hover:bg-[#2196f3] text-white rounded-md transition-colors">
                            Save Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Edit Modal -->
@if($showEditModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeEditModal"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="updateDepartment">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Department</h3>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="edit-name" class="block text-sm font-medium text-gray-700">
                                    Department Name
                                </label>
                                <input type="text" 
                                       id="edit-name"
                                       wire:model="name"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#1976d2] focus:border-transparent @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="edit-description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <input type="text"
                                       id="edit-description"
                                       wire:model="description"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#1976d2] focus:border-transparent @error('description') border-red-500 @enderror">
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                        <button type="button" 
                                wire:click="closeEditModal"
                                class="px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 rounded-md transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-[#1976d2] hover:bg-[#2196f3] text-white rounded-md transition-colors">
                            Save Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Delete Confirmation Modal -->
@if($showDeleteModal && $selectedDepartment)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeDeleteModal"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-5 pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Department</h3>
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete the department "{{ $selectedDepartment->name }}"? 
                        This action cannot be undone and will fail if there are employees assigned to this department.
                    </p>
                </div>

                <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                    <button wire:click="closeDeleteModal" 
                            class="px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 rounded-md transition-colors">
                        Cancel
                    </button>
                    <button wire:click="deleteDepartment" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

