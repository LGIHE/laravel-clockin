<div class="space-y-6">
    <!-- Main Content Card -->
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Notices</h2>
                    @if($isAdmin)
                        <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Notice
                        </button>
                    @endif
                </div>

                @if($notices->count() === 0)
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-lg font-medium">No notices found</p>
                        <p class="text-sm">There are no notices added yet.</p>
                        @if($isAdmin)
                            <button wire:click="openCreateModal" class="mt-4 px-4 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Notice
                            </button>
                        @endif
                    </div>
                @else
                    <!-- Notices List -->
                    <div class="space-y-4">
                        @foreach($notices as $notice)
                            <div class="bg-white border rounded-lg shadow-sm {{ !$notice->is_active ? 'opacity-60' : '' }}">
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h3 class="text-lg font-semibold">{{ $notice->subject }}</h3>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $notice->is_active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $notice->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                            <p class="text-gray-600 whitespace-pre-wrap mb-3">{{ $notice->message }}</p>
                                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                                <span>Created: {{ $notice->created_at->format('M d, Y h:i a') }}</span>
                                                @if($notice->updated_at->ne($notice->created_at))
                                                    <span>Updated: {{ $notice->updated_at->format('M d, Y h:i a') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($isAdmin)
                                            <div class="flex items-center gap-2 ml-4">
                                                <button wire:click="toggleActive('{{ $notice->id }}')" 
                                                        class="p-2 hover:bg-gray-100 rounded transition-colors"
                                                        title="{{ $notice->is_active ? 'Deactivate' : 'Activate' }}">
                                                    @if($notice->is_active)
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                        </svg>
                                                    @endif
                                                </button>
                                                <button wire:click="openEditModal('{{ $notice->id }}')" 
                                                        class="p-2 hover:bg-gray-100 rounded transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <button wire:click="confirmDelete('{{ $notice->id }}')" 
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create/Edit Notice Dialog -->
    @if($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle(($showCreateModal ? 'showCreateModal' : 'showEditModal')) }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="{{ $showCreateModal ? 'createNotice' : 'updateNotice' }}">
                        <div class="bg-white px-6 pt-5 pb-4">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $showCreateModal ? 'Create Notice' : 'Edit Notice' }}</h3>
                                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                                        Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="subject"
                                           wire:model="subject"
                                           placeholder="Enter notice title"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('subject') border-red-500 @enderror">
                                    @error('subject')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                                        Content <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="message"
                                              wire:model="message"
                                              rows="8"
                                              placeholder="Enter notice content"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('message') border-red-500 @enderror"></textarea>
                                    @error('message')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" 
                                           id="is_active"
                                           wire:model="is_active"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                                        Active (visible to all users)
                                    </label>
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
                                {{ $showCreateModal ? 'Create Notice' : 'Update Notice' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Dialog -->
    @if($showDeleteModal && $selectedNotice)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: @entangle('showDeleteModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Notice</h3>
                        <p class="text-sm text-gray-600">
                            Are you sure you want to delete this notice? This action cannot be undone.
                        </p>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                        <button @click="open = false" 
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button wire:click="deleteNotice" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
