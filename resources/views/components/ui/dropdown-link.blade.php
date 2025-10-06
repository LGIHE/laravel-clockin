@props([
    'active' => false
])

@php
$classes = $active 
    ? 'bg-gray-100 text-gray-900' 
    : 'text-gray-700 hover:bg-gray-50';
@endphp

<a {{ $attributes->merge(['class' => "block px-4 py-2 text-sm {$classes}"]) }}>
    {{ $slot }}
</a>
