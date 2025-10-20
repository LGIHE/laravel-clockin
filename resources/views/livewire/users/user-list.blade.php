<div>
    <div class="space-y-6">
        <!-- Tabs -->
        <div class="flex justify-between items-center mb-4">
            <div class="border-b border-gray-200">
                <!-- <nav class="-mb-px flex space-x-8">
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
                </nav> -->
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
                    <button 
                        wire:click="openAddUserModal"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <svg class="w-3 h-3 -ml-3 -mt-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Add New User</span>
                    </button>
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

                    <div class="flex items-center ml-4">
                        <label for="statusFilter" class="mr-2 text-sm">Status</label>
                        <select 
                            id="statusFilter" 
                            wire:model.live="userStatusFilter"
                            class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="active">Active</option>
                            <option value="deactivated">Deactivated</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <div class="flex items-center ml-4">
                        <label for="departmentFilter" class="mr-2 text-sm">Department</label>
                        <select 
                            id="departmentFilter" 
                            wire:model.live="departmentId"
                            class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center ml-4">
                        <label for="designationFilter" class="mr-2 text-sm">Designation</label>
                        <select 
                            id="designationFilter" 
                            wire:model.live="designationId"
                            class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">All Designations</option>
                            @foreach($designations as $designation)
                                <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                            @endforeach
                        </select>
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
                <div class="border rounded-md bg-white overflow-visible">
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-visible">
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
                                                        <div class="text-xs text-green-800 bg-green-100 font-medium px-2 py-0.5 rounded-sm inline-block mt-1">
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
                                            @if($user->supervisors && $user->supervisors->count() > 0)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($user->supervisors as $supervisor)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $supervisor->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
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

                                                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                                    <div class="py-1" role="menu">
                                                        <button wire:click="openEditUserModal('{{ $user->id }}')" @click="open = false" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                            Edit User
                                                        </button>
                                                        
                                                        <div class="border-t border-gray-100"></div>
                                                        
                                                        <button wire:click="changeDepartment('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                            </svg>
                                                            Change Department
                                                        </button>
                                                        
                                                        <button wire:click="updateDesignation('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                            </svg>
                                                            Update Designation
                                                        </button>
                                                        
                                                        <button wire:click="changeSupervisor('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                            </svg>
                                                            Change Supervisor
                                                        </button>
                                                        
                                                        <button wire:click="updatePassword('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                            </svg>
                                                            Update Password
                                                        </button>
                                                        
                                                        <button wire:click="ipRestriction('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left" role="menuitem">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                            </svg>
                                                            IP Restriction
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
                                                        
                                                        @if($userStatusFilter === 'archived')
                                                            <button wire:click="unarchiveUser('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-green-600 hover:bg-gray-100 text-left" role="menuitem">
                                                                <svg class="mr-3 h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                                </svg>
                                                                Unarchive
                                                            </button>
                                                        @else
                                                            <button wire:click="confirmDelete('{{ $user->id }}')" class="flex items-center w-full px-4 py-2 text-sm text-yellow-600 hover:bg-gray-100 text-left" role="menuitem">
                                                                <svg class="mr-3 h-4 w-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                                </svg>
                                                                Archive
                                                            </button>
                                                            
                                                            <button wire:click="openDeleteConfirmationModal('{{ $user->id }}')" @click="open = false" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 text-left" role="menuitem">
                                                                <svg class="mr-3 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                                Delete
                                                            </button>
                                                        @endif
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
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Archive User</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to archive <strong>{{ $selectedUser->name }}</strong>? The user will be hidden from the active list but can be restored later.
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
                            Archive
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteConfirmationModal && $selectedUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showDeleteConfirmationModal') }" x-show="show" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeDeleteConfirmationModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete User</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        You are about to take action on <strong class="text-gray-900">{{ $selectedUser->name }}</strong>. Please choose one of the following options:
                                    </p>
                                    
                                    <div class="space-y-3 mt-4">
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-yellow-800 mb-2">Option 1: Archive (Recommended)</h4>
                                            <p class="text-xs text-yellow-700 mb-3">
                                                The user will be hidden from the active list but all data will be preserved. You can restore the user later if needed.
                                            </p>
                                            <button wire:click="confirmArchiveUser" 
                                                    class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors text-sm font-medium">
                                                Archive User
                                            </button>
                                        </div>
                                        
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-red-800 mb-2">Option 2: Permanent Delete</h4>
                                            <p class="text-xs text-red-700 mb-2">
                                                ⚠️ <strong>Warning:</strong> This action cannot be undone!
                                            </p>
                                            <p class="text-xs text-red-700 mb-3">
                                                All records associated with this user will be permanently removed from the database, including:
                                            </p>
                                            <ul class="text-xs text-red-700 list-disc list-inside mb-3 space-y-1">
                                                <li>Attendance records</li>
                                                <li>Leave requests</li>
                                                <li>Project assignments</li>
                                                <li>Supervisor relationships</li>
                                                <li>All other related data</li>
                                            </ul>
                                            <button wire:click="confirmPermanentDelete" 
                                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm font-medium">
                                                Permanently Delete User
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex justify-end">
                        <button wire:click="closeDeleteConfirmationModal" 
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                            Cancel
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

    <!-- Add User Modal -->
    @if($showAddUserModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeAddUserModal"></div>

                <!-- This element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full max-h-[90vh] overflow-y-auto">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                                Add New User
                            </h3>
                            <button wire:click="closeAddUserModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="createUser" class="space-y-6">
                            <!-- Basic Information -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-4">Basic Information</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="newUserName" class="block text-sm font-medium text-gray-700">Name *</label>
                                        <input 
                                            type="text"
                                            id="newUserName"
                                            wire:model="newUser.name"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        />
                                        @error('newUser.name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="newUserEmail" class="block text-sm font-medium text-gray-700">Email *</label>
                                        <input 
                                            type="email"
                                            id="newUserEmail"
                                            wire:model="newUser.email"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        />
                                        @error('newUser.email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="newUserGender" class="block text-sm font-medium text-gray-700">Gender *</label>
                                        <select 
                                            id="newUserGender"
                                            wire:model="newUser.gender"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        >
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                        @error('newUser.gender') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="newUserPhone" class="block text-sm font-medium text-gray-700">Phone</label>
                                        <input 
                                            type="text"
                                            id="newUserPhone"
                                            wire:model="newUser.phone"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                        @error('newUser.phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="newUserEmployeeCode" class="block text-sm font-medium text-gray-700">Employee Code</label>
                                        <input 
                                            type="text"
                                            id="newUserEmployeeCode"
                                            wire:model="newUser.employee_code"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                        @error('newUser.employee_code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Password Fields -->
                                    <div class="space-y-2">
                                        <label for="newUserPassword" class="block text-sm font-medium text-gray-700">Password *</label>
                                        <div class="relative">
                                            <input 
                                                type="{{ $showNewPassword ? 'text' : 'password' }}"
                                                id="newUserPassword"
                                                wire:model="newUser.password"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="Minimum 6 characters"
                                                required
                                            />
                                            <button type="button"
                                                    wire:click="$toggle('showNewPassword')"
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                                @if($showNewPassword)
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
                                        @error('newUser.password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Role & Organization -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-4">Role & Organization</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="newUserRole" class="block text-sm font-medium text-gray-700">Role *</label>
                                        <select 
                                            id="newUserRole"
                                            wire:model="newUser.user_level_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        >
                                            <option value="">Select role</option>
                                            @foreach($userLevels as $level)
                                                <option value="{{ $level->id }}">{{ ucfirst($level->name) }}</option>
                                            @endforeach
                                        </select>
                                        @error('newUser.user_level_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="newUserStatus" class="block text-sm font-medium text-gray-700">Status *</label>
                                        <select 
                                            id="newUserStatus"
                                            wire:model="newUser.status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        >
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        @error('newUser.status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="newUserDepartment" class="block text-sm font-medium text-gray-700">Department</label>
                                        <select 
                                            id="newUserDepartment"
                                            wire:model="newUser.department_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">Select department</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('newUser.department_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="newUserDesignation" class="block text-sm font-medium text-gray-700">Designation</label>
                                        <select 
                                            id="newUserDesignation"
                                            wire:model="newUser.designation_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">Select designation</option>
                                            @foreach($designations as $designation)
                                                <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('newUser.designation_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Primary Supervisor -->
                                    <div class="space-y-2">
                                        <label for="newUserPrimarySupervisor" class="block text-sm font-medium text-gray-700">Primary Supervisor</label>
                                        <select 
                                            id="newUserPrimarySupervisor"
                                            wire:model="newUser.primary_supervisor_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">Select Primary Supervisor</option>
                                            @foreach($supervisors as $supervisor)
                                                <option value="{{ $supervisor->id }}">
                                                    {{ $supervisor->name }} ({{ ucfirst($supervisor->userLevel->name) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">Primary supervisor</p>
                                        @error('newUser.primary_supervisor_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Secondary Supervisor -->
                                    <div class="space-y-2">
                                        <label for="newUserSecondarySupervisor" class="block text-sm font-medium text-gray-700">Secondary Supervisor</label>
                                        <select 
                                            id="newUserSecondarySupervisor"
                                            wire:model="newUser.secondary_supervisor_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">Select Secondary Supervisor</option>
                                            @foreach($supervisors as $supervisor)
                                                <option value="{{ $supervisor->id }}" {{ $supervisor->id == $newUser['primary_supervisor_id'] ? 'disabled' : '' }}>
                                                    {{ $supervisor->name }} ({{ ucfirst($supervisor->userLevel->name) }})
                                                    @if($supervisor->id == $newUser['primary_supervisor_id'])
                                                        (Already selected as primary)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">Secondary Supervisor (optional)</p>
                                        @error('newUser.secondary_supervisor_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Project Assignment -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-4">Project Assignment</h4>
                                @if(count($projects) > 0)
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($projects as $project)
                                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors
                                                {{ in_array($project->id, $newUserProjects) ? 'bg-blue-50 border-blue-500' : '' }}">
                                                <input type="checkbox" 
                                                       wire:click="toggleNewProjectSelection('{{ $project->id }}')"
                                                       {{ in_array($project->id, $newUserProjects) ? 'checked' : '' }}
                                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <span class="ml-3 text-sm text-gray-900">{{ $project->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No active projects available</p>
                                @endif
                            </div>

                            <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3 -mx-6 -mb-4 mt-6">
                                <button 
                                    type="button"
                                    wire:click="closeAddUserModal"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors"
                                >
                                    Add User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit User Modal -->
    @if($showEditUserModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeEditUserModal"></div>

                <!-- This element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full max-h-[90vh] overflow-y-auto">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                                Edit User
                            </h3>
                            <button wire:click="closeEditUserModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="updateUser" class="space-y-6">
                            <!-- Basic Information -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-4">Basic Information</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="editUserName" class="block text-sm font-medium text-gray-700">Name *</label>
                                        <input 
                                            type="text"
                                            id="editUserName"
                                            wire:model="editUser.name"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        />
                                        @error('editUser.name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="editUserEmail" class="block text-sm font-medium text-gray-700">Email *</label>
                                        <input 
                                            type="email"
                                            id="editUserEmail"
                                            wire:model="editUser.email"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        />
                                        @error('editUser.email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="editUserGender" class="block text-sm font-medium text-gray-700">Gender *</label>
                                        <select 
                                            id="editUserGender"
                                            wire:model="editUser.gender"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        >
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                        @error('editUser.gender') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="editUserPhone" class="block text-sm font-medium text-gray-700">Phone</label>
                                        <input 
                                            type="text"
                                            id="editUserPhone"
                                            wire:model="editUser.phone"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                        @error('editUser.phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="editUserEmployeeCode" class="block text-sm font-medium text-gray-700">Employee Code</label>
                                        <input 
                                            type="text"
                                            id="editUserEmployeeCode"
                                            wire:model="editUser.employee_code"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                        @error('editUser.employee_code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Password Fields (Optional) -->
                                    <div class="space-y-2">
                                        <label for="editUserPassword" class="block text-sm font-medium text-gray-700">Password (leave blank to keep current)</label>
                                        <div class="relative">
                                            <input 
                                                type="{{ $showEditPassword ? 'text' : 'password' }}"
                                                id="editUserPassword"
                                                wire:model="editUser.password"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="Minimum 6 characters"
                                            />
                                            <button type="button"
                                                    wire:click="$toggle('showEditPassword')"
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                                @if($showEditPassword)
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
                                        @error('editUser.password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="editUserPasswordConfirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                        <input 
                                            type="{{ $showEditPassword ? 'text' : 'password' }}"
                                            id="editUserPasswordConfirmation"
                                            wire:model="editUser.password_confirmation"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="Re-enter password"
                                        />
                                        @error('editUser.password_confirmation') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Role & Organization -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-4">Role & Organization</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="editUserRole" class="block text-sm font-medium text-gray-700">Role *</label>
                                        <select 
                                            id="editUserRole"
                                            wire:model="editUser.user_level_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        >
                                            <option value="">Select role</option>
                                            @foreach($userLevels as $level)
                                                <option value="{{ $level->id }}">{{ ucfirst($level->name) }}</option>
                                            @endforeach
                                        </select>
                                        @error('editUser.user_level_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="editUserStatus" class="block text-sm font-medium text-gray-700">Status *</label>
                                        <select 
                                            id="editUserStatus"
                                            wire:model="editUser.status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        >
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        @error('editUser.status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="editUserDepartment" class="block text-sm font-medium text-gray-700">Department</label>
                                        <select 
                                            id="editUserDepartment"
                                            wire:model="editUser.department_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">Select department</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('editUser.department_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="editUserDesignation" class="block text-sm font-medium text-gray-700">Designation</label>
                                        <select 
                                            id="editUserDesignation"
                                            wire:model="editUser.designation_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">Select designation</option>
                                            @foreach($designations as $designation)
                                                <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('editUser.designation_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Primary Supervisor -->
                                    <div class="space-y-2">
                                        <label for="editUserPrimarySupervisor" class="block text-sm font-medium text-gray-700">Primary Supervisor</label>
                                        <select 
                                            id="editUserPrimarySupervisor"
                                            wire:model="editUser.primary_supervisor_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">Select Primary Supervisor</option>
                                            @foreach($supervisors as $supervisor)
                                                @if($supervisor->id != $editUser['id'])
                                                    <option value="{{ $supervisor->id }}">
                                                        {{ $supervisor->name }} ({{ ucfirst($supervisor->userLevel->name) }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">Primary supervisor</p>
                                        @error('editUser.primary_supervisor_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Secondary Supervisor -->
                                    <div class="space-y-2">
                                        <label for="editUserSecondarySupervisor" class="block text-sm font-medium text-gray-700">Secondary Supervisor</label>
                                        <select 
                                            id="editUserSecondarySupervisor"
                                            wire:model="editUser.secondary_supervisor_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">Select Secondary Supervisor</option>
                                            @foreach($supervisors as $supervisor)
                                                @if($supervisor->id != $editUser['id'])
                                                    <option value="{{ $supervisor->id }}" {{ $supervisor->id == $editUser['primary_supervisor_id'] ? 'disabled' : '' }}>
                                                        {{ $supervisor->name }} ({{ ucfirst($supervisor->userLevel->name) }})
                                                        @if($supervisor->id == $editUser['primary_supervisor_id'])
                                                            (Already selected as primary)
                                                        @endif
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">Secondary Supervisor (optional)</p>
                                        @error('editUser.secondary_supervisor_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Project Assignment -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-4">Project Assignment</h4>
                                @if(count($projects) > 0)
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($projects as $project)
                                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors
                                                {{ in_array($project->id, $editUserProjects) ? 'bg-blue-50 border-blue-500' : '' }}">
                                                <input type="checkbox" 
                                                       wire:click="toggleEditProjectSelection('{{ $project->id }}')"
                                                       {{ in_array($project->id, $editUserProjects) ? 'checked' : '' }}
                                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <span class="ml-3 text-sm text-gray-900">{{ $project->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No active projects available</p>
                                @endif
                            </div>

                            <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3 -mx-6 -mb-4 mt-6">
                                <button 
                                    type="button"
                                    wire:click="closeEditUserModal"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors"
                                >
                                    Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Change Department Modal -->
    @if($showChangeDepartmentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeChangeDepartmentModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Change Department</h3>
                            <button wire:click="closeChangeDepartmentModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="saveChangeDepartment" class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-2">User: <strong>{{ $changeDepartmentData['user_name'] }}</strong></p>
                                <p class="text-sm text-gray-600 mb-4">Current Department: <strong>{{ $changeDepartmentData['current_department'] }}</strong></p>
                            </div>

                            <div>
                                <label for="newDepartment" class="block text-sm font-medium text-gray-700">New Department *</label>
                                <select 
                                    id="newDepartment"
                                    wire:model="changeDepartmentData.new_department_id"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required
                                >
                                    <option value="">Select department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('changeDepartmentData.new_department_id') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3 -mx-6 -mb-4 mt-6">
                                <button 
                                    type="button"
                                    wire:click="closeChangeDepartmentModal"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors"
                                >
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Change Supervisor Modal -->
    @if($showChangeSupervisorModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeChangeSupervisorModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Change Supervisor</h3>
                            <button wire:click="closeChangeSupervisorModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="saveChangeSupervisor" class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-md">
                                <p class="text-sm text-gray-600 mb-2">User: <strong>{{ $changeSupervisorData['user_name'] }}</strong></p>
                                <p class="text-sm text-gray-600">Current Primary: <strong>{{ $changeSupervisorData['current_primary_supervisor'] }}</strong></p>
                                <p class="text-sm text-gray-600">Current Secondary: <strong>{{ $changeSupervisorData['current_secondary_supervisor'] }}</strong></p>
                            </div>

                            <div>
                                <label for="newPrimarySupervisor" class="block text-sm font-medium text-gray-700 mb-1">Primary Supervisor</label>
                                <select 
                                    id="newPrimarySupervisor"
                                    wire:model="changeSupervisorData.new_primary_supervisor_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Select Primary Supervisor</option>
                                    @foreach($supervisors as $supervisor)
                                        @if($supervisor->id != $changeSupervisorData['user_id'])
                                            <option value="{{ $supervisor->id }}">
                                                {{ $supervisor->name }} ({{ $supervisor->userLevel->name ?? 'N/A' }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Primary supervisor</p>
                                @error('changeSupervisorData.new_primary_supervisor_id') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div>
                                <label for="newSecondarySupervisor" class="block text-sm font-medium text-gray-700 mb-1">Secondary Supervisor</label>
                                <select 
                                    id="newSecondarySupervisor"
                                    wire:model="changeSupervisorData.new_secondary_supervisor_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Select Secondary Supervisor</option>
                                    @foreach($supervisors as $supervisor)
                                        @if($supervisor->id != $changeSupervisorData['user_id'])
                                            <option value="{{ $supervisor->id }}" {{ $supervisor->id == $changeSupervisorData['new_primary_supervisor_id'] ? 'disabled' : '' }}>
                                                {{ $supervisor->name }} ({{ $supervisor->userLevel->name ?? 'N/A' }})
                                                @if($supervisor->id == $changeSupervisorData['new_primary_supervisor_id'])
                                                    (Already selected as primary)
                                                @endif
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Secondary Supervisor (optional)</p>
                                @error('changeSupervisorData.new_secondary_supervisor_id') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3 -mx-6 -mb-4 mt-6">
                                <button 
                                    type="button"
                                    wire:click="closeChangeSupervisorModal"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors"
                                >
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- IP Restriction Modal -->
    @if($showIpRestrictionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeIpRestrictionModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900">IP Restriction</h3>
                            <button wire:click="closeIpRestrictionModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="saveIpRestriction" class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-4">User: <strong>{{ $ipRestrictionData['user_name'] }}</strong></p>
                            </div>

                            <div>
                                <label for="ipAddress" class="block text-sm font-medium text-gray-700">Allowed IP Address</label>
                                <input 
                                    type="text"
                                    id="ipAddress"
                                    wire:model="ipRestrictionData.ip_address"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="e.g., 192.168.1.1"
                                />
                                <p class="mt-1 text-xs text-gray-500">Leave blank to allow access from any IP address</p>
                                @error('ipRestrictionData.ip_address') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3 -mx-6 -mb-4 mt-6">
                                <button 
                                    type="button"
                                    wire:click="closeIpRestrictionModal"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors"
                                >
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Update Password Modal -->
    @if($showUpdatePasswordModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeUpdatePasswordModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Update Password</h3>
                            <button wire:click="closeUpdatePasswordModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="saveUpdatePassword" class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-4">User: <strong>{{ $updatePasswordData['user_name'] }}</strong></p>
                            </div>

                            <div>
                                <label for="newPassword" class="block text-sm font-medium text-gray-700">New Password *</label>
                                <input 
                                    type="password"
                                    id="newPassword"
                                    wire:model="updatePasswordData.new_password"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Minimum 6 characters"
                                    required
                                />
                                @error('updatePasswordData.new_password') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div>
                                <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                                <input 
                                    type="password"
                                    id="confirmPassword"
                                    wire:model="updatePasswordData.confirm_password"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Re-enter password"
                                    required
                                />
                                @error('updatePasswordData.confirm_password') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3 -mx-6 -mb-4 mt-6">
                                <button 
                                    type="button"
                                    wire:click="closeUpdatePasswordModal"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors"
                                >
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Update Designation Modal -->
    @if($showUpdateDesignationModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeUpdateDesignationModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Update Designation</h3>
                            <button wire:click="closeUpdateDesignationModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="saveUpdateDesignation" class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-2">User: <strong>{{ $updateDesignationData['user_name'] }}</strong></p>
                                <p class="text-sm text-gray-600 mb-4">Current Designation: <strong>{{ $updateDesignationData['current_designation'] }}</strong></p>
                            </div>

                            <div>
                                <label for="newDesignation" class="block text-sm font-medium text-gray-700">New Designation</label>
                                <select 
                                    id="newDesignation"
                                    wire:model="updateDesignationData.new_designation_id"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">Select designation</option>
                                    @foreach($designations as $designation)
                                        <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                    @endforeach
                                </select>
                                @error('updateDesignationData.new_designation_id') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3 -mx-6 -mb-4 mt-6">
                                <button 
                                    type="button"
                                    wire:click="closeUpdateDesignationModal"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors"
                                >
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Auto Punch Out Modal -->
    @if($showAutoPunchOutModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeAutoPunchOutModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Auto Punch Out Time</h3>
                            <button wire:click="closeAutoPunchOutModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="saveAutoPunchOut" class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-4">User: <strong>{{ $autoPunchOutData['user_name'] }}</strong></p>
                            </div>

                            <div>
                                <label for="autoPunchOutTime" class="block text-sm font-medium text-gray-700">Auto Punch Out Time</label>
                                <input 
                                    type="time"
                                    id="autoPunchOutTime"
                                    wire:model="autoPunchOutData.auto_punch_out_time"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Set a specific time for this user, or leave empty to use the global setting
                                </p>
                                @error('autoPunchOutData.auto_punch_out_time') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-xs text-gray-700">
                                        <strong>Note:</strong> If no time is set, this user will be automatically clocked out at the 
                                        <strong>global auto clock-out time</strong> configured in System Settings.
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-6 py-3 flex justify-between items-center -mx-6 -mb-4 mt-6">
                                <button 
                                    type="button"
                                    wire:click="removeAutoPunchOut"
                                    class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-md transition-colors text-sm font-medium"
                                    wire:confirm="Are you sure you want to remove the auto punch out time for this user? They will use the global setting instead."
                                >
                                    Remove Setting
                                </button>
                                <div class="flex space-x-3">
                                    <button 
                                        type="button"
                                        wire:click="closeAutoPunchOutModal"
                                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors"
                                    >
                                        Cancel
                                    </button>
                                    <button 
                                        type="submit"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors"
                                    >
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Last In Time Modal -->
    @if($showLastInTimeModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeLastInTimeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Last Punch In Time</h3>
                            <button wire:click="closeLastInTimeModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-4">User: <strong>{{ $lastInTimeData['user_name'] }}</strong></p>
                            </div>

                            <div class="border rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase mb-1">Last Punch In</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $lastInTimeData['last_punch_in'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase mb-1">Project</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $lastInTimeData['project'] }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-xs text-gray-500 uppercase mb-1">Task</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $lastInTimeData['task'] }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-xs text-gray-500 uppercase mb-1">Message</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $lastInTimeData['message'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-6 py-3 flex justify-end -mx-6 -mb-4 mt-6">
                                <button 
                                    type="button"
                                    wire:click="closeLastInTimeModal"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
