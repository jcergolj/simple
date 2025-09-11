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
                        {{ App\Enums\Currency::from($project->hourly_rate->currency)->symbol() }}{{ number_format($project->hourly_rate->amount, 2) }}/hr
                    </span>
                @endif
            </div>
        </a>
    @endforeach
@else
    <!-- Project creation fields inside the existing timer form -->
    <fieldset class="p-4 space-y-4 border border-base-300 rounded-lg">
        <legend class="text-sm text-base-content/70 px-2">
            {{ __('No projects found. Create a new one?') }}
        </legend>

        <!-- Project Name (pre-filled with search query) -->
        <div class="form-control">
            <label class="label" for="project_name">
                <span class="label-text font-semibold">{{ __('Project Name') }}</span>
                <span class="label-text-alt text-error">*</span>
            </label>
            <div class="relative">
                <input type="text" id="project_name" name="project_name" value="{{ request('q') ?? old('project_name') }}"
                    placeholder="{{ __('Enter project name') }}"
                    class="input input-bordered w-full pl-10 @error('project_name') input-error @enderror" />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
            <x-form.error for="project_name" />
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
                    <input type="hidden" name="project_client_id" value="{{ $clientId }}">
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
            <label class="label" for="project_hourly_rate_amount">
                <span class="label-text font-semibold">{{ __('Project Hourly Rate') }}</span>
                <span class="label-text-alt text-base-content/50">{{ __('Override client rate (Optional)') }}</span>
            </label>
            <div class="join w-full">
                <div class="relative flex-1">
                    <input type="number" id="project_hourly_rate_amount" name="project_hourly_rate_amount"
                        value="{{ old('project_hourly_rate_amount') }}" placeholder="0.00"
                        class="input input-bordered join-item w-full pl-10 @error('project_hourly_rate_amount') input-error @enderror"
                        step="0.01" min="0" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <select name="project_hourly_rate_currency" class="select select-bordered join-item @error('project_hourly_rate_currency') select-error @enderror">
                    @foreach(App\Enums\Currency::commonOptions() as $code => $display)
                        <option value="{{ $code }}" {{ old('project_hourly_rate_currency', 'USD') === $code ? 'selected' : '' }}>
                            {{ $display }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="label">
                <span class="label-text-alt text-base-content/50">{{ __('Leave empty to use client\'s default rate') }}</span>
            </div>
            <x-form.error for="project_hourly_rate_amount" />
            <x-form.error for="project_hourly_rate_currency" />
        </div>

        <button type="submit"
                formaction="{{ route('turbo.projects-search.store') }}"
                formmethod="post"
                class="btn btn-primary btn-sm w-full"
                {{ !$clientId ? 'disabled' : '' }}>
            {{ __('Create Project') }}
        </button>
    </fieldset>
@endif