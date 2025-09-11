<turbo-frame id="client-{{ $client->id }}">
  <div class="p-6 bg-base-50 border-b border-base-200 last:border-b-0">
    <div class="card bg-base-100 shadow-lg">
      <div class="card-body space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
          <div>
            <h3 class="card-title text-xl">{{ __('Edit Client') }}</h3>
            <p class="text-base-content/70">{{ __('Update client information and default hourly rate.') }}</p>
          </div>
          <div class="avatar placeholder w-12 h-12">
            <div class="bg-info text-info-content rounded-full w-12 h-12 flex items-center justify-center">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Form -->
        <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-6">
          @csrf
          @method('PUT')

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">

              <!-- Client Name -->
              <div class="form-control">
                <label class="label" for="edit_name_{{ $client->id }}">
                  <span class="label-text font-semibold">{{ __('Client Name') }}</span>
                  <span class="label-text-alt text-error">*</span>
                </label>
                <div class="relative">
                  <input type="text" id="edit_name_{{ $client->id }}" name="name"
                    value="{{ old('name', $client->name) }}"
                    placeholder="{{ __('Enter client name (e.g., Acme Corporation)') }}"
                    class="input input-bordered w-full pl-10 focus:input-primary @error('name') input-error @enderror"
                    required />
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                </div>
                <x-form.error for="name" />
              </div>

            </div>

            <!-- Right Column -->
            <div class="space-y-6">

              <!-- Hourly Rate -->
              <div class="form-control">
                <label class="label" for="edit_hourly_rate_amount_{{ $client->id }}">
                  <span class="label-text font-semibold">{{ __('Default Hourly Rate') }}</span>
                  <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
                </label>
                <x-form.money-input
                  name="hourly_rate_amount"
                  currency-name="hourly_rate_currency"
                  :value="old('hourly_rate_amount', $client->hourly_rate?->amount)"
                  :currency="old('hourly_rate_currency', $client->hourly_rate?->currency ?? 'USD')"
                  placeholder="0.00"
                  :id="'edit_hourly_rate_amount_' . $client->id"
                />
                <div class="label">
                  <span class="label-text-alt text-base-content/50">{{ __('This rate will be used as default for new projects') }}</span>
                </div>
              </div>

            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex gap-2 justify-end pt-4 border-t border-base-200">
            <a href="{{ route('clients.index') }}" class="btn btn-ghost">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary flex items-center gap-1">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              {{ __('Update Client') }}
            </button>
          </div>
        </form>

        <!-- Client Statistics -->
        @if($client->projects_count > 0 || $client->time_entries_count > 0)
          <div class="stats shadow bg-base-200 mt-4">
            <div class="stat">
              <div class="stat-figure text-primary">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
              <div class="stat-title">{{ __('Projects') }}</div>
              <div class="stat-value text-primary">{{ $client->projects_count ?? 0 }}</div>
              <div class="stat-desc">{{ __('Active projects') }}</div>
            </div>

            <div class="stat">
              <div class="stat-figure text-secondary">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="stat-title">{{ __('Time Entries') }}</div>
              <div class="stat-value text-secondary">{{ $client->time_entries_count ?? 0 }}</div>
              <div class="stat-desc">{{ __('Total logged entries') }}</div>
            </div>
          </div>
        @endif

      </div>
    </div>
  </div>
</turbo-frame>