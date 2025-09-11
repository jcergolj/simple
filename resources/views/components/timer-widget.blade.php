@props([
    'preselectedClientId' => null,
    'preselectedClientName' => null,
    'preselectedProjectId' => null,
    'preselectedProjectName' => null,
    'runningTimer' => null
])

<turbo-frame id="timer-widget" class="contents">
    @if($runningTimer)
        @include('turbo::timer-sessions.running', ['runningTimer' => $runningTimer])
    @else
        <div class="p-4 sm:p-8" data-controller="keyboard-shortcuts">
            <div class="text-center">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">{{ __('Start New Timer') }}</h2>

                    <form action="{{ route('turbo.running-timer-session.store') }}" method="POST" data-turbo-frame="timer-widget">
                        @csrf

                        <!-- Client and Project Selection Row -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                            <div class="w-full">
                                <x-form.search-clients :client-id="$preselectedClientId" :client-name="$preselectedClientName" />
                            </div>

                            <div class="w-full">
                                <x-form.search-projects :project-id="$preselectedProjectId" :project-name="$preselectedProjectName" />
                            </div>
                        </div>

                        <!-- Start Timer Button Row -->
                        <div class="flex justify-center">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-medium flex items-center justify-center space-x-2 sm:space-x-3 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" data-keyboard-shortcuts-target="startButton" title="{{ __('Start Timer') }} (Ctrl+Shift+S)">
                                <svg class="h-8 w-8 sm:h-12 sm:w-12" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                <span class="text-sm sm:text-base">{{ __('Start Timer') }}</span>
                                <span class="text-green-200 text-xs sm:text-sm hidden sm:inline">(Ctrl+Shift+S)</span>
                            </button>
                        </div>

                        <!-- Error Messages -->
                        <div class="mt-4 space-y-2">
                            <x-form.error for="client_id" />
                            <x-form.error for="project_id" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</turbo-frame>
