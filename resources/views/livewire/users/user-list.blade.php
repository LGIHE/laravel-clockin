<div>
    <div class="space-y-6">
        <!-- Tabs -->
        <div class="flex justify-between items-center mb-4">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button 
                        wire:click="$set('activeTab', 'dashboard')"
                        class="@if($activeTab === 'dashboard') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        Dashboard
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'userList')"
                        class="@if($activeTab === 'userList') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        User List
                    </button>
                </nav>
            </div>

            <div class="flex gap-2">
                @if($isAdmin)
                    <button 
                        wire:click="openBulkAssignModal"
                        class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Assign Supervisor</span>
                    </button>
                    <a href="{{ route('users.create') }}">
                        <button class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Add New User</span>
                        </button>
                    </a>
                @endif
            </div>
        </div>

        <!-- Dashboard Tab Content -->
        @if($activeTab === 'dashboard')
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">User Dashboard</h3>
                <p class="text-gray-600">User statistics and reports will appear here.</p>
            </div>
        @endif

        <!-- User List Tab Content -->
        @if($activeTab === 'userList')
            <div class="space-y-4">

            <div class="space-y-4">
                <!-- Show entries and search controls -->
                <div class="flex items-center gap-2">
                    <div class="flex items-center">
                        <label for="entries" class="mr-2 text-sm">Show</label>
                        <select 
                            id="entries" 
                            wire:model.live="perPage"
                            class="border border-gray-300 rounded px-2 py-1 text-sm"
                        >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ml-2 text-sm">entries</span>
                    </div>
                    
                    <div class="ml-auto relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input 
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search by name, email..."
                            class="pl-10 w-64 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>
                </div>
                <!-- Users Table -->
                <div class="border rounded-md overflow-hidden bg-white">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Department
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Supervisor
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Options
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                    @if($user->designation)
                                                        <div class="text-xs text-white bg-purple-500 px-2 py-0.5 rounded-sm inline-block mt-1">
                                                            {{ $user->designation->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($user->department)
                                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $user->department->name }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="text-gray-400">—</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded {{ $user->status === 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $user->status === 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 relative">
                                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                                <button @click="open = !open" type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    Action
                                                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>

                                                <div x-show="open" @click.away="open = false" class="z-40 origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                    <div class="py-1" role="menu">
                                                        <a href="{{ route('users.edit', $user->id) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                            Edit User
                                                        </a>
                                                        
                                                        <div class="border-t border-gray-100"></div>
                                                        
                                                        <button wire:click="changeDepartment('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                            </svg>
                                                            Change Department
                                                        </button>
                                                        
                                                        <button wire:click="changeSupervisor('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                            </svg>
                                                            Change Supervisor
                                                        </button>
                                                        
                                                        <button wire:click="ipRestriction('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                            </svg>
                                                            IP Restriction
                                                        </button>
                                                        
                                                        <button wire:click="updatePassword('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                            </svg>
                                                            Update Password
                                                        </button>
                                                        
                                                        <button wire:click="updateDesignation('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                            </svg>
                                                            Update Designation
                                                        </button>
                                                        
                                                        <div class="border-t border-gray-100"></div>
                                                        
                                                        <button wire:click="toggleStatus('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <span class="mr-3 text-{{ $user->status === 1 ? 'red' : 'green' }}-500">⬤</span>
                                                            {{ $user->status === 1 ? 'Deactivate User' : 'Activate User' }}
                                                        </button>
                                                        
                                                        <button wire:click="lastInTime('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            Last In Time
                                                        </button>
                                                        
                                                        <button wire:click="autoPunchOut('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            Auto Punch Out Time
                                                        </button>
                                                        
                                                        <button wire:click="forcePunch('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            Force Punch In/Out
                                                        </button>
                                                        
                                                        <button wire:click="forceLogin('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                            </svg>
                                                            Force Login
                                                        </button>
                                                        
                                                        <div class="border-t border-gray-100"></div>
                                                        
                                                        <button wire:click="confirmDelete('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            {{ $search ? 'No users found matching your search' : 'No users found' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    
                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-white border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                @if($users->total() > 0)
                                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                                @else
                                    No entries to display
                                @endif
                            </div>
                            @if($users->hasPages())
                                <div class="flex justify-center gap-1">
                                    @if($users->onFirstPage())
                                        <button disabled class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-400 cursor-not-allowed">
                                            Previous
                                        </button>
                                    @else
                                        <button wire:click="previousPage" class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">
                                            Previous
                                        </button>
                                    @endif
                                    
                                    @php
                                        $currentPage = $users->currentPage();
                                        $lastPage = $users->lastPage();
                                        $start = max(1, min($currentPage - 2, $lastPage - 4));
                                        $end = min($lastPage, max($currentPage + 2, 5));
                                    @endphp
                                    
                                    @for($i = $start; $i <= $end; $i++)
                                        <button 
                                            wire:click="gotoPage({{ $i }})" 
                                            class="px-3 py-1 border rounded text-sm {{ $i == $currentPage ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                                        >
                                            {{ $i }}
                                        </button>
                                    @endfor
                                    
                                    @if($users->hasMorePages())
                                        <button wire:click="nextPage" class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">
                                            Next
                                        </button>
                                    @else
                                        <button disabled class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-400 cursor-not-allowed">
                                            Next
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showDetailModal') }" x-show="show" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900">User Details</h3>
                            <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $selectedUser->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $selectedUser->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Role</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($selectedUser->userLevel->name) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Status</label>
                                    <p class="mt-1">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($selectedUser->status === 1) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $selectedUser->status === 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Department</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $selectedUser->department->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Designation</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $selectedUser->designation->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Project</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($selectedUser->project_id)
                                            @php
                                                $projectIds = json_decode($selectedUser->project_id, true);
                                                $projects = \App\Models\Project::whereIn('id', $projectIds ?? [])->pluck('name');
                                            @endphp
                                            {{ $projects->implode(', ') ?: 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Member Since</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $selectedUser->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end">
                        <button @click="show = false" 
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal && $selectedUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showDeleteModal') }" x-show="show" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete User</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete <strong>{{ $selectedUser->name }}</strong>? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                        <button @click="show = false" 
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button wire:click="deleteUser" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Supervisor Assignment Modal -->
    @if($showBulkAssignModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showBulkAssignModal') }" x-show="show" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Bulk Supervisor Assignment</h3>
                                <p class="text-sm text-gray-500 mt-1">Assign a supervisor to multiple users at once</p>
                            </div>
                            <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Supervisor Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Supervisor</label>
                                <select 
                                    wire:model="selectedSupervisor"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Select a supervisor</option>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Department Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Department</label>
                                <select 
                                    wire:model.live="bulkDepartmentFilter"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Users Table with Checkboxes -->
                            <div class="border rounded-md overflow-hidden mt-4">
                                <table class="min-w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="py-2 px-4 text-left">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model="selectAll"
                                                    wire:click="toggleSelectAll"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                            </th>
                                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500">Name</th>
                                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500">Department</th>
                                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500">Current Supervisor</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @if(count($filteredUsersForBulk) === 0)
                                            <tr>
                                                <td colspan="4" class="py-4 text-center text-sm text-gray-500">
                                                    No users found in the selected department
                                                </td>
                                            </tr>
                                        @else
                                            @foreach($filteredUsersForBulk as $user)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="py-2 px-4">
                                                        <input 
                                                            type="checkbox" 
                                                            wire:model="selectedUserIds"
                                                            value="{{ $user->id }}"
                                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                        />
                                                    </td>
                                                    <td class="py-2 px-4">{{ $user->name }}</td>
                                                    <td class="py-2 px-4">{{ $user->department->name ?? '-' }}</td>
                                                    <td class="py-2 px-4">-</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2">
                        <button @click="show = false" 
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button wire:click="assignSupervisorToUsers" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            Assign Supervisor
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
