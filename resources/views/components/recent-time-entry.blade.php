@props(['entry', 'runningTimer' => null])

<turbo-frame id="recent-entry-{{ $entry->id }}">
    <div class="px-4 sm:px-8 py-4 sm:py-6 hover:bg-gray-50 transition-colors group">
        <!-- Mobile Layout (sm and below) -->
        <div class="sm:hidden">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <h4 class="font-semibold text-base text-gray-900 truncate">
                        {{ $entry->client->name ?? __('No Client') }}
                    </h4>
                    @if($entry->project)
                        <p class="text-sm text-gray-500 truncate">{{ $entry->project->name }}</p>
                    @endif
                </div>
                @if($entry->duration)
                    <div class="text-right ml-3">
                        <div class="text-lg font-mono font-bold text-gray-900">
                            {{ $entry->getFormattedDuration() }}
                        </div>
                        @if($entry->calculateEarnings())
                            <div class="text-xs text-gray-600">
                                {{ $entry->calculateEarnings()->formatted() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            
            <!-- Time info row -->
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mb-3 text-xs text-gray-600">
                <div class="flex items-center">
                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>
                        <x-user-datetime :datetime="$entry->start_time" />
                        @if($entry->end_time)
                            - <x-user-time :time="$entry->end_time" />
                        @endif
                    </span>
                </div>
                
                @if($entry->getEffectiveHourlyRate())
                    <div class="flex items-center text-blue-600">
                        <span class="font-medium">{{ $entry->getEffectiveHourlyRate()->formatted() }}/hr</span>
                    </div>
                @endif
            </div>
            
            @if($entry->notes)
                <div class="mb-3">
                    <p class="text-sm text-gray-500 line-clamp-2">{{ $entry->notes }}</p>
                </div>
            @endif
            
            <!-- Action buttons -->
            <div class="flex flex-wrap gap-2">
                @if(!$entry->end_time)
                    <span class="inline-flex items-center px-2 py-1 h-7 rounded text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1 animate-pulse"></span>
                        <span class="animate-pulse">{{ __('Running') }}</span>
                    </span>
                @elseif($runningTimer)
                    <button type="button"
                            disabled
                            class="bg-gray-100 text-gray-400 px-2 py-1 h-7 rounded text-xs font-medium cursor-not-allowed inline-flex items-center space-x-1"
                            title="{{ __('Another timer is running') }}">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ __('Start') }}</span>
                    </button>
                @else
                    <form action="{{ route('turbo.running-timer-session.store') }}" method="POST" data-turbo-frame="timer-widget" class="inline">
                        @csrf
                        <input type="hidden" name="client_id" value="{{ $entry->client_id }}">
                        <input type="hidden" name="project_id" value="{{ $entry->project_id }}">
                        <button type="submit"
                                class="bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 h-7 rounded text-xs font-medium transition-colors inline-flex items-center space-x-1">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ __('Start') }}</span>
                        </button>
                    </form>
                @endif

                <a href="{{ route('turbo.time-entries.edit', $entry) }}"
                   class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 h-7 rounded text-xs font-medium transition-colors inline-flex items-center space-x-1"
                   data-turbo-frame="recent-entry-{{ $entry->id }}">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>{{ __('Edit') }}</span>
                </a>

                <a href="{{ route('time-entries.destroy', ['timeEntry' => $entry, 'is_recent' => true]) }}"
                   data-turbo-method="delete"
                   data-turbo-confirm="{{ __('Are you sure you want to delete this time entry?') }}"
                   class="bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 h-7 rounded text-xs font-medium transition-colors inline-flex items-center space-x-1"
                   data-turbo-frame="_top">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>{{ __('Delete') }}</span>
                </a>
            </div>
        </div>

        <!-- Desktop Layout (sm and above) -->
        <div class="hidden sm:block">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-3 mb-3">
                        <!-- Project/Client Info -->
                        <div class="flex-1">
                            <h4 class="font-semibold text-lg text-gray-900 truncate">
                                {{ $entry->client->name ?? __('No Client') }}
                                @if($entry->project)
                                    <span class="text-gray-500 font-normal">→ {{ $entry->project->name }}</span>
                                @endif
                            </h4>
                        </div>
                    </div>
                    
                    <!-- Time and Notes -->
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center text-gray-600">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm">
                                <x-user-datetime :datetime="$entry->start_time" />
                                @if($entry->end_time)
                                    - <x-user-time :time="$entry->end_time" />
                                @endif
                            </span>
                        </div>
                        
                        @if($entry->getEffectiveHourlyRate())
                            <div class="flex items-center text-blue-600">
                                <span class="text-sm font-medium">{{ $entry->getEffectiveHourlyRate()->formatted() }}/hr</span>
                            </div>
                        @endif
                        
                        @if($entry->notes)
                            <div class="flex items-center text-gray-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                <span class="text-sm truncate max-w-xs">{{ $entry->notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Duration and Actions -->
                <div class="flex items-center space-x-6">
                    <!-- Duration & Earnings with Hourly Rate -->
                    <div class="text-right">
                        @if($entry->duration)
                            <div class="text-xl font-mono font-bold text-gray-900">
                                {{ $entry->getFormattedDuration() }}
                            </div>
                        @endif
                        <div class="space-y-1 mt-1">
                            @if($entry->calculateEarnings())
                                <div class="text-sm text-gray-600">
                                    {{ $entry->calculateEarnings()->formatted() }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        @if(!$entry->end_time)
                            <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-800 border border-red-200">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></span>
                                <span class="animate-pulse">{{ __('Running') }}</span>
                            </span>
                        @elseif($runningTimer)
                            <button type="button"
                                    disabled
                                    class="bg-gray-100 text-gray-400 px-3 py-2 rounded-lg text-sm font-medium cursor-not-allowed inline-flex items-center space-x-1"
                                    title="{{ __('Another timer is running') }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('Start') }}</span>
                            </button>
                        @else
                            <form action="{{ route('turbo.running-timer-session.store') }}" method="POST" data-turbo-frame="timer-widget">
                                @csrf
                                <input type="hidden" name="client_id" value="{{ $entry->client_id }}">
                                <input type="hidden" name="project_id" value="{{ $entry->project_id }}">
                                <button type="submit"
                                        class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center space-x-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ __('Start') }}</span>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('turbo.time-entries.edit', $entry) }}"
                           class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center space-x-1"
                           data-turbo-frame="recent-entry-{{ $entry->id }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>{{ __('Edit') }}</span>
                        </a>

                        <a href="{{ route('time-entries.destroy', ['timeEntry' => $entry, 'is_recent' => true]) }}"
                           data-turbo-method="delete"
                           data-turbo-confirm="{{ __('Are you sure you want to delete this time entry?') }}"
                           class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center space-x-1"
                           data-turbo-frame="_top">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>{{ __('Delete') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</turbo-frame>
