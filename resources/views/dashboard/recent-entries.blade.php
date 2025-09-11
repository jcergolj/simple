<div class="bg-white rounded-lg border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-medium text-gray-900">{{ __('Recent Time Entries') }}</h3>
            <a href="{{ route('time-entries.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">{{ __('View all') }}</a>
        </div>
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($recentEntries->take(5) as $entry)
            <div class="px-6 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <p class="font-medium text-gray-900 truncate">
                                {{ $entry->client->name ?? __('No Client') }}
                                @if($entry->project)
                                    <span class="text-gray-500">- {{ $entry->project->name }}</span>
                                @endif
                            </p>
                            @if(!$entry->end_time)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <span class="w-1.5 h-1.5 bg-red-400 rounded-full mr-1 animate-pulse"></span>
                                    {{ __('Running') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('Done') }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-4 mt-1">
                            <div class="text-xs text-gray-500">
                                {{ $entry->start_time->format('M j, g:i A') }}
                                @if($entry->end_time)
                                    - {{ $entry->end_time->format('g:i A') }}
                                @endif
                            </div>
                            @if($entry->notes)
                                <div class="text-xs text-gray-400 truncate max-w-xs">{{ $entry->notes }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                        @if($entry->duration)
                            <div class="text-sm font-mono font-medium text-gray-900">
                                {{ $entry->getFormattedDuration() }}
                            </div>
                        @endif
                        @if($entry->calculateEarnings())
                            <div class="text-xs text-gray-500">
                                {{ $entry->calculateEarnings()->formatted() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center">
                <div class="w-12 h-12 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 font-medium">{{ __('No time entries yet') }}</p>
                <p class="text-gray-500 text-sm">{{ __('Start your first timer above to get started!') }}</p>
            </div>
        @endforelse
    </div>
    
    @if($recentEntries->count() > 5)
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
            <div class="text-center">
                <a href="{{ route('time-entries.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    {{ __('View all :count entries', ['count' => $recentEntries->count()]) }} →
                </a>
            </div>
        </div>
    @endif
</div>