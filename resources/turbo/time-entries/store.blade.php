<turbo-stream action="replace" target="time-entry-create-form">
    <template>
        <x-time-entry-create-button />
    </template>
</turbo-stream>

<turbo-stream action="replace" target="time-entries-lists">
    <template>
        <turbo-frame id="time-entries-lists">
            @include('turbo::time-entries.list', ['timeEntries' => $timeEntries])
        </turbo-frame>
    </template>
</turbo-stream>
