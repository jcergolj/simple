<x-layouts.app.sidebar :title="$title ?? null">
    <div class="mx-auto max-w-7xl px-2 sm:px-4 lg:px-8 py-8">
        {{ $slot }}
    </div>
</x-layouts.app.sidebar>
