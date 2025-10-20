@props([
    'collapsed' => false
])

@php
    $user = auth()->user();
    $userRole = $user->role ?? 'USER';
    $currentRoute = request()->route()->getName();
    $hasSupervisees = $user->supervisedUsers()->count() > 0;
    
    // Define navigation items based on permissions
    $navigationItems = [
        [
            'label' => 'Dashboard',
            'route' => match($userRole) {
                'ADMIN' => 'admin.dashboard',
                'SUPERVISOR' => 'supervisor.dashboard',
                default => 'dashboard'
            },
            'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            'permission' => null, // Always show dashboard
        ],
        [
            'label' => 'Supervisor Dashboard',
            'route' => 'supervisor.dashboard',
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            'permission' => null,
            'showCondition' => $hasSupervisees
        ],
        [
            'label' => 'Attendance',
            'route' => 'attendance.user',
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            'permission' => 'attendance.view-own',
        ],
        [
            'label' => 'Tasks',
            'route' => 'tasks.index',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            'permission' => 'tasks.view-own',
        ],
        [
            'label' => 'Users',
            'route' => 'users.index',
            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'permission' => 'users.view',
        ],
        [
            'label' => 'Designations',
            'route' => 'designations.index',
            'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            'permission' => 'designations.view',
        ],
        [
            'label' => 'Departments',
            'route' => 'departments.index',
            'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'permission' => 'departments.view',
        ],
        [
            'label' => 'Projects',
            'route' => 'projects.index',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'permission' => 'projects.view',
        ],
        [
            'label' => 'Holidays',
            'route' => 'holidays.index',
            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'permission' => 'holidays.view',
        ],
        [
            'label' => 'Notices',
            'route' => 'notices.index',
            'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
            'permission' => 'notices.view',
        ],
        [
            'label' => 'Leave',
            'type' => 'submenu',
            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'permission' => 'leaves.view-own',
            'activeRoutes' => ['leaves.apply', 'leaves.index', 'leave-categories.index', 'compensation-leaves.index'],
            'submenu' => [
                [
                    'label' => 'Apply Leave',
                    'route' => 'leaves.apply',
                    'permission' => 'leaves.apply'
                ],
                [
                    'label' => 'Compensation Leave',
                    'route' => 'compensation-leaves.index',
                    'permission' => null // Available to all users
                ],
                [
                    'label' => 'View All Leaves',
                    'route' => 'leaves.index',
                    'permission' => 'leaves.view-all'
                ],
                [
                    'label' => 'Leave Categories',
                    'route' => 'leave-categories.index',
                    'permission' => 'leave-categories.view'
                ],
            ]
        ],
        [
            'label' => 'Reports',
            'type' => 'submenu',
            'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'permission' => 'reports.view',
            'activeRoutes' => ['reports.index', 'reports.individual', 'reports.summary'],
            'submenu' => [
                [
                    'label' => 'View All',
                    'route' => 'reports.index',
                    'permission' => 'reports.view'
                ],
                [
                    'label' => 'Individual Report',
                    'route' => 'reports.individual',
                    'permission' => 'reports.individual'
                ],
                [
                    'label' => 'Summary Report',
                    'route' => 'reports.summary',
                    'permission' => 'reports.summary'
                ],
            ]
        ],
        [
            'label' => 'Roles & Permissions',
            'route' => 'roles.index',
            'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'permission' => 'roles.view',
            'activeRoutes' => ['roles.index', 'roles.create', 'roles.edit']
        ],
        [
            'label' => 'Settings',
            'route' => 'settings.index',
            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'permission' => 'settings.view',
            'activeRoutes' => ['settings.index', 'settings.logs', 'settings.stats']
        ],
    ];
    
    // Filter navigation items based on permissions
    $filteredItems = array_filter($navigationItems, function($item) use ($user) {
        // Check custom show condition
        if (isset($item['showCondition']) && $item['showCondition'] === false) {
            return false;
        }
        
        // If no permission required, show the item
        if (!isset($item['permission']) || $item['permission'] === null) {
            return true;
        }
        
        // Check if user has the required permission
        return $user->hasPermission($item['permission']);
    });
@endphp

<aside {{ $attributes->merge(['class' => 'w-64 h-screen border-r border-gray-200 bg-white flex flex-col fixed left-0 top-0']) }}>
    <!-- Sidebar Header with Logo -->
    <div class="p-4 border-b border-gray-200">
        <a href="{{ route(match($userRole) { 'ADMIN' => 'admin.dashboard', 'SUPERVISOR' => 'supervisor.dashboard', default => 'dashboard' }) }}" class="flex items-center gap-2">
            @php
                $appLogo = settings('app_logo');
                $appName = settings('app_name', 'Luigi Giussani Foundation');
            @endphp
            
            @if($appLogo)
                <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ $appName }}" class="h-10 w-auto">
            @else
                <div class="bg-lgf-blue text-white font-bold p-2 rounded">
                    <span class="text-lg">LGF</span>
                </div>
            @endif
            
            <div class="text-gray-700 font-medium leading-tight">
                <div class="text-sm font-semibold">{{ $appName }}</div>
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
                        $filteredSubmenu = array_filter($item['submenu'] ?? [], function($subItem) use ($user) {
                            // If no permission required, show the item
                            if (!isset($subItem['permission']) || $subItem['permission'] === null) {
                                return true;
                            }
                            // Check if user has the required permission
                            return $user->hasPermission($subItem['permission']);
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
