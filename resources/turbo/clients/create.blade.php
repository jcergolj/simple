<turbo-frame id="client-create-form">
  <div class="card-body space-y-6">

    <!-- Header -->
    <div class="mb-4">
      <h2 class="card-title text-xl mb-1">{{ __('Create New Client') }}</h2>
      <p class="text-base-content/70">{{ __('Fill in the details below to add a new client to your system.') }}</p>
    </div>

    <!-- Form -->
    <form action="{{ route('turbo.clients.store') }}" method="POST" class="space-y-6" data-turbo-frame="client-create-form">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Client Name -->
            <div class="form-control">
                <label class="label" for="name">
                    <span class="label-text font-semibold">{{ __('Client Name') }}</span>
                    <span class="label-text-alt text-error">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        placeholder="{{ __('Enter client name (e.g., Acme Corporation)') }}"
                        class="input input-bordered input-lg w-full pl-12 text-lg @error('name') input-error @enderror" required autofocus />
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <x-form.error for="name" />
            </div>

            <!-- Hourly Rate -->
            <div class="form-control">
                <label class="label" for="hourly_rate_amount">
                    <span class="label-text font-semibold">{{ __('Hourly Rate') }}</span>
                    <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
                </label>
                <x-form.money-input
                    name="hourly_rate_amount"
                    currency-name="hourly_rate_currency"
                    placeholder="0.00"
                    id="hourly_rate_amount"
                />
                <x-form.error for="hourly_rate_amount" />
                <x-form.error for="hourly_rate_currency" />
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-2 mt-4">
            <a href="{{ route('clients.index') }}" data-turbo-frame="client-create-form" class="btn btn-ghost">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary flex items-center gap-1">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('Create') }}
            </button>
        </div>
    </form>

  </div>
</turbo-frame>
