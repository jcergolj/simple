
<x-layouts.app :title="__('Time Tracking Dashboard')">
    <div class="space-y-8">
        <!-- Header -->
        <div class="text-center py-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Time Tracking Dashboard') }}</h1>
            <p class="text-gray-600">{{ __('Track your time and manage your projects') }}</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <turbo-frame id="weekly-hours">
                @include('dashboard.weekly-hours', ['totalHours' => $totalHours])
            </turbo-frame>

            <turbo-frame id="weekly-earnings">
                @include('dashboard.weekly-earnings', ['totalAmount' => $totalAmount])
            </turbo-frame>
        </div>

        <!-- Timer Section -->
        <div class="bg-white rounded-lg border border-gray-200">
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
        <turbo-frame id="recent-entries">
            @include('dashboard.recent-entries', ['recentEntries' => $recentEntries])
        </turbo-frame>
    </div>
</x-layouts.app>
