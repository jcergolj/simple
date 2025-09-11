@props(['transitions' => true, 'scalable' => false, 'title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', [
            'transitions' => $transitions,
            'scalable' => $scalable,
            'title' => $title,
        ])

        <meta name="turbo-refresh-method" content="morph">
        <meta name="turbo-refresh-scroll" content="preserve">
    </head>
    <body @class(["min-h-screen bg-gray-50", "hotwire-native" => Turbo::isHotwireNativeVisit()]) data-controller="session-recovery">
        <!-- Top Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="mx-auto px-2 sm:px-4 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left side - Logo and main navigation -->
                    <div class="flex items-center">
                        <!-- Logo -->
                        <a href="{{ route('dashboard') }}" class="flex items-center mr-8">
                            <x-app-logo />
                        </a>

                        <!-- Desktop Navigation -->
                        <div class="hidden md:flex space-x-1">
                            <x-navbar.nav-item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-navbar.nav-item>
                            <x-navbar.nav-item icon="user-group" :href="route('clients.index')" :current="request()->routeIs('clients.*')">
                                {{ __('Clients') }}
                            </x-navbar.nav-item>
                            <x-navbar.nav-item icon="briefcase" :href="route('projects.index')" :current="request()->routeIs('projects.*')">
                                {{ __('Projects') }}
                            </x-navbar.nav-item>
                            <x-navbar.nav-item icon="clock" :href="route('time-entries.index')" :current="request()->routeIs('time-entries.*')">
                                {{ __('Time Entries') }}
                            </x-navbar.nav-item>
                            <x-navbar.nav-item icon="chart-bar" :href="route('reports.index')" :current="request()->routeIs('reports.*')">
                                {{ __('Reports') }}
                            </x-navbar.nav-item>
                        </div>
                    </div>

                    <!-- Right side - User menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Settings link -->
                        <a href="{{ route('settings') }}" class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-md transition-colors">
                            <x-profile :initials="auth()->user()->initials()" class="p-0!" />
                            <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                        </a>

                        <!-- Mobile menu button -->
                        <div class="md:hidden" data-controller="mobile-menu">
                            <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-500" aria-controls="mobile-menu" aria-expanded="false" data-action="mobile-menu#toggle">
                                <span class="sr-only">Open main menu</span>
                                <svg data-mobile-menu-target="openIcon" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <svg data-mobile-menu-target="closeIcon" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

            <!-- Mobile menu -->
            <div data-mobile-menu-target="menu" class="md:hidden hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
                    <x-navbar.nav-item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" class="block w-full text-left" data-action="click->mobile-menu#close">
                        {{ __('Dashboard') }}
                    </x-navbar.nav-item>
                    <x-navbar.nav-item icon="user-group" :href="route('clients.index')" :current="request()->routeIs('clients.*')" class="block w-full text-left" data-action="click->mobile-menu#close">
                        {{ __('Clients') }}
                    </x-navbar.nav-item>
                    <x-navbar.nav-item icon="briefcase" :href="route('projects.index')" :current="request()->routeIs('projects.*')" class="block w-full text-left" data-action="click->mobile-menu#close">
                        {{ __('Projects') }}
                    </x-navbar.nav-item>
                    <x-navbar.nav-item icon="clock" :href="route('time-entries.index')" :current="request()->routeIs('time-entries.*')" class="block w-full text-left" data-action="click->mobile-menu#close">
                        {{ __('Time Entries') }}
                    </x-navbar.nav-item>
                    <x-navbar.nav-item icon="chart-bar" :href="route('reports.index')" :current="request()->routeIs('reports.*')" class="block w-full text-left" data-action="click->mobile-menu#close">
                        {{ __('Reports') }}
                    </x-navbar.nav-item>
                </div>
            </div>
                        </div>
                    </div>
                </div>
            </div>

        </nav>

        <x-in-app-notifications::notification />

        <!-- Main Content -->
        <main class="bg-white">
            {{ $slot }}
        </main>
    </body>
</html>
