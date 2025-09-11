
<x-layouts.app :title="__('Time Tracking Dashboard')">
    <div class="space-y-8">
        <!-- Header -->
        <div class="text-center py-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Time Tracking Dashboard') }}</h1>
            <p class="text-gray-600">{{ __('Track your time and manage your projects') }}</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <div class="text-sm text-gray-500 mb-1">{{ __('Total Hours This Week') }}</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($totalHours, 1) }}h</div>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <div class="text-sm text-gray-500 mb-1">{{ __('Weekly Earnings') }}</div>
                <div class="text-2xl font-bold text-gray-900">${{ number_format($billableAmount, 2) }}</div>
            </div>
        </div>

        <!-- Timer Section -->
        <div class="bg-white rounded-lg border border-gray-200">
            @if($runningTimer)
                @include('timer-sessions.running', ['runningTimer' => $runningTimer])
            @else
                <turbo-frame id="timer-widget" class="contents">
                    <div class="p-8" data-controller="keyboard-shortcuts">
                        <h2 class="text-xl font-medium text-gray-900 text-center mb-6">{{ __('Start New Timer') }}</h2>

                        <form action="{{ route('timer-session.store') }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end max-w-2xl mx-auto" data-turbo-frame="timer-widget">
                            @csrf

                            <div class="flex-1 w-full">
                                <x-form.search-clients :client-id="$preselectedClientId" :client-name="$preselectedClientName" />
                            </div>

                            <div class="flex-1 w-full">
                                <x-form.search-projects :project-id="$preselectedProjectId" :project-name="$preselectedProjectName" />
                            </div>

                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-full transition-colors" data-keyboard-shortcuts-target="startButton" title="{{ __('Start Timer') }} (Ctrl+Shift+S)">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </button>
                        </form>
                        <x-form.error for="client_id" />
                        <x-form.error for="project_id" />
                    </div>
                </turbo-frame>
            @endif
        </div>

        <!-- Recent Entries -->
        <turbo-frame id="recent-entries">
            @include('dashboard.recent-entries', ['recentEntries' => $recentEntries])
        </turbo-frame>
    </div>

</x-layouts.app>
