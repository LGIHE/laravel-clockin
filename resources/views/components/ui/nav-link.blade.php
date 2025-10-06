@props([
    'active' => false,
    'icon' => null
])

@php
$classes = $active 
    ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-700' 
    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent';
@endphp

<a {{ $attributes->merge(['class' => "flex items-center px-4 py-2.5 text-sm font-medium transition-colors {$classes}"]) }}>
    @if($icon)
        <span class="mr-3">
            {!! $icon !!}
        </span>
    @endif
    <span>{{ $slot }}</span>
</a>
