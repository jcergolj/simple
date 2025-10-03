<turbo-stream action="replace" target="project-{{ $project->id }}">
    <template>
        <turbo-frame id="project-{{ $project->id }}">
            <div class="p-4 sm:p-6 border-b border-base-200 last:border-b-0 hover:bg-base-50 transition-colors">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <!-- Main content area -->
                    <div class="flex-1 space-y-3">
                        <!-- Project name at the top -->
                        <div>
                            <h4 class="font-semibold text-base sm:text-lg">{{ $project->name }}</h4>
                        </div>

                        <!-- Client info and metadata -->
                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                            <div class="badge badge-info badge-outline text-xs sm:text-sm w-full sm:w-auto justify-center sm:justify-start">
                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ $project->client->name }}
                            </div>

                            @if($project->hourly_rate)
                                <div class="badge badge-secondary badge-outline text-xs sm:text-sm w-full sm:w-auto justify-center sm:justify-start">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $project->hourly_rate->formatted() }}/hr
                                </div>
                            @elseif($project->client->hourly_rate)
                                <div class="badge badge-ghost text-xs sm:text-sm w-full sm:w-auto justify-center sm:justify-start">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $project->client->hourly_rate->formatted() }}/hr ({{ __('client') }})
                                </div>
                            @else
                                <div class="badge badge-ghost text-xs sm:text-sm w-full sm:w-auto justify-center sm:justify-start">
                                    {{ __('No rate set') }}
                                </div>
                            @endif

                            @if($project->time_entries_count > 0)
                                <div class="badge badge-success badge-outline text-xs sm:text-sm w-full sm:w-auto justify-center sm:justify-start">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $project->time_entries_count }} {{ Str::plural('entry', $project->time_entries_count) }}
                                </div>
                            @endif
                        </div>

                        @if($project->description)
                            <p class="text-base-content/70 text-sm">{{ $project->description }}</p>
                        @endif
                    </div>

                    <!-- Edit/Delete buttons - on the right for wider screens -->
                    <div class="flex items-center space-x-2 pt-2 border-t border-base-200 lg:border-t-0 lg:pt-0 lg:flex-shrink-0">
                        <a href="{{ route('turbo.projects.edit', $project) }}" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors inline-flex items-center space-x-1">
                            <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>{{ __('Edit') }}</span>
                        </a>

                        <a href="{{ route('projects.destroy', $project) }}"
                           class="bg-red-100 hover:bg-red-200 text-red-700 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors inline-flex items-center space-x-1"
                           data-turbo-method="delete"
                           data-turbo-confirm="Are you sure you want to delete this project? This will also delete all associated time entries."
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
    </template>
</turbo-stream>
