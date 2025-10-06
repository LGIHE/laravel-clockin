@props([
    'disabled' => false,
    'label' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
    'min' => null,
    'max' => null
])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label {{ $attributes->only('id')->merge(['for' => $attributes->get('id')]) }} class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        <input
            type="date"
            {{ $attributes->except(['class', 'label', 'error', 'hint', 'required'])->merge([
                'class' => 'block w-full rounded-md shadow-sm sm:text-sm transition-colors ' . 
                          ($error ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500') .
                          ($disabled ? ' bg-gray-50 text-gray-500 cursor-not-allowed' : ''),
                'disabled' => $disabled,
                'min' => $min,
                'max' => $max
            ]) }}
        >
    </div>
    
    @if($hint && !$error)
        <p class="mt-1 text-sm text-gray-500">{{ $hint }}</p>
    @endif
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
