@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false
])

@php
$variantClasses = match($variant) {
    'primary' => 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm focus:ring-blue-500',
    'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-900 focus:ring-gray-500',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white shadow-sm focus:ring-red-500',
    'success' => 'bg-green-600 hover:bg-green-700 text-white shadow-sm focus:ring-green-500',
    'outline' => 'border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 focus:ring-blue-500',
    'ghost' => 'hover:bg-gray-100 text-gray-700 focus:ring-gray-500',
    'link' => 'text-blue-600 hover:text-blue-700 underline-offset-4 hover:underline focus:ring-blue-500',
    default => 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm focus:ring-blue-500'
};

$sizeClasses = match($size) {
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-base',
    'lg' => 'px-6 py-3 text-lg',
    'icon' => 'p-2',
    default => 'px-4 py-2 text-base'
};
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "inline-flex items-center justify-center rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none {$variantClasses} {$sizeClasses}",
        'disabled' => $disabled
    ]) }}
>
    {{ $slot }}
</button>
