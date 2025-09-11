<turbo-frame id="timer-widget" class="contents">
    <div class="card bg-base-100 shadow-xl"
         data-controller="timer keyboard-shortcuts"
         data-timer-running-value="true"
         data-timer-start-time-value="{{ $runningTimer->start_time->timestamp ?? $timeEntry->start_time->timestamp }}">
        <div class="card-body">
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-error/10 rounded-full flex items-center justify-center">
                        <svg class="h-8 w-8 text-error animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="card-title justify-center mb-2">{{ __('Timer Running') }}</h3>

                <!-- Live Timer Display -->
                <div class="text-4xl font-mono font-bold text-error mb-4" data-timer-target="display">00:00:00</div>

                <!-- Current Session Info -->
                @php $entry = $runningTimer ?? $timeEntry @endphp
                @if($entry->client || $entry->project)
                    <div class="mb-4 space-y-1">
                        @if($entry->client)
                            <div class="badge badge-info badge-lg">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ $entry->client->name }}
                            </div>
                        @endif
                        @if($entry->project)
                            <div class="badge badge-secondary badge-lg">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                {{ $entry->project->name }}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Timer Controls -->
                <div class="flex gap-2 justify-center">
                    <!-- Stop Timer -->
                    <form action="{{ route('timer-session.update') }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-error btn-wide" data-keyboard-shortcuts-target="stopButton">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z"></path>
                            </svg>
                            {{ __('Stop Timer') }}
                            <span class="text-xs opacity-70">(Ctrl+Shift+T)</span>
                        </button>
                    </form>

                    <!-- Cancel Timer -->
                    <form action="{{ route('timer-session.destroy') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline btn-error"
                                onclick="return confirm('{{ __('Are you sure you want to cancel this timer? All progress will be lost.') }}')">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            {{ __('Cancel') }}
                        </button>
                    </form>
                </div>

                <!-- Timer Started Info -->
                <div class="mt-4 text-sm text-base-content/70">
                    {{ __('Started at') }}: {{ $entry->start_time->format('g:i A') }}
                </div>
            </div>
        </div>
    </div>
</turbo-frame>

<script>
// Update page title with running timer
document.addEventListener('DOMContentLoaded', function() {
    const timerDisplay = document.querySelector('[data-timer-target="display"]');
    if (timerDisplay) {
        const originalTitle = document.title;
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || mutation.type === 'characterData') {
                    const timeText = timerDisplay.textContent.trim();
                    document.title = `⏱️ ${timeText} - ${originalTitle}`;
                }
            });
        });
        observer.observe(timerDisplay, { childList: true, characterData: true, subtree: true });
    }
});
</script>