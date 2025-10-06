@props([
    'showCopyright' => true
])

<footer {{ $attributes->merge(['class' => 'bg-white border-t border-gray-200 mt-auto']) }}>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                {{ $slot }}
            </div>

            @if($showCopyright)
                <div class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} ClockIn. All rights reserved.
                </div>
            @endif
        </div>
    </div>
</footer>
