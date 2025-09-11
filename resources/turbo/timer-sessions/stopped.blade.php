<turbo-stream action="replace" target="timer-widget">
    <template>
        @if(isset($runningTimer))
            <turbo-frame id="timer-widget" class="contents">
                <div class="p-8"
                     data-controller="timer keyboard-shortcuts"
                     data-timer-running-value="true"
                     data-timer-start-time-value="{{ $runningTimer->start_time->timestamp }}">
                    <div class="text-center space-y-6">
                            <!-- Session Status -->
                            <div class="text-lg font-medium text-gray-700">{{ __('Session in progress') }}</div>

                            <!-- Running Time Display -->
                            <div class="text-6xl font-mono font-bold text-black" data-timer-target="display">00:00:00</div>

                            <!-- Client and Project Info - Side by Side -->
                            @php $entry = $runningTimer @endphp
                            <div class="flex justify-center items-center gap-4">
                                @if($entry->client)
                                    <div class="inline-flex items-center px-6 py-3 rounded-full text-2xl bg-green-100 text-black border border-green-200">
                                        <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="font-bold">{{ $entry->client->name }}</span>
                                    </div>
                                @endif

                                @if($entry->project)
                                    <div class="inline-flex items-center px-6 py-3 rounded-full text-2xl bg-blue-100 text-black border border-blue-200">
                                        <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        <span class="font-bold">{{ $entry->project->name }}</span>
                                    </div>
                                @endif

                                @if(!$entry->client && !$entry->project)
                                    <div class="inline-flex items-center px-6 py-3 rounded-full text-2xl bg-gray-100 text-black border border-gray-200">
                                        <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="font-bold">{{ __('No client or project assigned') }}</span>
                                    </div>
                                @endif
                            </div>

                            @if(isset($isEditUpdate) && $isEditUpdate)
                                <!-- Success Message -->
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-green-800 font-medium">{{ __('Timer session updated successfully!') }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Control Buttons -->
                            <div class="flex flex-col sm:flex-row justify-center gap-4 pt-4">
                                <a href="{{ route('turbo.running-timer-session.edit') }}" 
                                   data-turbo-frame="timer-widget"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center space-x-3"
                                   title="{{ __('Edit Timer') }}">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span class="text-lg">{{ __('Edit') }}</span>
                                </a>

                                <a href="{{ route('turbo.running-timer-session.completion') }}"
                                   data-turbo-method="post" 
                                   class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 flex items-center justify-center space-x-3" 
                                   data-keyboard-shortcuts-target="stopButton"
                                   title="{{ __('Stop Timer') }} (Ctrl+Shift+T)">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <rect x="6" y="6" width="12" height="12" rx="1"></rect>
                                    </svg>
                                    <span class="text-lg">{{ __('Stop Timer') }}</span>
                                    <span class="text-red-200 text-sm">(Ctrl+Shift+T)</span>
                                </a>

                                <a href="{{ route('turbo.running-timer-session.destroy') }}"
                                   data-turbo-method="delete" 
                                   class="border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 hover:bg-gray-50 px-8 py-4 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 flex items-center justify-center space-x-3" 
                                   data-keyboard-shortcuts-target="cancelButton" 
                                   data-turbo-confirm="{{ __('Are you sure you want to cancel this timer? All progress will be lost.') }}"
                                   title="{{ __('Cancel Timer') }}">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="text-lg">{{ __('Cancel Timer') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </turbo-frame>
        @else
            <x-timer-widget 
                :preselected-client-id="$lastEntry?->client_id" 
                :preselected-client-name="$lastEntry?->client?->name"
                :preselected-project-id="$lastEntry?->project_id" 
                :preselected-project-name="$lastEntry?->project?->name"
                :running-timer="$runningTimer ?? null" />
        @endif
    </template>
</turbo-stream>

<turbo-stream action="replace" target="recent-entries">
    <template>
        @include('dashboard.recent-entries', ['recentEntries' => $recentEntries, 'runningTimer' => $runningTimer ?? null])
    </template>
</turbo-stream>

<turbo-stream action="replace" target="weekly-hours">
    <template>
        @include('dashboard.weekly-hours', compact('totalHours'))
    </template>
</turbo-stream>

<turbo-stream action="replace" target="weekly-earnings">
    <template>
        @include('dashboard.weekly-earnings', ['totalAmount' => $totalAmount, 'weeklyEarnings' => $weeklyEarnings])
    </template>
</turbo-stream>

@if($lastEntry && $lastEntry->end_time)
<turbo-stream action="replace" target="recent-entry-{{ $lastEntry->id }}">
    <template>
        <x-recent-time-entry :entry="$lastEntry" />
    </template>
</turbo-stream>
@endif