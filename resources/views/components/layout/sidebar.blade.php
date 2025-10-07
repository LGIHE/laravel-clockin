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
            'route' => 'attendance.user',
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            'roles' => ['USER', 'SUPERVISOR', 'ADMIN']
        ],
        [
            'label' => 'Users',
            'route' => 'users.index',
            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'roles' => ['ADMIN']
        ],
        [
            'label' => 'Designations',
            'route' => 'designations.index',
            'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            'roles' => ['ADMIN']
        ],
        [
            'label' => 'Departments',
            'route' => 'departments.index',
            'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'roles' => ['ADMIN']
        ],
        [
            'label' => 'Projects',
            'route' => 'projects.index',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'roles' => ['ADMIN']
        ],
        [
            'label' => 'Holidays',
            'route' => 'holidays.index',
            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'roles' => ['ADMIN']
        ],
        [
            'label' => 'Notices',
            'route' => 'notices.index',
            'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
            'roles' => ['USER', 'SUPERVISOR', 'ADMIN']
        ],
        [
            'label' => 'Leave',
            'type' => 'submenu',
            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'roles' => ['USER', 'SUPERVISOR', 'ADMIN'],
            'activeRoutes' => ['leaves.apply', 'leaves.index', 'leave-categories.index'],
            'submenu' => [
                [
                    'label' => 'Apply',
                    'route' => 'leaves.apply',
                    'roles' => ['USER', 'SUPERVISOR', 'ADMIN']
                ],
                [
                    'label' => 'View All',
                    'route' => 'leaves.index',
                    'roles' => ['ADMIN', 'SUPERVISOR']
                ],
                [
                    'label' => 'Category',
                    'route' => 'leave-categories.index',
                    'roles' => ['ADMIN']
                ],
            ]
        ],
        [
            'label' => 'Reports',
            'type' => 'submenu',
            'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'roles' => ['USER', 'SUPERVISOR', 'ADMIN'],
            'activeRoutes' => ['reports.index', 'reports.individual', 'reports.summary'],
            'submenu' => [
                [
                    'label' => 'View All',
                    'route' => 'reports.index',
                    'roles' => ['ADMIN']
                ],
                [
                    'label' => 'Individual Report',
                    'route' => 'reports.individual',
                    'roles' => ['ADMIN', 'SUPERVISOR']
                ],
                [
                    'label' => 'Summary Report',
                    'route' => 'reports.summary',
                    'roles' => ['ADMIN']
                ],
            ]
        ],
    ];
    
    // Filter navigation items based on user role
    $filteredItems = array_filter($navigationItems, function($item) use ($userRole) {
        return in_array($userRole, $item['roles'] ?? []);
    });
@endphp

<aside {{ $attributes->merge(['class' => 'w-64 h-screen border-r border-gray-200 bg-white flex flex-col fixed left-0 top-0']) }}>
    <!-- Sidebar Header with Logo -->
    <div class="p-4 border-b border-gray-200">
        <a href="{{ route(match($userRole) { 'ADMIN' => 'admin.dashboard', 'SUPERVISOR' => 'supervisor.dashboard', default => 'dashboard' }) }}" class="flex items-center gap-2">
            <div class="bg-lgf-blue text-white font-bold p-2 rounded">
                <span class="text-lg">LGF</span>
            </div>
            <div class="text-gray-700 font-medium leading-tight">
                <div class="text-xs uppercase">Luigi</div>
                <div class="text-xs uppercase">Giussani Foundation</div>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1">
            @foreach($filteredItems as $item)
                @if(isset($item['type']) && $item['type'] === 'submenu')
                    @php
                        $isActive = isset($item['activeRoutes']) && in_array($currentRoute, $item['activeRoutes']);
                        $filteredSubmenu = array_filter($item['submenu'] ?? [], function($subItem) use ($userRole) {
                            return in_array($userRole, $subItem['roles'] ?? []);
                        });
                    @endphp
                    @if(count($filteredSubmenu) > 0)
                        <li x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
                            <button
                                @click="open = !open"
                                class="flex items-center px-4 py-3 text-sm w-full justify-between {{ $isActive ? 'bg-lgf-blue text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                            >
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path>
                                    </svg>
                                    <span>{{ $item['label'] }}</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            <ul x-show="open" x-transition class="pl-10 mt-1 space-y-1">
                                @foreach($filteredSubmenu as $subItem)
                                    @php
                                        $isSubActive = $currentRoute === $subItem['route'];
                                    @endphp
                                    <li>
                                        <a
                                            href="{{ route($subItem['route']) }}"
                                            class="block py-2 px-4 text-sm rounded {{ $isSubActive ? 'text-lgf-blue font-medium' : 'text-gray-600 hover:text-gray-900' }}"
                                            wire:navigate
                                        >
                                            {{ $subItem['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @else
                    @php
                        $isActive = isset($item['route']) && ($currentRoute === $item['route'] || 
                                    (isset($item['activeRoutes']) && in_array($currentRoute, $item['activeRoutes'])));
                    @endphp
                    <li>
                        <a 
                            href="{{ route($item['route']) }}" 
                            class="flex items-center px-4 py-3 text-sm {{ $isActive ? 'bg-lgf-blue text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                            wire:navigate
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path>
                            </svg>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</aside>
