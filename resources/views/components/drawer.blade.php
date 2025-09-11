@props(['id', 'asideWidth' => 'w-64'])

<div class="drawer">
    @if ($checkbox ?? false)
        {{ $checkbox }}
    @else
        <input id="{{ $id }}" type="checkbox" class="drawer-toggle" />
    @endif

    <div class="drawer-content">
        {{ $slot }}
    </div>

    <div class="drawer-side">
        <label for="{{ $id }}" aria-label="close sidebar" class="drawer-overlay"></label>

        <div class="bg-white min-h-full {{ $asideWidth }} flex flex-col border-r border-gray-200">
            {{ $aside }}
        </div>
    </div>
</div>
