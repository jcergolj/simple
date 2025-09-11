@props(['icon' => null, 'current' => false, 'as' => 'a'])

@if ($as === 'a')
<a {{ $attributes->merge(['class' => 'flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ' . ($current ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50')]) }}>
    @if ($icon)
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-4 h-4 mr-2" />
    @endif
    {{ $slot }}
</a>
@else
<button {{ $attributes->merge(['type' => 'button', 'class' => 'flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ' . ($current ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50')]) }}>
    @if ($icon)
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-4 h-4 mr-2" />
    @endif
    {{ $slot }}
</button>
@endif