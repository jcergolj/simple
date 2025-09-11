<div class="p-3 bg-success/10 border border-success rounded-lg">
    <div class="flex items-center space-x-2">
        <svg class="h-5 w-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="text-success font-medium">{{ __('Project created:') }}</span>
        <span class="font-semibold">{{ $project->name }}</span>
        @if($project->client)
            <span class="text-base-content/70">({{ $project->client->name }})</span>
        @endif
        @if($project->hourly_rate)
            <span class="badge badge-info badge-sm">
                {{ $project->hourly_rate->formatted() }}/hr
            </span>
        @endif
    </div>
    <input type="hidden" name="project_id" value="{{ $project->id }}">
</div>