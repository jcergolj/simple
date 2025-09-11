<div class="card bg-base-100 shadow-xl">
    <div class="card-body p-0">
        <div class="p-6 border-b border-base-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title text-xl">{{ __('Your Clients') }}</h3>
                    <p class="text-base-content/70">{{ $clients->total() }} {{ Str::plural(__('client'), $clients->total()) }} {{ __('total') }}</p>
                </div>
                @if($clients->hasPages())
                    <div class="text-sm text-base-content/70">
                        {{ __('Showing') }} {{ $clients->firstItem() }}-{{ $clients->lastItem() }} {{ __('of') }} {{ $clients->total() }}
                    </div>
                @endif
            </div>
        </div>

        @forelse($clients as $client)
            <div id="client-{{ $client->id }}" class="p-6 border-b border-base-200 last:border-b-0 hover:bg-base-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <!-- Client Avatar -->
                            <div class="avatar placeholder">
                                <div class="bg-primary text-primary-content rounded-full w-12 h-12">
                                    <span class="text-lg font-bold">{{ strtoupper(substr($client->name, 0, 2)) }}</span>
                                </div>
                            </div>

                            <div class="flex-1">
                                <h4 class="font-semibold text-lg">{{ $client->name }}</h4>
                                <div class="flex items-center space-x-4 mt-1">
                                    @if($client->hourly_rate)
                                        <div class="badge badge-secondary badge-outline">
                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            {{ $client->hourly_rate->formatted() }}/hr
                                        </div>
                                    @else
                                        <div class="badge badge-ghost">
                                            No rate set
                                        </div>
                                    @endif

                                    @if($client->projects_count > 0)
                                        <div class="badge badge-info badge-outline">
                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                            {{ $client->projects_count }} {{ Str::plural('project', $client->projects_count) }}
                                        </div>
                                    @endif

                                    @if($client->time_entries_count > 0)
                                        <div class="badge badge-success badge-outline">
                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $client->time_entries_count }} {{ Str::plural('entry', $client->time_entries_count) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('turbo.clients.edit', $client) }}" class="btn btn-ghost btn-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            {{ __('Edit') }}
                        </a>

                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </div>
                            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow-xl border border-base-200">
                                <li>
                                    <a href="{{ route('clients.destroy', $client) }}" class="text-primary" data-turbo-method="delete" data-turbo-confirm="Are you sure you want to delete this client?" >
                                        {{ __('Delete Client') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 mx-auto bg-base-200 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">{{ __('No clients yet') }}</h3>
                <p class="text-base-content/70 mb-4">Get started by adding your first client above.</p>
                <a href="{{ route('turbo.clients.create') }}" class="btn btn-primary">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('Add Your First Client') }}
                </a>
            </div>
        @endforelse

        @if($clients->hasPages())
            <div class="p-6 border-t border-base-200 bg-base-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-base-content/70">
                        {{ __('Showing') }} {{ $clients->firstItem() }} {{ __('to') }} {{ $clients->lastItem() }} {{ __('of') }} {{ $clients->total() }} {{ __('results') }}
                    </div>
                    <div class="join">
                        @if($clients->onFirstPage())
                            <button class="join-item btn btn-disabled">«</button>
                        @else
                            <a href="{{ $clients->previousPageUrl() }}" class="join-item btn">«</a>
                        @endif

                        <button class="join-item btn btn-active">{{ __('Page') }} {{ $clients->currentPage() }}</button>

                        @if($clients->hasMorePages())
                            <a href="{{ $clients->nextPageUrl() }}" class="join-item btn">»</a>
                        @else
                            <button class="join-item btn btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
