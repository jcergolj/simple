<turbo-stream action="replace" target="timer-widget">
    <template>
        @include('turbo::timer-sessions.running', ['runningTimer' => $runningTimer])
    </template>
</turbo-stream>

<turbo-stream action="replace" target="recent-entries">
    <template>
        @include('dashboard.recent-entries', compact('recentEntries'))
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
