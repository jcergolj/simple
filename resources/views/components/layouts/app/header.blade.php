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
    <body data-controller="sidebar" data-layout="header" data-action="turbo:before-cache@document->sidebar#close" @class(["min-h-screen bg-base-200", "hotwire-native" => Turbo::isHotwireNativeVisit()])>
        <x-drawer id="main-sidebar">
            <x-slot name="checkbox">
                <input id="main-sidebar" data-sidebar-target="checkbox" type="checkbox" class="drawer-toggle" />
            </x-slot>

            @unlesshotwirenative
                <header class="min-h-14 flex items-center border-b border-base-300 bg-base-100">
                    <div class="mx-auto w-full h-full px-2 sm:px-4 lg:px-8 flex items-center">
                        <x-drawer.toggle for="main-sidebar" icon="bars-3" class="lg:hidden px-2.5 [&>div>svg]:size-5! [&>div>svg]:mr-0! h-10" />

                        <a href="{{ route('dashboard') }}" class="ml-2 mr-5 flex items-center space-x-2 lg:ml-0">
                            <x-app-logo />
                        </a>

                        <x-navbar class="-mb-px max-lg:hidden">
                            <x-navbar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                                <span>{{ __('Dashboard') }}</span>
                            </x-navbar.item>
                        </x-navbar>

                        <div class="flex-1"></div>

                        <x-navbar class="mr-1.5 space-x-0.5 !py-0">
                            <x-navbar.item icon="magnifying-glass" href="#" class="px-2.5 [&>div>svg]:size-5! [&>div>svg]:mr-0! h-10">
                                <span class="sr-only">{{ __('Search') }}</span>
                            </x-navbar.item>
                        </x-navbar>

                        <!-- Desktop User Menu -->
                        <a href="{{ route('settings') }}">
                            <x-profile
                                class="cursor-pointer"
                                :initials="auth()->user()->initials()"
                            />
                        </a>
                    </div>
                </header>
            @endunlesshotwirenative

            <x-in-app-notifications::notification />

            <div>
                {{ $slot }}
            </div>

            <x-slot name="aside">
                <div class="flex items-center space-x-2">
                    <x-drawer.toggle for="main-sidebar" icon="x-mark" class="lg:hidden px-2 [&>div>svg]:size-5! [&>div>svg]:mr-0! h-10" />
                </div>

                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-1.5 my-4">
                    <x-app-logo />
                </a>

                <x-sidebar.navlist class="px-0">
                    <x-sidebar.navlist-item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-sidebar>
                </x-sidebar.navlist>

                <div class="flex-1 w-0"></div>
            </x-slot>
        </x-drawer>
    </body>
</html>
