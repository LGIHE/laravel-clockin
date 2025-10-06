@props([
    'title' => null
])

@php
    $user = auth()->user();
@endphp

<header {{ $attributes->merge(['class' => 'bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30']) }}>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left side: Mobile menu button -->
            <div class="flex items-center">
                <button 
                    @click="$dispatch('toggle-sidebar')"
                    class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 lg:hidden"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                @if($title)
                    <h1 class="ml-4 text-xl font-semibold text-gray-900 lg:text-2xl">{{ $title }}</h1>
                @endif
            </div>

            <!-- Right side: Notifications and User Profile -->
            <div class="flex items-center space-x-4">
                <!-- Notification Dropdown -->
                @livewire('notifications.notification-dropdown')

                <!-- User Profile Dropdown -->
                <div x-data="{ open: false }" @click.away="open = false" class="relative">
                    <button 
                        @click="open = !open"
                        class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst(strtolower($user->role ?? 'User')) }}</p>
                        </div>
                        <svg class="hidden md:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div 
                        x-show="open"
                        x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100"
                    >
                        <!-- User Info -->
                        <div class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ ucfirst(strtolower($user->role ?? 'User')) }}
                                @if($user->department)
                                    • {{ $user->department->name }}
                                @endif
                            </p>
                        </div>

                        <!-- Navigation Links -->
                        <div class="py-1">
                            <a 
                                href="{{ route(match($user->role) {
                                    'ADMIN' => 'admin.dashboard',
                                    'SUPERVISOR' => 'supervisor.dashboard',
                                    default => 'dashboard'
                                }) }}" 
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                wire:navigate
                            >
                                <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Dashboard
                            </a>
                            <a 
                                href="{{ route('attendance.index') }}" 
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                wire:navigate
                            >
                                <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                My Attendance
                            </a>
                            <a 
                                href="{{ route('leaves.index') }}" 
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                wire:navigate
                            >
                                <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                My Leaves
                            </a>
                        </div>

                        <!-- Settings & Logout -->
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button 
                                    type="submit"
                                    class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50"
                                >
                                    <svg class="mr-3 h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
