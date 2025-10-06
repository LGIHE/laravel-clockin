<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        @isset($header)
            <x-layout.header>
                {{ $header }}
            </x-layout.header>
        @endisset

        <!-- Page Content -->
        <div class="flex">
            <!-- Sidebar -->
            @isset($sidebar)
                <x-layout.sidebar>
                    {{ $sidebar }}
                </x-layout.sidebar>
            @endisset

            <!-- Main Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>

        <!-- Footer -->
        @isset($footer)
            <x-layout.footer>
                {{ $footer }}
            </x-layout.footer>
        @endisset
    </div>

    <!-- Toast Notifications -->
    <x-ui.toast />

    @livewireScripts
</body>
</html>
