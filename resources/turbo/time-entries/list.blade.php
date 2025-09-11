<div class="card bg-base-100 shadow-xl">
    <div class="card-body p-0">
        <div class="p-4 sm:p-6 border-b border-base-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                <div>
                    <h3 class="card-title text-lg sm:text-xl">{{ __('Your Time Entries') }}</h3>
                    <p class="text-base-content/70 text-sm">{{ $timeEntries->total() }} {{ Str::plural(__('entry'), $timeEntries->total()) }} {{ __('total') }}</p>
                </div>
                @if($timeEntries->hasPages())
                    <div class="text-xs sm:text-sm text-base-content/70">
                        {{ __('Showing') }} {{ $timeEntries->firstItem() }}-{{ $timeEntries->lastItem() }} {{ __('of') }} {{ $timeEntries->total() }}
                    </div>
                @endif
            </div>
        </div>

        @forelse($timeEntries as $timeEntry)
            <turbo-frame id="time-entry-{{ $timeEntry->id }}">
                <div class="p-4 sm:p-6 border-b border-base-200 last:border-b-0 hover:bg-base-50 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <!-- Main content area -->
                        <div class="flex-1 space-y-3">
                            <!-- Date and Duration at the top -->
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                                <h4 class="text-base sm:text-lg font-bold"><x-user-date :date="$timeEntry->start_time" /></h4>
                                @if($timeEntry->duration)
                                    <div class="badge badge-primary badge-outline text-xs sm:text-sm">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $timeEntry->getFormattedDuration() }}
                                    </div>
                                @else
                                    <div class="badge badge-warning badge-outline text-xs sm:text-sm">
                                        <span class="w-2 h-2 bg-warning rounded-full mr-1 animate-pulse"></span>
                                        {{ __('Running') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Time Period -->
                            <div class="flex items-center text-base-content/70">
                                <svg class="h-3 w-3 sm:h-4 sm:w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs sm:text-sm">
                                    <x-user-time :time="$timeEntry->start_time" />
                                    @if($timeEntry->end_time)
                                        - <x-user-time :time="$timeEntry->end_time" />
                                    @endif
                                </span>
                            </div>

                            <!-- Client, project and rate info -->
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                                @if($timeEntry->client)
                                    <div class="badge badge-info badge-outline text-xs sm:text-sm w-full sm:w-auto justify-center sm:justify-start">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ $timeEntry->client->name }}
                                    </div>
                                @endif

                                @if($timeEntry->project)
                                    <div class="badge badge-accent badge-outline text-xs sm:text-sm w-full sm:w-auto justify-center sm:justify-start">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        {{ $timeEntry->project->name }}
                                    </div>
                                @endif

                                @if($timeEntry->getEffectiveHourlyRate())
                                    <div class="badge badge-secondary badge-outline text-xs sm:text-sm w-full sm:w-auto justify-center sm:justify-start">
                                        {{ $timeEntry->getEffectiveHourlyRate()->formatted() }}/hr
                                    </div>
                                @endif

                                @if($timeEntry->calculateEarnings())
                                    <div class="badge badge-success badge-outline text-xs sm:text-sm w-full sm:w-auto justify-center sm:justify-start">
                                        {{ $timeEntry->calculateEarnings()->formatted() }} {{ __('earned') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Notes -->
                            @if($timeEntry->notes)
                                <div class="bg-base-200 rounded-lg p-3 sm:p-4">
                                    <div class="flex items-start space-x-2">
                                        <svg class="h-3 w-3 sm:h-4 sm:w-4 mt-1 text-base-content/50 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                        <p class="text-xs sm:text-sm text-base-content/70 break-words">{{ $timeEntry->notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Edit/Delete buttons - on the right for wider screens -->
                        <div class="flex items-center space-x-2 pt-2 border-t border-base-200 lg:border-t-0 lg:pt-0 lg:flex-shrink-0">
                            <a href="{{ route('turbo.time-entries.edit', $timeEntry) }}"
                               class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors inline-flex items-center space-x-1">
                                <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span>{{ __('Edit') }}</span>
                            </a>

                            <a href="{{ route('time-entries.destroy', $timeEntry) }}"
                               class="bg-red-100 hover:bg-red-200 text-red-700 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors inline-flex items-center space-x-1"
                               data-turbo-method="delete"
                               data-turbo-confirm="{{ __('Are you sure you want to delete this time entry?') }}"
                               data-turbo-frame="_top">
                                <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>{{ __('Delete') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </turbo-frame>
        @empty
            <div class="p-8 sm:p-12 text-center">
                <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto bg-base-200 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-6 w-6 sm:h-8 sm:w-8 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-semibold mb-2">{{ __('No time entries yet') }}</h3>
                <p class="text-base-content/70 mb-4 text-sm">{{ __('Get started by tracking your first time entry above.') }}</p>
                <a href="{{ route('turbo.time-entries.create') }}"
                   class="btn btn-primary btn-sm sm:btn-md"
                   data-turbo-frame="time-entry-create-form">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('Add Your First Time Entry') }}
                </a>
            </div>
        @endforelse

        @if($timeEntries->hasPages())
            <div class="p-4 sm:p-6 border-t border-base-200 bg-base-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <div class="text-xs sm:text-sm text-base-content/70 text-center sm:text-left">
                        {{ __('Showing') }} {{ $timeEntries->firstItem() }} {{ __('to') }} {{ $timeEntries->lastItem() }} {{ __('of') }} {{ $timeEntries->total() }} {{ __('results') }}
                    </div>
                    <nav class="join justify-center sm:justify-end flex-wrap gap-1" aria-label="Pagination">
                        {{-- Previous Button --}}
                        @if($timeEntries->onFirstPage())
                            <button class="join-item btn btn-disabled btn-sm" disabled>
                                <span class="hidden sm:inline">{{ __('Previous') }}</span>
                                <span class="sm:hidden">«</span>
                            </button>
                        @else
                            <a href="{{ $timeEntries->previousPageUrl() }}" class="join-item btn btn-sm hover:btn-primary">
                                <span class="hidden sm:inline">{{ __('Previous') }}</span>
                                <span class="sm:hidden">«</span>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $timeEntries->currentPage();
                            $lastPage = $timeEntries->lastPage();
                            $showPages = [];

                            // Always show first page
                            if ($lastPage > 1) {
                                $showPages[] = 1;
                            }

                            // Show pages around current page
                            for ($i = max(2, $currentPage - 1); $i <= min($lastPage - 1, $currentPage + 1); $i++) {
                                $showPages[] = $i;
                            }

                            // Always show last page
                            if ($lastPage > 1) {
                                $showPages[] = $lastPage;
                            }

                            $showPages = array_unique($showPages);
                            sort($showPages);
                        @endphp

                        @foreach($showPages as $index => $page)
                            {{-- Show ellipsis if there's a gap --}}
                            @if($index > 0 && $page - $showPages[$index - 1] > 1)
                                <button class="join-item btn btn-disabled btn-sm" disabled>...</button>
                            @endif

                            {{-- Page number button --}}
                            @if($page == $currentPage)
                                <button class="join-item btn btn-active btn-primary btn-sm">{{ $page }}</button>
                            @else
                                <a href="{{ $timeEntries->url($page) }}" class="join-item btn btn-sm hover:btn-primary">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Button --}}
                        @if($timeEntries->hasMorePages())
                            <a href="{{ $timeEntries->nextPageUrl() }}" class="join-item btn btn-sm hover:btn-primary">
                                <span class="hidden sm:inline">{{ __('Next') }}</span>
                                <span class="sm:hidden">»</span>
                            </a>
                        @else
                            <button class="join-item btn btn-disabled btn-sm" disabled>
                                <span class="hidden sm:inline">{{ __('Next') }}</span>
                                <span class="sm:hidden">»</span>
                            </button>
                        @endif
                    </nav>
                </div>
            </div>
        @endif
    </div>
</div>
