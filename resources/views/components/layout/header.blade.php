@props([
    'title' => null
])

@php
    $user = auth()->user();
@endphp

<header {{ $attributes->merge(['class' => 'sticky top-0 z-30 bg-white border-b border-gray-200 px-4 py-4 flex justify-between items-center']) }}>
    <!-- Left side: Title -->
    <div class="flex items-center">
        <h1 class="text-xl font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
    </div>

    <!-- Right side: Notifications and User Profile -->
    <div class="flex items-center gap-4">
        <!-- Notification Dropdown -->
        @livewire('notifications.notification-dropdown')

        <!-- User Profile Dropdown -->
        <div x-data="{ open: false }" @click.away="open = false" class="relative">
            <button 
                @click="open = !open"
                class="flex items-center gap-2 text-sm"
            >
                <div class="w-8 h-8 bg-lgf-blue rounded-full flex items-center justify-center text-white">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <span class="hidden md:inline-block">{{ $user->name }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
            >
                <!-- User Info -->
                <div class="p-2 text-sm">
                    <p class="font-medium">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                </div>
                
                <div class="border-t border-gray-100"></div>
                
                <!-- Profile Settings Link -->
                <a 
                    href="{{ route('profile') }}" 
                    class="flex w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    wire:navigate
                >
                    Profile Settings
                </a>
                
                <div class="border-t border-gray-100"></div>
                
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button 
                        type="submit"
                        class="flex w-full px-4 py-2 text-sm text-red-500 hover:bg-gray-100"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
