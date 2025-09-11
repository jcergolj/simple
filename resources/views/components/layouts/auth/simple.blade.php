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
    <body @class(["min-h-screen antialiased bg-gray-50", "hotwire-native" => Turbo::isHotwireNativeVisit()])>
        <div class="flex min-h-screen flex-col items-center justify-center px-6 py-12">
            <x-in-app-notifications::notification />

            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <a href="{{ route('home') }}" class="inline-flex items-center">
                        <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center mb-2">
                            <x-app-logo-icon class="w-6 h-6 text-white" />
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
