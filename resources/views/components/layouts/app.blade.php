@props([
    'title' => config('app.name', 'ClockIn'),
    'breadcrumbs' => []
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'ClockIn') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen">
        @auth
            <!-- Mobile Sidebar Overlay -->
            <div 
                x-show="sidebarOpen" 
                x-cloak
                @click="sidebarOpen = false"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
            ></div>

            <!-- Flex Container for Sidebar and Main Content -->
            <div class="flex">
                <!-- Sidebar -->
                <x-layout.sidebar 
                    :collapsed="false"
                    x-bind:class="{ 
                        'translate-x-0': sidebarOpen, 
                        '-translate-x-full': !sidebarOpen,
                        'lg:translate-x-0': true,
                        'lg:w-64': !sidebarCollapsed,
                        'lg:w-20': sidebarCollapsed
                    }"
                    class="fixed inset-y-0 left-0 z-50 transition-all duration-300 ease-in-out transform lg:static lg:translate-x-0"
                />

                <!-- Main Content Area -->
                <div class="flex-1 flex flex-col lg:pl-0">
                    <!-- Header -->
                    <x-layout.header 
                        @toggle-sidebar="sidebarOpen = !sidebarOpen"
                        @toggle-collapse="sidebarCollapsed = !sidebarCollapsed"
                    />

                    <!-- Breadcrumbs -->
                    @if(!empty($breadcrumbs))
                        <x-layout.breadcrumbs :items="$breadcrumbs" />
                    @endif

                    <!-- Page Content -->
                    <main class="flex-1 py-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {{ $slot }}
                        </div>
                    </main>
                </div>
            </div>
        @else
            <!-- Guest Layout (Login, etc.) -->
            <main>
                {{ $slot }}
            </main>
        @endauth
    </div>

    <!-- Toast Notifications -->
    <div
        x-data="{ show: false, message: '', variant: 'info' }"
        x-on:toast.window="
            message = $event.detail.message;
            variant = $event.detail.variant || 'info';
            show = true;
            setTimeout(() => show = false, 5000);
        "
        x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-bind:class="{
            'bg-blue-600 text-white': variant === 'info',
            'bg-green-600 text-white': variant === 'success',
            'bg-yellow-600 text-white': variant === 'warning',
            'bg-red-600 text-white': variant === 'danger'
        }"
        class="fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg pointer-events-auto"
        style="display: none;"
    >
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg x-show="variant === 'success'" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="variant === 'warning'" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <svg x-show="variant === 'danger'" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="variant === 'info'" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium" x-text="message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="show = false" class="inline-flex rounded-md hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
