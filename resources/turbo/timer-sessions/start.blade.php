<turbo-frame id="timer-widget" class="contents">
    <div class="p-8" data-controller="keyboard-shortcuts">
        <div class="text-center space-y-4">
                <!-- Status -->
                <div class="text-sm font-medium text-base-content/70">{{ __('Ready to start') }}</div>

                <!-- Timer Display (Static) -->
                <div class="text-4xl font-mono font-bold text-base-content/30">00:00:00</div>

                <!-- Start Form -->
                <form action="{{ route('turbo.running-timer-session.store') }}" method="POST" class="space-y-3" data-turbo-frame="timer-widget">
                    @csrf

                    <div class="grid grid-cols-1 gap-2">
                        <!-- Client Selection -->
                        <select name="client_id" class="select select-bordered select-sm w-full"
                                data-controller="client-prefill"
                                data-client-prefill-last-value="{{ $lastEntry?->client_id }}">
                            <option value="">{{ __('Select Client') }}</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ ($lastEntry?->client_id == $client->id) ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Project Selection -->
                        <select name="project_id" class="select select-bordered select-sm w-full"
                                data-controller="project-prefill"
                                data-project-prefill-last-value="{{ $lastEntry?->project_id }}">
                            <option value="">{{ __('Select Project') }}</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}"
                                    {{ ($lastEntry?->project_id == $project->id) ? 'selected' : '' }}>
                                    {{ $project->client->name }} - {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Start Button -->
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-lg font-medium flex items-center justify-center space-x-3 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" data-keyboard-shortcuts-target="startButton">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ __('Start Timer') }}</span>
                        <span class="text-green-200 text-sm">(Ctrl+Shift+S)</span>
                    </button>
                </form>

                <x-form.error for="client_id" />
                <x-form.error for="project_id" />
            </div>
        </div>
    </div>
</turbo-frame>
