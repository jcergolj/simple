<turbo-frame id="project-{{ $project->id }}">
  <div class="card-body space-y-6">

    <!-- Header -->
    <div class="mb-4">
      <h2 class="card-title text-xl mb-1">{{ __('Edit Project') }}</h2>
      <p class="text-base-content/70">{{ __('Update :name\'s information and settings.', ['name' => $project->name]) }}</p>
    </div>

    <!-- Form -->
    <form action="{{ route('turbo.projects.update', $project) }}" method="POST" class="space-y-6" data-turbo-frame="project-{{ $project->id }}">
      @csrf
      @method('PUT')

      <!-- Project Name, Client, and Hourly Rate in One Row -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Project Name -->
        <div class="form-control">
          <label class="label" for="edit_name_{{ $project->id }}">
            <span class="label-text font-semibold">{{ __('Project Name') }}</span>
            <span class="label-text-alt text-error">*</span>
          </label>
          <div class="relative">
            <input type="text" id="edit_name_{{ $project->id }}" name="name" value="{{ old('name', $project->name) }}"
              placeholder="{{ __('Enter project name') }}"
              class="input input-bordered input-lg w-full pl-12 text-lg @error('name') input-error @enderror" required autofocus />
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="h-6 w-6 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
            </div>
          </div>
          <x-form.error for="name" />
        </div>

        <!-- Client -->
        <div class="form-control">
          <label class="label">
            <span class="label-text font-semibold">{{ __('Client') }}</span>
          </label>
          <x-form.search-clients
            searchId="edit-project-{{ $project->id }}"
            fieldName="client_id"
            inputName="client_name"
            :clientId="old('client_id', $project->client_id)"
            :clientName="old('client_name', $project->client?->name)"
            placeholder="{{ __('Search or create client...') }}"
          />
          <x-form.error for="client_id" />
        </div>

        <!-- Hourly Rate -->
        <div class="form-control">
          <label class="label" for="edit_hourly_rate_amount_{{ $project->id }}">
            <span class="label-text font-semibold">{{ __('Hourly Rate') }}</span>
            <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
          </label>
          <x-form.money-input
            name="hourly_rate_amount"
            currency-name="hourly_rate_currency"
            :value="old('hourly_rate_amount', $project->hourly_rate?->toDecimal())"
            :currency="old('hourly_rate_currency', $project->hourly_rate?->currency)"
            placeholder="0.00"
            :id="'edit_hourly_rate_amount_' . $project->id"
            :project="$project"
          />
          <span class="label-text-alt text-base-content/50">{{ __('Override client rate') }}</span>
          <x-form.error for="hourly_rate_amount" />
        <x-form.error for="hourly_rate_currency" />
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
  </div>
</turbo-frame>
