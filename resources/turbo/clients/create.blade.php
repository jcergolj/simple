<turbo-frame id="client-create-form">
  <div class="card-body space-y-6">

    <!-- Header -->
    <div class="mb-4">
      <h2 class="card-title text-xl mb-1">{{ __('Create New Client') }}</h2>
      <p class="text-base-content/70">{{ __('Fill in the details below to add a new client to your system.') }}</p>
    </div>

    <!-- Form -->
    <form action="{{ route('clients.store') }}" method="POST" class="flex flex-col sm:flex-row sm:items-end sm:gap-4 flex-wrap">
      @csrf

      <!-- Client Name -->
      <div class="form-control flex-1 min-w-[250px]">
        <label class="label" for="name">
          <span class="label-text font-semibold">{{ __('Client Name') }}</span>
          <span class="label-text-alt text-error">*</span>
        </label>
        <div class="relative">
          <input type="text" id="name" name="name" value="{{ old('name') }}"
            placeholder="{{ __('Enter client name (e.g., Acme Corporation)') }}"
            class="input input-bordered w-full pl-10 @error('name') input-error @enderror" required autofocus />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
        </div>
        <x-form.error for="name" />
      </div>

      <!-- Hourly Rate -->
      <div class="form-control flex-1 min-w-[200px]">
        <label class="label" for="hourly_rate_amount">
          <span class="label-text font-semibold">{{ __('Hourly Rate') }}</span>
          <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
        </label>
        <div class="join w-full relative flex items-center">
          <input type="number" id="hourly_rate_amount" name="hourly_rate_amount"
            value="{{ old('hourly_rate_amount') }}" placeholder="0.00"
            class="input input-bordered join-item w-full pl-10 @error('hourly_rate_amount') input-error @enderror"
            step="0.01" min="0" />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <select name="hourly_rate_currency" class="select select-bordered join-item @error('hourly_rate_currency') select-error @enderror">
            @foreach(App\Enums\Currency::commonOptions() as $code => $display)
              <option value="{{ $code }}" {{ old('hourly_rate_currency', 'USD') === $code ? 'selected' : '' }}>
                {{ $display }}
              </option>
            @endforeach
          </select>
        </div>
        <x-form.error for="hourly_rate_amount" />
        <x-form.error for="hourly_rate_currency" />
      </div>

      <!-- Helper Text -->
      <div class="w-full text-sm text-base-content/50 mt-1 sm:mt-0">
        {{ __('Set the default hourly rate for this client\'s projects') }}
      </div>

      <!-- Form Actions -->
      <div class="flex gap-2 mt-2 sm:mt-0">
        <a href="{{ route('clients.index') }}" class="btn btn-ghost">{{ __('Cancel') }}</a>
        <button type="submit" class="btn btn-primary flex items-center gap-1">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          {{ __('Create') }}
        </button>
      </div>
    </form>

  </div>
</turbo-frame>
