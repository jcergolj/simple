
<x-layouts.app :title="__('Time Tracking Dashboard')">
    <div class="space-y-8">
        <!-- Header -->
        <div class="text-center py-4 px-4 sm:px-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ __('Time Tracking Dashboard') }}</h1>
            <p class="text-gray-600 text-sm sm:text-base">{{ __('Track your time and manage your projects') }}</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 px-4 sm:px-0">
            <turbo-frame id="weekly-hours">
                @include('dashboard.weekly-hours', ['totalHours' => $totalHours])
            </turbo-frame>

            <turbo-frame id="weekly-earnings">
                @include('dashboard.weekly-earnings', ['totalAmount' => $totalAmount])
            </turbo-frame>
        </div>

        <!-- Timer Section -->
        <div class="bg-white rounded-lg border border-gray-200 mx-4 sm:mx-0">
            @if($runningTimer)
                @include('turbo::timer-sessions.running', ['runningTimer' => $runningTimer])
            @else
                <x-timer-widget
                    :preselected-client-id="$preselectedClientId"
                    :preselected-client-name="$preselectedClientName"
                    :preselected-project-id="$preselectedProjectId"
                    :preselected-project-name="$preselectedProjectName"
                    :running-timer="$runningTimer" />
            @endif
        </div>

        <!-- Recent Entries -->
        <div class="px-4 sm:px-0">
            <turbo-frame id="recent-entries">
                @include('dashboard.recent-entries', ['recentEntries' => $recentEntries])
            </turbo-frame>
        </div>
    </div>
</x-layouts.app>
