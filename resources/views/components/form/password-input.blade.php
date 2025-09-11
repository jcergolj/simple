@props(['class' => ''])

<div class="relative {{ $class }}" data-controller="password-reveal">
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <x-heroicon-o-key aria-hidden="true" class="h-5 w-5 text-gray-400"/>
        </div>

        <input {{ $attributes->merge(['type' => 'password', 'data-password-reveal-target' => 'input', 'class' => 'w-full pl-10 pr-12 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent data-error:border-red-500']) }} />

        <button class="absolute inset-y-0 right-0 pr-3 flex items-center" type="button" data-action="password-reveal#toggle turbo:before-cache@document->password-reveal#reset">
            <span class="grid grid-cols-1">
                <x-heroicon-o-eye aria-hidden="true" class="[:where([data-password-reveal-revealed-value=true]_&)]:hidden h-5 w-5 text-gray-400 hover:text-gray-500 col-start-1 row-start-1"/>
                <x-heroicon-o-eye-slash aria-hidden="true" class="hidden [:where([data-password-reveal-revealed-value=true]_&)]:block! h-5 w-5 text-gray-400 hover:text-gray-500 col-start-1 row-start-1"/>
            </span>

            <span class="sr-only">{{ __('Reveal') }}</span>
        </button>
    </div>
</div>
