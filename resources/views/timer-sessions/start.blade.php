<turbo-frame id="timer-widget" class="contents">
    <div class="card bg-base-100 shadow-xl" data-controller="keyboard-shortcuts">
        <div class="card-body">
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-success/10 rounded-full flex items-center justify-center">
                        <svg class="h-8 w-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="card-title justify-center mb-4">{{ __('Start New Timer') }}</h3>

                <form action="{{ route('timer-session.store') }}" method="POST" class="space-y-4" data-turbo-frame="timer-widget">
                    @csrf

                    <!-- Client Selection -->
                    <select name="client_id" class="select select-bordered w-full"
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
                    <select name="project_id" class="select select-bordered w-full"
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

                    <!-- Start Button -->
                    <button type="submit" class="btn btn-success btn-wide" data-keyboard-shortcuts-target="startButton">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('Start Timer') }}
                        <span class="text-xs opacity-70">(Ctrl+Shift+S)</span>
                    </button>
                </form>

                <x-form.error for="client_id" />
                <x-form.error for="project_id" />
            </div>
        </div>
    </div>
</turbo-frame>
