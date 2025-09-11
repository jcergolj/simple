@props(['transitions' => true, 'scalable' => false, 'title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', [
            'transitions' => $transitions,
            'scalable' => $scalable,
            'title' => $title,
        ])
    </head>
    <body @class(["min-h-screen antialiased bg-gray-50", "hotwire-native" => Turbo::isHotwireNativeVisit()]) data-controller="session-recovery">
        <div class="flex min-h-screen flex-col items-center justify-center px-2 py-12">
            <x-in-app-notifications::notification />

            <div class="w-full max-w-lg px-4">
                <div class="text-center mb-8">
                    <a href="{{ route('home') }}" class="inline-flex items-center">
                        <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center mb-2">
                            <svg class="h-8 w-8 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                        </div>
                    </a>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </div>

                <div class="bg-white py-8 px-6 shadow-sm rounded-lg border border-gray-200">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
