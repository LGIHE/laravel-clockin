<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('roles.index') }}" class="text-blue-600 hover:text-blue-900">← Back to Roles</a>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">
                    {{ $roleId ? 'Edit Role' : 'Create New Role' }}
                </h2>

                <form wire:submit.prevent="save">
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Role Name</label>
                        <input wire:model="name" type="text" id="name" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Permissions</h3>
                        <p class="text-sm text-gray-600 mb-4">Select the permissions this role should have access to.</p>

                        <div class="space-y-4">
                            @foreach ($this->permissionsByCategory as $category => $permissions)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-md font-semibold text-gray-800 capitalize">{{ str_replace('-', ' ', $category) }}</h4>
                                        <button type="button" wire:click="toggleCategory('{{ $category }}')" class="text-sm text-blue-600 hover:text-blue-800">
                                            Toggle All
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach ($permissions as $permission)
                                            <label class="flex items-start space-x-3 cursor-pointer">
                                                <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->id }}" class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-700">{{ $permission->name }}</span>
                                                    @if ($permission->description)
                                                        <p class="text-xs text-gray-500">{{ $permission->description }}</p>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700">
                            {{ $roleId ? 'Update Role' : 'Create Role' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
