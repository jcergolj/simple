@props([
    'name',
    'value' => '',
    'placeholder' => '',
    'dataError' => false,
])

@php
    $classes = 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-base';
    if ($dataError) {
        $classes .= ' border-red-500';
    }
    
    $userFormat = auth()->user()?->getPreferredDateFormat();
    $formatExample = $userFormat ? $userFormat->example() : '';
@endphp

<div>
    <input 
        type="date" 
        name="{{ $name }}" 
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($placeholder)
            placeholder="{{ $placeholder }}"
        @endif
    />
    @if($userFormat)
        <p class="mt-1 text-xs text-gray-500">{{ __('Format: :format', ['format' => $formatExample]) }}</p>
    @endif
</div>