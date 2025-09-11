@if($projects->count() > 0)
    @foreach($projects as $project)
        <a href="#"
           class="block px-4 py-2 text-sm hover:bg-base-200 cursor-pointer border-b border-base-300 last:border-b-0"
           data-id="{{ $project->id }}">
            <div class="flex items-center justify-between">
                <div>
                    <span class="font-medium">{{ $project->name }}</span>
                    @if($project->client)
                        <span class="text-base-content/70 ml-2">({{ $project->client->name }})</span>
                    @endif
                </div>
                @if($project->hourly_rate)
                    <span class="badge badge-info badge-sm">
                        {{ $project->hourly_rate->formatted() }}/hr
                    </span>
                @endif
            </div>
        </a>
    @endforeach
@else
    <!-- Project creation fields - NO FORM, just div for styling -->
    <div class="p-4 space-y-4 border border-base-300 rounded-lg" data-search-projects-target="createForm">
        <div class="text-sm text-base-content/70 mb-4">
            {{ __('No projects found. Create a new one?') }}
        </div>

        <!-- Project Name (pre-filled with search query) -->
        <div class="form-control">
            <label class="label" for="new_project_name">
                <span class="label-text font-semibold">{{ __('Project Name') }}</span>
                <span class="label-text-alt text-error">*</span>
            </label>
            <div class="relative">
                <input type="text" id="new_project_name" name="new_project_name" value="{{ request('q') ?? '' }}"
                    placeholder="{{ __('Enter project name') }}"
                    class="input input-bordered w-full pl-10"
                    data-search-projects-target="newProjectName" />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Client Selection (always preselected and non-editable) -->
        @if($clientId)
            @php
                $selectedClient = \App\Models\Client::find($clientId);
            @endphp
            @if($selectedClient)
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Client') }}</span>
                    </label>
                    <div class="input input-bordered flex items-center bg-base-200 cursor-not-allowed">
                        <svg class="h-5 w-5 text-base-content/50 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ $selectedClient->name }}
                        <span class="ml-auto text-xs text-base-content/50">{{ __('Pre-selected') }}</span>
                    </div>
                    <input type="hidden" data-search-projects-target="newProjectClientId" value="{{ $clientId }}">
                </div>
            @endif
        @else
            <!-- Fallback if no client is preselected -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold text-error">{{ __('Error') }}</span>
                </label>
                <div class="alert alert-error">
                    <span>{{ __('No client selected. Please select a client first.') }}</span>
                </div>
            </div>
        @endif

        <!-- Hourly Rate Override -->
        <div class="form-control">
            <label class="label" for="new_project_hourly_rate_amount">
                <span class="label-text font-semibold">{{ __('Project Hourly Rate') }}</span>
                <span class="label-text-alt text-base-content/50">{{ __('Override client rate (Optional)') }}</span>
            </label>
            <x-form.money-input
                name="new_project_hourly_rate_amount"
                currency-name="new_project_hourly_rate_currency"
                placeholder="0.00"
                id="new_project_hourly_rate_amount"
                data-search-projects-target="newProjectRate"
                currency-attributes='data-search-projects-target="newProjectCurrency"'
            />
            <div class="label">
                <span class="label-text-alt text-base-content/50">{{ __('Leave empty to use client\'s default rate') }}</span>
            </div>
            <x-form.error for="new_project_hourly_rate_amount" />
            <x-form.error for="new_project_hourly_rate_currency" />
        </div>

        <div class="flex justify-end">
            <button type="button"
                    class="btn btn-primary btn-base"
                    {{ !$clientId ? 'disabled' : '' }}
                    data-action="click->search-projects#createProjectFromFields"
                    data-create-url="{{ route('turbo.projects.store') }}">
                {{ __('Create Project') }}
            </button>
        </div>
    </div>
@endif
