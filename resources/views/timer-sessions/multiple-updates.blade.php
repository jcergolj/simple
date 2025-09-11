<turbo-stream action="replace" target="timer-widget">
    <template>
        <turbo-frame id="timer-widget" class="contents">
            <div class="card bg-base-100 shadow-xl" data-controller="keyboard-shortcuts">
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-center mb-6">{{ __('Start New Timer') }}</h2>

                        <form action="{{ route('timer-session.store') }}" method="POST" class="flex gap-2 items-end" data-turbo-frame="timer-widget">
                            @csrf

                            <div class="flex-1">
                                <x-form.search-clients :client-id="$lastEntry?->client_id" :client-name="$lastEntry?->client?->name" />
                            </div>

                            <div class="flex-1">
                                <x-form.search-projects :project-id="$lastEntry?->project_id" :project-name="$lastEntry?->project?->name" />
                            </div>

                            <button type="submit" class="btn btn-success btn-circle btn-lg" data-keyboard-shortcuts-target="startButton" title="{{ __('Start Timer') }} (Ctrl+Shift+S)">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </button>
                        </form>

                        
                    </div>
                </div>
            </div>
        </turbo-frame>
    </template>
</turbo-stream>

<turbo-stream action="replace" target="recent-entries">
    <template>
        @include('dashboard.recent-entries', compact('recentEntries'))
    </template>
</turbo-stream>
