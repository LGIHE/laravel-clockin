@props([
    'title' => null
])

<header {{ $attributes->merge(['class' => 'bg-white shadow-sm border-b border-gray-200']) }}>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                @if($title)
                    <h1 class="text-2xl font-semibold text-gray-900">{{ $title }}</h1>
                @endif
                {{ $slot }}
            </div>

            <div class="flex items-center space-x-2">
                <!-- Notification Dropdown -->
                @auth
                    @livewire('notifications.notification-dropdown')
                @endauth

                @isset($actions)
                    {{ $actions }}
                @endisset
            </div>
        </div>
    </div>
</header>
