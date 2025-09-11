<turbo-frame id="time-entry-create-form">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <h2 class="text-lg sm:text-xl font-medium text-gray-900 mb-1">{{ __('Manual Time Entry') }}</h2>
            <p class="text-gray-600 text-sm sm:text-base">{{ __('Add time entries with specific start and end times, client, and project details.') }}</p>
        </div>
        <a href="{{ route('turbo.time-entries.create') }}" class="bg-gray-900 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-md font-medium hover:bg-gray-800 transition-colors inline-flex items-center justify-center space-x-2 w-full sm:w-auto" data-turbo-frame="time-entry-create-form">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>{{ __('Add Manual Entry') }}</span>
        </a>
    </div>
</turbo-frame>
