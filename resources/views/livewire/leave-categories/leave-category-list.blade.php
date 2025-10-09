<div class="space-y-6 p-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Leave Categories</h1>
    </div>

    <div class="flex gap-6 flex-col lg:flex-row">
        <!-- Create Leave Category Form -->
        <div class="bg-white p-6 rounded-md shadow-sm lg:w-1/3 w-full">
            <h2 class="text-lg font-medium mb-6">Create Leave Category</h2>

            <div class="space-y-4">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                    <input
                        type="text"
                        id="name"
                        wire:model="name"
                        placeholder="e.g., Annual Leave"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                    />
                    @error('name')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea
                        id="description"
                        wire:model="description"
                        placeholder="Optional description..."
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    ></textarea>
                </div>

                <div class="space-y-2">
                    <label for="maxDays" class="block text-sm font-medium text-gray-700">Maximum Days Per Year *</label>
                    <input
                        type="number"
                        id="maxDays"
                        wire:model="max_in_year"
                        min="1"
                        placeholder="e.g., 30"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('max_in_year') border-red-500 @enderror"
                    />
                    @error('max_in_year')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    wire:click="createCategory"
                    class="bg-green-500 hover:bg-green-600 text-white w-full mt-4 px-4 py-2 rounded-md font-medium"
                >
                    Create Category
                </button>
            </div>
        </div>

        <!-- Leave Categories Table -->
        <div class="bg-white p-6 rounded-md shadow-sm flex-1">
            <h2 class="text-lg font-medium mb-4">Existing Categories</h2>

            @if($isLoading)
                <div class="space-y-2">
                    <div class="h-10 w-full bg-gray-200 rounded animate-pulse"></div>
                    <div class="h-10 w-full bg-gray-200 rounded animate-pulse"></div>
                    <div class="h-10 w-full bg-gray-200 rounded animate-pulse"></div>
                </div>
            @elseif($categories->count() === 0)
                <div class="text-center py-12 text-gray-500">
                    <p class="text-lg">No leave categories found</p>
                    <p class="text-sm mt-2">Create your first category using the form</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Days/Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($categories as $index => $category)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $category->name }}</td>
                                    <td class="px-6 py-4 max-w-xs truncate">
                                        {{ $category->description ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $category->max_in_year }} days</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex gap-2">
                                            <button
                                                wire:click="openEditModal('{{ $category->id }}')"
                                                class="text-blue-500 hover:text-blue-700 p-1"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button
                                                wire:click="confirmDelete('{{ $category->id }}')"
                                                class="text-red-500 hover:text-red-700 p-1"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-sm text-gray-600">
                    Showing {{ $categories->count() }} {{ $categories->count() === 1 ? 'entry' : 'entries' }}
                </div>
            @endif
        </div>
    </div>


    <!-- Edit Dialog -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showEditModal') }" x-show="open" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Edit Leave Category</h3>
                                <p class="mt-1 text-sm text-gray-500">Update the leave category details below.</p>
                            </div>
                            <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4 py-4">
                            <div class="space-y-2">
                                <label for="editName" class="block text-sm font-medium text-gray-700">Name *</label>
                                <input
                                    type="text"
                                    id="editName"
                                    wire:model="name"
                                    placeholder="e.g., Annual Leave"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                />
                                @error('name')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="editDescription" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea
                                    id="editDescription"
                                    wire:model="description"
                                    placeholder="Optional description..."
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                            </div>

                            <div class="space-y-2">
                                <label for="editMaxDays" class="block text-sm font-medium text-gray-700">Maximum Days Per Year *</label>
                                <input
                                    type="number"
                                    id="editMaxDays"
                                    wire:model="max_in_year"
                                    min="1"
                                    placeholder="e.g., 30"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('max_in_year') border-red-500 @enderror"
                                />
                                @error('max_in_year')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end gap-2">
                        <button
                            @click="open = false"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Cancel
                        </button>
                        <button
                            wire:click="updateCategory"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Dialog -->
    @if($showDeleteModal && $selectedCategory)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showDeleteModal') }" x-show="open" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Are you sure?</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        This action cannot be undone. This will permanently delete the leave category "{{ $selectedCategory->name }}".
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end gap-2">
                        <button
                            @click="open = false"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Cancel
                        </button>
                        <button
                            wire:click="deleteCategory"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
