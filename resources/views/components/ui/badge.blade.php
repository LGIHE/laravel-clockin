@props([
    'variant' => 'default',
    'size' => 'md'
])

@php
$variantClasses = match($variant) {
    'default' => 'bg-gray-100 text-gray-800',
    'primary' => 'bg-blue-100 text-blue-800',
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'danger' => 'bg-red-100 text-red-800',
    'info' => 'bg-cyan-100 text-cyan-800',
    'secondary' => 'bg-purple-100 text-purple-800',
    default => 'bg-gray-100 text-gray-800'
};

$sizeClasses = match($size) {
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-0.5 text-sm',
    'lg' => 'px-3 py-1 text-base',
    default => 'px-2.5 py-0.5 text-sm'
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full font-medium {$variantClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</span>
