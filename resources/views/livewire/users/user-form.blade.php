<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $isEditMode ? 'Edit User' : 'Create New User' }}</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $isEditMode ? 'Update user information and permissions' : 'Add a new employee to the system' }}</p>
                </div>
                <a href="{{ route('users.index') }}">
                    <x-ui.button variant="outline" size="sm">
                        Back to Users
                    </x-ui.button>
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name"
                           wire:model="name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                           placeholder="Enter full name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email"
                           wire:model="email"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="email@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <select id="gender" 
                            wire:model="gender"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gender') border-red-500 @enderror">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password @if(!$isEditMode)<span class="text-red-500">*</span>@endif
                    </label>
                    <div class="relative">
                        <input type="{{ $showPassword ? 'text' : 'password' }}" 
                               id="password"
                               wire:model="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                               placeholder="{{ $isEditMode ? 'Leave blank to keep current' : 'Minimum 6 characters' }}">
                        <button type="button"
                                wire:click="$toggle('showPassword')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            @if($showPassword)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm Password @if(!$isEditMode)<span class="text-red-500">*</span>@endif
                    </label>
                    <input type="{{ $showPassword ? 'text' : 'password' }}" 
                           id="password_confirmation"
                           wire:model="password_confirmation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password_confirmation') border-red-500 @enderror"
                           placeholder="Re-enter password">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Role and Organization -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Role & Organization</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User Role -->
                <div>
                    <label for="userLevelId" class="block text-sm font-medium text-gray-700 mb-1">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select id="userLevelId" 
                            wire:model="userLevelId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('userLevelId') border-red-500 @enderror">
                        <option value="">Select Role</option>
                        @foreach($userLevels as $level)
                            <option value="{{ $level->id }}">{{ ucfirst($level->name) }}</option>
                        @endforeach
                    </select>
                    @error('userLevelId')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            wire:model="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div>
                    <label for="departmentId" class="block text-sm font-medium text-gray-700 mb-1">
                        Department
                    </label>
                    <select id="departmentId" 
                            wire:model="departmentId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('departmentId') border-red-500 @enderror">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('departmentId')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Designation -->
                <div>
                    <label for="designationId" class="block text-sm font-medium text-gray-700 mb-1">
                        Designation
                    </label>
                    <select id="designationId" 
                            wire:model="designationId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('designationId') border-red-500 @enderror">
                        <option value="">Select Designation</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                        @endforeach
                    </select>
                    @error('designationId')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Primary Supervisor -->
                <div>
                    <label for="primarySupervisorId" class="block text-sm font-medium text-gray-700 mb-1">
                        Primary Supervisor
                    </label>
                    <select id="primarySupervisorId" 
                            wire:model="primarySupervisorId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('primarySupervisorId') border-red-500 @enderror">
                        <option value="">Select Primary Supervisor</option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">
                                {{ $supervisor->name }} 
                                @if($supervisor->userLevel)
                                    ({{ ucfirst($supervisor->userLevel->name) }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Primary supervisor receives leave request notifications</p>
                    @error('primarySupervisorId')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Secondary Supervisor -->
                <div>
                    <label for="secondarySupervisorId" class="block text-sm font-medium text-gray-700 mb-1">
                        Secondary Supervisor
                    </label>
                    <select id="secondarySupervisorId" 
                            wire:model="secondarySupervisorId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('secondarySupervisorId') border-red-500 @enderror">
                        <option value="">Select Secondary Supervisor</option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}" {{ $supervisor->id == $primarySupervisorId ? 'disabled' : '' }}>
                                {{ $supervisor->name }} 
                                @if($supervisor->userLevel)
                                    ({{ ucfirst($supervisor->userLevel->name) }})
                                @endif
                                @if($supervisor->id == $primarySupervisorId)
                                    (Already selected as primary)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Backup supervisor (optional)</p>
                    @error('secondarySupervisorId')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Project Assignment -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Project Assignment</h3>
            
            @if(count($projects) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($projects as $project)
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors
                            {{ in_array($project->id, $selectedProjects) ? 'bg-blue-50 border-blue-500' : '' }}">
                            <input type="checkbox" 
                                   wire:click="toggleProjectSelection('{{ $project->id }}')"
                                   {{ in_array($project->id, $selectedProjects) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-3 text-sm text-gray-900">{{ $project->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('selectedProjects')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            @else
                <p class="text-sm text-gray-500">No active projects available</p>
            @endif
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <button type="button" 
                    wire:click="cancel"
                    class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors">
                Cancel
            </button>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                {{ $isEditMode ? 'Update User' : 'Create User' }}
            </button>
        </div>
    </form>
    </div>
</div>
