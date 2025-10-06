@props([
    'type' => 'text',
    'disabled' => false,
    'error' => null
])

@php
$baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-colors';
$errorClasses = $error ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '';
$disabledClasses = $disabled ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : '';
@endphp

<input 
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "{$baseClasses} {$errorClasses} {$disabledClasses}",
        'disabled' => $disabled
    ]) }}
>
