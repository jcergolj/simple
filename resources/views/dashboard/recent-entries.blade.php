<div id="recent-entries" class="bg-white rounded-xl shadow-lg border border-gray-100">
    <div class="px-4 sm:px-8 py-4 sm:py-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ __('Recent Time Entries') }}</h3>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">{{ __('Your latest time tracking activity') }}</p>
            </div>
            <a href="{{ route('time-entries.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center justify-center sm:justify-start space-x-2 text-sm sm:text-base" data-turbo-frame="_top">
                <span>{{ __('View all') }}</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($recentEntries->take(5) as $entry)
            <x-recent-time-entry :entry="$entry" :running-timer="$runningTimer ?? null" />
        @empty
            <div class="px-4 sm:px-8 py-12 sm:py-16 text-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mb-4 sm:mb-6">
                    <svg class="h-8 w-8 sm:h-10 sm:w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">{{ __('No time entries yet') }}</h4>
                <p class="text-gray-600 text-sm sm:text-base">{{ __('Start your first timer above to begin tracking your time!') }}</p>
            </div>
        @endforelse
    </div>

    @if($recentEntries->count() > 5)
        <div class="px-4 sm:px-8 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
            <div class="text-center">
                <a href="{{ route('time-entries.index') }}" class="text-gray-700 hover:text-gray-900 font-medium transition-colors inline-flex items-center justify-center space-x-2 text-sm sm:text-base">
                    <span>{{ __('View all :count entries', ['count' => $recentEntries->count()]) }}</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>
    @endif
</div>
