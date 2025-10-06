@props([
    'disabled' => false,
    'label' => null,
    'error' => null,
    'hint' => null,
    'required' => false
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
    
    <input
        {{ $attributes->except(['class', 'label', 'error', 'hint', 'required'])->merge([
            'type' => 'text',
            'class' => 'block w-full rounded-md shadow-sm sm:text-sm transition-colors ' . 
                      ($error ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500') .
                      ($disabled ? ' bg-gray-50 text-gray-500 cursor-not-allowed' : ''),
            'disabled' => $disabled
        ]) }}
    >
    
    @if($hint && !$error)
        <p class="mt-1 text-sm text-gray-500">{{ $hint }}</p>
    @endif
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
