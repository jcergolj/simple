<x-layouts.app :title="__('Clients')">
    <div class="space-y-8" data-controller="inline-edit">
        <!-- Page Header -->
        <div class="text-center py-2 sm:py-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ __('Clients') }}</h1>
            <p class="text-gray-600 text-sm sm:text-base">{{ __('Manage your clients and their hourly rates efficiently.') }}</p>
        </div>

        <!-- Search Filter -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-4">{{ __('Search Clients') }}</h3>
            <form method="GET" action="{{ route('clients.index') }}">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Search by name') }}</label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="{{ __('Enter client name...') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-base">
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-end space-y-2 sm:space-y-0 sm:space-x-2">
                        <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md font-medium hover:bg-gray-800 transition-colors flex items-center justify-center space-x-2 w-full sm:w-auto">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>{{ __('Search') }}</span>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('clients.index') }}" class="text-gray-600 hover:text-gray-900 px-4 py-2 font-medium transition-colors text-center">
                                {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Add Client Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <turbo-frame id="client-create-form">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <div>
                        <h2 class="text-lg sm:text-xl font-medium text-gray-900 mb-1">{{ __('Add New Client') }}</h2>
                        <p class="text-gray-600 text-sm sm:text-base">{{ __('Create a new client profile with billing information.') }}</p>
                    </div>
                    <a href="{{ route('turbo.clients.create') }}" class="bg-gray-900 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-md font-medium hover:bg-gray-800 transition-colors inline-flex items-center justify-center space-x-2 w-full sm:w-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>{{ __('Add Client') }}</span>
                    </a>
                </div>
            </turbo-frame>
        </div>

        <!-- Clients List -->
        <turbo-frame id="clients-lists">
            @include('turbo::clients.list', ['clients' => $clients])
        </turbo-frame>
    </div>
</x-layouts.app>
