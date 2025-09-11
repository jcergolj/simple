<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Simple') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600" rel="stylesheet" />
    <link href="{{ tailwindcss('css/app.css') }}" rel="stylesheet" data-turbo-track="reload" />
</head>
<body class="min-h-screen bg-white">
    <!-- Navigation -->
    @if (Route::has('login'))
        <nav class="border-b border-gray-100">
            <div class="mx-auto px-2 sm:px-4 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-6">
                        <h1 class="text-lg font-medium text-gray-900">{{ config('app.name', 'Simple') }}</h1>
                        <a href="https://github.com/jcergolj/simple" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-gray-900 transition-colors flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium">GitHub</span>
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800 transition-colors">{{ __('Dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">{{ __('Log in') }}</a>
                            @if (Route::has('register') && !\App\Models\User::exists())
                                <a href="{{ route('register') }}" class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800 transition-colors">{{ __('Register') }}</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <!-- Hero Section -->
    <div class="bg-white">
        <div class="mx-auto px-2 sm:px-4 lg:px-8 py-20 text-center">
            <!-- App Icon -->
            <div class="w-16 h-16 mx-auto mb-8 bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="h-8 w-8 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">{{ __('Simple Time Tracking') }}</h1>
            <p class="text-xl text-gray-600 mb-12 max-w-2xl mx-auto">{{ __('Clean, focused time tracking. Manage projects and track your work without the complexity.') }}</p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-center gap-4 mb-20">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-gray-900 text-white px-8 py-3 rounded-md font-medium hover:bg-gray-800 transition-colors">
                        {{ __('Go to Dashboard') }}
                    </a>
                @else
                    @if (Route::has('register') && !\App\Models\User::exists())
                        <a href="{{ route('register') }}" class="bg-gray-900 text-white px-8 py-3 rounded-md font-medium hover:bg-gray-800 transition-colors">
                            {{ __('Get Started') }}
                        </a>
                    @endif
                    <a href="{{ route('login') }}" class="border border-gray-300 text-gray-700 px-8 py-3 rounded-md font-medium hover:bg-gray-50 transition-colors">
                        {{ __('Sign In') }}
                    </a>
                @endauth
            </div>

            <!-- Features Grid -->
            <!-- Features Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Time Tracking -->
    <div class="p-6">
        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4 mx-auto">
            <svg class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Time Tracking') }}</h3>
        <p class="text-gray-600">
            {{ __('Track work effortlessly. Start, pause, and stop timers in seconds with shortcuts—let the app handle the timing.') }}
        </p>
    </div>

    <!-- Stay Organized -->
    <div class="p-6">
        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4 mx-auto">
            <svg class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Effortless Organization') }}</h3>
        <p class="text-gray-600">
            {{ __('Add projects, clients, and tasks inline. Everything stays in one place, simple and intuitive.') }}
        </p>
    </div>

    <!-- Privacy Focused -->
    <div class="p-6">
        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4 mx-auto">
            <svg class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Privacy Focused') }}</h3>
        <p class="text-gray-600">
            {{ __('Your data, your control. Self-hosted, secure, and private—the app tracks time, not you.') }}
        </p>
    </div>
</div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-100">
        <div class="mx-auto px-2 sm:px-4 lg:px-8 py-12 text-center">
            <p class="text-gray-600">{{ config('app.name', 'Simple') }} © {{ date('Y') }} - {{ __('Built with Laravel') }}</p>
        </div>
    </footer>
</body>
</html>
