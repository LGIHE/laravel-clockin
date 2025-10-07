<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Leave Category Management</h1>
                    <p class="text-sm text-gray-600 mt-1">Manage leave types and annual limits</p>
                </div>
                <div class="flex items-center space-x-4">
                    @if($isAdmin)
                        <x-ui.button wire:click="openCreateModal" variant="primary" size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Leave Category
                        </x-ui.button>
                    @endif
                    <a href="{{ route('admin.dashboard') }}">
                        <x-ui.button variant="outline" size="sm">
                            Back to Dashboard
                        </x-ui.button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- Search Bar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" 
                           id="search"
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Search by category name..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Leave Categories Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th wire:click="sortByColumn('name')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center space-x-1">
                                    <span>Category Name</span>
                                    @if($sortBy === 'name')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortOrder === 'asc')
                                                <path d="M5 10l5-5 5 5H5z"/>
                                            @else
                                                <path d="M15 10l-5 5-5-5h10z"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortByColumn('max_in_year')" 
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center justify-center space-x-1">
                                    <span>Max Days/Year</span>
                                    @if($sortBy === 'max_in_year')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortOrder === 'asc')
                                                <path d="M5 10l5-5 5 5H5z"/>
                                            @else
                                                <path d="M15 10l-5 5-5-5h10z"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Active Leaves
                            </th>
                            <th wire:click="sortByColumn('created_at')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center space-x-1">
                                    <span>Created</span>
                                    @if($sortBy === 'created_at')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortOrder === 'asc')
                                                <path d="M5 10l5-5 5 5H5z"/>
                                            @else
                                                <path d="M15 10l-5 5-5-5h10z"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            @if($isAdmin)
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $category->max_in_year }} {{ Str::plural('day', $category->max_in_year) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $category->leaves_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $category->created_at ? $category->created_at->format('M d, Y') : 'N/A' }}
                                </td>
                                @if($isAdmin)
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button wire:click="openEditModal('{{ $category->id }}')"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                    title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="confirmDelete('{{ $category->id }}')"
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 5 : 4 }}" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-2">No leave categories found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showCreateModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="createCategory">
                        <div class="bg-white px-6 pt-5 pb-4">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Create Leave Category</h3>
                                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="create-name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Category Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="create-name"
                                           wire:model="name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                           placeholder="e.g., Sick Leave, Annual Leave">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="create-max-days" class="block text-sm font-medium text-gray-700 mb-1">
                                        Maximum Days Per Year <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="create-max-days"
                                           wire:model="max_in_year"
                                           min="1"
                                           max="365"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_in_year') border-red-500 @enderror"
                                           placeholder="Enter maximum days allowed">
                                    @error('max_in_year')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Specify the maximum number of days an employee can take per year for this category</p>
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
                                Create Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showEditModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="updateCategory">
                        <div class="bg-white px-6 pt-5 pb-4">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Edit Leave Category</h3>
                                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Category Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="edit-name"
                                           wire:model="name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                           placeholder="e.g., Sick Leave, Annual Leave">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="edit-max-days" class="block text-sm font-medium text-gray-700 mb-1">
                                        Maximum Days Per Year <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="edit-max-days"
                                           wire:model="max_in_year"
                                           min="1"
                                           max="365"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_in_year') border-red-500 @enderror"
                                           placeholder="Enter maximum days allowed">
                                    @error('max_in_year')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Specify the maximum number of days an employee can take per year for this category</p>
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
                                Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal && $selectedCategory)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showDeleteModal') }">
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
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Leave Category</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete <strong>{{ $selectedCategory->name }}</strong>?
                                    </p>
                                    @if($selectedCategory->leaves_count > 0)
                                        <p class="mt-2 text-sm text-red-600 font-medium">
                                            This category has {{ $selectedCategory->leaves_count }} active leave(s) and cannot be deleted.
                                        </p>
                                    @else
                                        <p class="mt-2 text-sm text-gray-500">
                                            This action cannot be undone.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                        <button @click="open = false" 
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                            Cancel
                        </button>
                        @if($selectedCategory->leaves_count === 0)
                            <button wire:click="deleteCategory" 
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
