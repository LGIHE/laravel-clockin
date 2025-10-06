@props([
    'items' => []
])

@if(!empty($items))
<nav class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-3" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2 text-sm">
        <!-- Home -->
        <li>
            <a 
                href="{{ route(match(auth()->user()->role) {
                    'ADMIN' => 'admin.dashboard',
                    'SUPERVISOR' => 'supervisor.dashboard',
                    default => 'dashboard'
                }) }}" 
                class="text-gray-500 hover:text-gray-700 transition-colors"
                wire:navigate
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                </svg>
                <span class="sr-only">Home</span>
            </a>
        </li>

        @foreach($items as $index => $item)
            <li class="flex items-center">
                <!-- Separator -->
                <svg class="flex-shrink-0 w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>

                @if($index === count($items) - 1)
                    <!-- Last item (current page) -->
                    <span class="ml-2 text-gray-900 font-medium" aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @else
                    <!-- Intermediate items -->
                    @if(isset($item['url']))
                        <a 
                            href="{{ $item['url'] }}" 
                            class="ml-2 text-gray-500 hover:text-gray-700 transition-colors"
                            wire:navigate
                        >
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="ml-2 text-gray-500">
                            {{ $item['label'] }}
                        </span>
                    @endif
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
