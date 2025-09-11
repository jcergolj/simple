<turbo-stream action="replace" target="recent-entry-{{ $timeEntry->id }}">
    <template>
        <x-recent-time-entry :entry="$timeEntry" :running-timer="$runningTimer" />
    </template>
</turbo-stream>

<turbo-stream action="replace" target="weekly-hours">
    <template>
        @include('dashboard.weekly-hours', compact('totalHours'))
    </template>
</turbo-stream>

<turbo-stream action="replace" target="weekly-earnings">
    <template>
        @include('dashboard.weekly-earnings', compact('totalAmount', 'weeklyEarnings'))
    </template>
</turbo-stream>
