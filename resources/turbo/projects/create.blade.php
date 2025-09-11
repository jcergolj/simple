<turbo-frame id="project-create-form">
  <div class="card-body space-y-6">

    <!-- Header -->
    <div class="mb-4">
      <h2 class="card-title text-xl mb-1">{{ __('Create New Project') }}</h2>
      <p class="text-base-content/70">{{ __('Fill in the details below to add a new project to your system.') }}</p>
    </div>

    <!-- Form -->
    <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
      @csrf

      <!-- Project Name -->
      <div class="form-control">
        <label class="label" for="name">
          <span class="label-text font-semibold">{{ __('Project Name') }}</span>
          <span class="label-text-alt text-error">*</span>
        </label>
        <div class="relative">
          <input type="text" id="name" name="name" value="{{ old('name') }}"
            placeholder="{{ __('Enter project name (e.g., Website Redesign)') }}"
            class="input input-bordered w-full pl-10 @error('name') input-error @enderror" required autofocus />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
        </div>
        <x-form.error for="name" />
      </div>

      <div class="form-control">
        <label class="label">
          <span class="label-text font-semibold">{{ __('Client') }}</span>
          <span class="label-text-alt text-error">*</span>
        </label>
        <x-form.search-clients
          searchId="create-project"
          fieldName="client_id"
          inputName="client_name"
          :clientId="old('client_id')"
          placeholder="{{ __('Search for a client...') }}"
        />
        <x-form.error for="client_id" />
      </div>

      <!-- Description -->
      <div class="form-control">
        <label class="label" for="description">
          <span class="label-text font-semibold">{{ __('Description') }}</span>
          <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
        </label>
        <textarea id="description" name="description" rows="3"
          placeholder="{{ __('Brief description of the project...') }}"
          class="textarea textarea-bordered @error('description') textarea-error @enderror">{{ old('description') }}</textarea>
        <x-form.error for="description" />
      </div>

      <!-- Hourly Rate Override -->
      <div class="form-control">
        <label class="label" for="hourly_rate_amount">
          <span class="label-text font-semibold">{{ __('Project Hourly Rate') }}</span>
          <span class="label-text-alt text-base-content/50">{{ __('Override client rate (Optional)') }}</span>
        </label>
        <x-form.money-input
          name="hourly_rate_amount"
          currency-name="hourly_rate_currency"
          :value="old('hourly_rate_amount')"
          :currency="old('hourly_rate_currency', 'USD')"
          placeholder="0.00"
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          {{ __('Create Project') }}
        </button>
      </div>
    </form>
  </div>
</turbo-frame>
