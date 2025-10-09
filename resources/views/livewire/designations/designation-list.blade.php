<div class="space-y-6">
    <!-- Tabs -->
    <div class="w-full">
        <div class="flex justify-between items-center mb-4">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Dashboard
                    </button>
                    <button class="border-[#1976d2] text-[#1976d2] whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Designation
                    </button>
                </nav>
            </div>
        </div>

        <!-- Dashboard Tab Content (hidden by default) -->
        <div class="hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    Designation dashboard content goes here.
                </div>
            </div>
        </div>

        <!-- Designation Tab Content -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Create New Designation Card -->
            @if($isAdmin)
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">Create New Designation</h2>
                        </div>
                        
                        <form wire:submit.prevent="createDesignation" class="flex gap-4 items-end">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Name
                                </label>
                                <input type="text"
                                       wire:model="name"
                                       placeholder="Designation name"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#1976d2] focus:border-transparent @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#1976d2] hover:bg-[#2196f3] text-white text-sm font-medium rounded-md transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Create
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Designations List Card -->
            <div class="bg-white rounded-lg shadow">
                <!-- Search Bar -->
                <div class="p-6">
                    <div class="flex gap-4 mb-4">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="Search designations..."
                               class="max-w-sm px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-[#1976d2] focus:border-transparent">
                    </div>
                </div>

                <!-- Table -->
                <div class="p-0">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Options
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($designations as $designation)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $designation->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                            @if($isAdmin)
                                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                                    <button @click="open = !open" 
                                                            type="button"
                                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#1976d2] text-white text-sm rounded-md hover:bg-[#2196f3] transition-colors">
                                                        <span>Action</span>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </button>

                                                    <div x-show="open" 
                                                         @click.away="open = false"
                                                         x-transition
                                                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                        <div class="py-1">
                                                            <button wire:click="openEditModal('{{ $designation->id }}')"
                                                                    @click="open = false"
                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                </svg>
                                                                Edit
                                                            </button>
                                                            <button wire:click="confirmDelete('{{ $designation->id }}')"
                                                                    @click="open = false"
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
                                    <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                                        {{ $search ? 'No designations found matching your search' : 'No designations found' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="px-6 py-4 bg-white border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Showing {{ $designations->count() }} {{ $designations->count() === 1 ? 'entry' : 'entries' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
@if($showEditModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showEditModal') }">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="updateDesignation">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Edit Designation</h3>
                        </div>

                        <div class="space-y-4 py-2">
                            <div class="space-y-2">
                                <label for="edit-name" class="block text-sm font-medium text-gray-700">
                                    Name
                                </label>
                                <input type="text" 
                                       id="edit-name"
                                       wire:model="name"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#1976d2] focus:border-transparent @error('name') border-red-500 @enderror"
                                       placeholder="Designation name">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                        <button type="button" 
                                @click="open = false"
                                class="px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 rounded-md transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-[#1976d2] hover:bg-[#2196f3] text-white rounded-md transition-colors">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Delete Confirmation Modal -->
@if($showDeleteModal && $selectedDesignation)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showDeleteModal') }">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-5 pb-4">
                    <div class="mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Designation</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete the designation "{{ $selectedDesignation->name }}"? 
                                This action cannot be undone and will fail if there are employees with this designation.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                    <button @click="open = false" 
                            class="px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 rounded-md transition-colors">
                        Cancel
                    </button>
                    <button wire:click="deleteDesignation" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

