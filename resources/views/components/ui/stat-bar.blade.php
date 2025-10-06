@props([
    'label' => '',
    'value' => 0,
    'max' => 100,
    'color' => 'blue'
])

@php
$percentage = $max > 0 ? min(($value / $max) * 100, 100) : 0;
$colorClasses = match($color) {
    'blue' => 'bg-blue-500',
    'green' => 'bg-green-500',
    'red' => 'bg-red-500',
    'yellow' => 'bg-yellow-500',
    'purple' => 'bg-purple-500',
    default => 'bg-blue-500'
};
@endphp

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <div class="flex justify-between items-center text-sm">
        <span class="font-medium text-gray-700">{{ $label }}</span>
        <span class="text-gray-900 font-semibold">{{ $value }} / {{ $max }}</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
        <div class="{{ $colorClasses }} h-3 rounded-full transition-all duration-500 ease-out" style="width: {{ $percentage }}%"></div>
    </div>
    <div class="text-xs text-gray-500 text-right">{{ number_format($percentage, 1) }}%</div>
</div>
