<x-layouts.app :title="__('Clients')">
    <div class="space-y-8" data-controller="inline-edit">
        <!-- Page Header -->
        <div class="text-center py-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Clients') }}</h1>
            <p class="text-gray-600">{{ __('Manage your clients and their hourly rates efficiently.') }}</p>
        </div>

        <!-- Add Client Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <turbo-frame id="client-create-form">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-medium text-gray-900 mb-1">{{ __('Add New Client') }}</h2>
                        <p class="text-gray-600">{{ __('Create a new client profile with billing information.') }}</p>
                    </div>
                    <a href="{{ route('turbo.clients.create') }}" class="bg-gray-900 text-white px-6 py-3 rounded-md font-medium hover:bg-gray-800 transition-colors inline-flex items-center space-x-2">
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
