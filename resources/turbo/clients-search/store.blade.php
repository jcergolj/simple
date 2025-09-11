<div class="p-3 bg-success/10 border border-success rounded-lg">
    <div class="flex items-center space-x-2">
        <svg class="h-5 w-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="text-success font-medium">{{ __('Client created:') }}</span>
        <span class="font-semibold">{{ $client->name }}</span>
    </div>
    <input type="hidden" name="client_id" value="{{ $client->id }}">
</div>
