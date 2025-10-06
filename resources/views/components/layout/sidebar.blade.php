@props([
    'collapsed' => false
])

@php
    $user = auth()->user();
    $userRole = $user->role ?? 'USER';
    $currentRoute = request()->route()->getName();
    
    // Define navigation items based on role
    $navigationItems = [
        [
            'label' => 'Dashboard',
            'route' => match($userRole) {
                'ADMIN' => 'admin.dashboard',
                'SUPERVISOR' => 'supervisor.dashboard',
                default => 'dashboard'
            },
            'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            'roles' => ['USER', 'SUPERVISOR', 'ADMIN']
        ],
        [
            'label' => 'Attendance',
            'route' => 'attendance.index',
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            'roles' => ['USER', 'SUPERVISOR', 'ADMIN']
        ],
        [
            'label' => 'Leaves',
            'route' => 'leaves.index',
            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'roles' => ['USER', 'SUPERVISOR', 'ADMIN']
        ],
        [
            'label' => 'Reports',
            'route' => 'reports.index',
            'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'roles' => ['USER', 'SUPERVISOR', 'ADMIN']
        ],
        [
            'label' => 'Notices',
            'route' => 'notices.index',
            'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
            'roles' => ['USER', 'SUPERVISOR', 'ADMIN']
        ],
        [
            'type' => 'divider',
            'roles' => ['ADMIN']
        ],
        [
            'label' => 'Users',
            'route' => 'users.index',
            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'roles' => ['ADMIN']
        ],
        [
            'label' => 'Departments',
            'route' => 'departments.index',
            'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'roles' => ['ADMIN']
        ],
        [
            'label' => 'Designations',
            'route' => 'designations.index',
            'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            'roles' => ['ADMIN']
        ],
        [
            'label' => 'Projects',
            'route' => 'projects.index',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'roles' => ['ADMIN']
        ],
        [
            'label' => 'Leave Categories',
            'route' => 'leave-categories.index',
            'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
            'roles' => ['ADMIN']
        ],
        [
            'label' => 'Holidays',
            'route' => 'holidays.index',
            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'roles' => ['ADMIN']
        ],
    ];
    
    // Filter navigation items based on user role
    $filteredItems = array_filter($navigationItems, function($item) use ($userRole) {
        return in_array($userRole, $item['roles'] ?? []);
    });
@endphp

<aside {{ $attributes->merge(['class' => 'bg-white border-r border-gray-200 h-screen overflow-y-auto']) }}>
    <div class="h-full flex flex-col">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 lg:h-16">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-900 lg:block hidden">ClockIn</span>
            </div>
            <button 
                @click="$dispatch('toggle-sidebar')"
                class="p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:hidden"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- User Info -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                <div class="flex-1 min-w-0 lg:block hidden">
                    <p class="text-sm font-medium text-gray-900 truncate">
                        {{ $user->name }}
                    </p>
                    <p class="text-xs text-gray-500 truncate">
                        {{ ucfirst(strtolower($userRole)) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            @foreach($filteredItems as $item)
                @if(isset($item['type']) && $item['type'] === 'divider')
                    <div class="my-4 border-t border-gray-200"></div>
                @else
                    @php
                        $isActive = $currentRoute === $item['route'] || 
                                    (isset($item['activeRoutes']) && in_array($currentRoute, $item['activeRoutes']));
                    @endphp
                    <a 
                        href="{{ route($item['route']) }}" 
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 {{ $isActive ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
                        wire:navigate
                    >
                        <svg class="flex-shrink-0 w-5 h-5 mr-3 {{ $isActive ? 'text-blue-700' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path>
                        </svg>
                        <span class="lg:block hidden">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <!-- Logout Button -->
        <div class="p-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button 
                    type="submit"
                    class="flex items-center w-full px-3 py-2 text-sm font-medium text-red-700 rounded-md hover:bg-red-50 transition-colors duration-150"
                >
                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="lg:block hidden">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
