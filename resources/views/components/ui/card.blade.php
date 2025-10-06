@props([
    'padding' => true,
    'shadow' => true
])

@php
$paddingClass = $padding ? 'p-6' : '';
$shadowClass = $shadow ? 'shadow-md' : '';
@endphp

<div {{ $attributes->merge(['class' => "bg-white rounded-lg border border-gray-200 {$shadowClass} {$paddingClass}"]) }}>
    {{ $slot }}
</div>
