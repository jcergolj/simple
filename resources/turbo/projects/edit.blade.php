<turbo-frame id="project-{{ $project->id }}">
  <div class="p-6 bg-base-50 border-b border-base-200 last:border-b-0">
    <div class="card bg-base-100 shadow-lg">
      <div class="card-body space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
          <div>
            <h3 class="card-title text-xl">{{ __('Edit Project') }}</h3>
            <p class="text-base-content/70">{{ __('Update :name\'s information and billing settings.', ['name' => $project->name]) }}</p>
          </div>
          <div class="avatar placeholder w-12 h-12">
            <div class="bg-secondary text-secondary-content rounded-full w-12 h-12 flex items-center justify-center">
              <span class="text-lg font-bold">{{ strtoupper(substr($project->name, 0, 2)) }}</span>
            </div>
          </div>
        </div>

        <!-- Form -->
        <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-6">
          @csrf
          @method('PUT')

          <!-- Project Name -->
          <div class="form-control">
            <label class="label" for="edit_name_{{ $project->id }}">
              <span class="label-text font-semibold">{{ __('Project Name') }}</span>
              <span class="label-text-alt text-error">*</span>
            </label>
            <div class="relative">
              <input type="text" id="edit_name_{{ $project->id }}" name="name" value="{{ old('name', $project->name) }}"
                placeholder="{{ __('Enter project name') }}"
                class="input input-bordered w-full pl-10 focus:input-primary @error('name') input-error @enderror" required />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
            </div>
            <x-form.error for="name" />
          </div>

          <!-- Client Selection -->
          <x-form.search-clients
            name="client_id"
            :value="old('client_id', $project->client_id)"
            placeholder="{{ __('Search for a client...') }}"
            :required="true"
            label="{{ __('Client') }}"
            :id="'edit_client_id_' . $project->id"
          />

          <!-- Description -->
          <div class="form-control">
            <label class="label" for="edit_description_{{ $project->id }}">
              <span class="label-text font-semibold">{{ __('Description') }}</span>
              <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
            </label>
            <textarea id="edit_description_{{ $project->id }}" name="description" rows="3"
              placeholder="{{ __('Brief description of the project...') }}"
              class="textarea textarea-bordered focus:textarea-primary @error('description') textarea-error @enderror">{{ old('description', $project->description) }}</textarea>
            <x-form.error for="description" />
          </div>

          <!-- Hourly Rate -->
          <div class="form-control">
            <label class="label" for="edit_hourly_rate_amount_{{ $project->id }}">
              <span class="label-text font-semibold">{{ __('Project Hourly Rate') }}</span>
              <span class="label-text-alt text-base-content/50">{{ __('Override client rate (Optional)') }}</span>
            </label>
            <x-form.money-input
              name="hourly_rate_amount"
              currency-name="hourly_rate_currency"
              :value="old('hourly_rate_amount', $project->hourly_rate?->amount)"
              :currency="old('hourly_rate_currency', $project->hourly_rate?->currency ?? 'USD')"
              placeholder="0.00"
              :id="'edit_hourly_rate_amount_' . $project->id"
            />
            <div class="label">
              <span class="label-text-alt text-base-content/50">{{ __('Leave empty to use client\'s default rate') }}</span>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex gap-2 justify-end">
            <a href="{{ route('projects.index') }}" class="btn btn-ghost">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary flex items-center gap-1">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              {{ __('Update Project') }}
            </button>
          </div>
        </form>

        <!-- Stats -->
        @if($project->time_entries_count > 0)
          <div class="stats stats-vertical sm:stats-horizontal shadow bg-base-200 mt-4">
            <div class="stat">
              <div class="stat-figure text-secondary">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="stat-title">{{ __('Time Entries') }}</div>
              <div class="stat-value text-secondary">{{ $project->time_entries_count }}</div>
              <div class="stat-desc">{{ __('Total entries logged') }}</div>
            </div>
          </div>
        @endif

      </div>
    </div>
  </div>
</turbo-frame>
