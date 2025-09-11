@if($clients->count() > 0)
    @foreach($clients as $client)
        <a href="#"
           class="block px-4 py-2 text-sm hover:bg-base-200 cursor-pointer border-b border-base-300 last:border-b-0"
           data-id="{{ $client->id }}">
            {{ $client->name }}
        </a>
    @endforeach
@else
    <!-- Client creation fields inside the existing timer form -->
    <fieldset class="p-4 space-y-4 border border-base-300 rounded-lg">
        <legend class="text-sm text-base-content/70 px-2">
            {{ __('No clients found. Create a new one?') }}
        </legend>

        <div class="form-control">
            <label class="label" for="client_name">
                <span class="label-text font-semibold">{{ __('Client Name') }}</span>
                <span class="label-text-alt text-error">*</span>
            </label>
            <div class="relative">
                <input type="text" id="client_name" name="client_name" value="{{ $query ?? old('client_name') }}"
                    placeholder="{{ __('Enter client name') }}"
                    class="input input-bordered w-full pl-10 @error('client_name') input-error @enderror" />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <x-form.error for="client_name" />
        </div>

        <!-- Hourly Rate -->
        <div class="form-control">
            <label class="label" for="client_hourly_rate_amount">
                <span class="label-text font-semibold">{{ __('Hourly Rate') }}</span>
                <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
            </label>
            <div class="join w-full">
                <div class="relative flex-1">
                    <input type="number" id="client_hourly_rate_amount" name="client_hourly_rate_amount"
                        value="{{ old('client_hourly_rate_amount') }}" placeholder="0.00"
                        class="input input-bordered join-item w-full pl-10 @error('client_hourly_rate_amount') input-error @enderror"
                        step="0.01" min="0" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <select name="client_hourly_rate_currency" class="select select-bordered join-item @error('client_hourly_rate_currency') select-error @enderror">
                    @foreach(App\Enums\Currency::commonOptions() as $code => $display)
                        <option value="{{ $code }}" {{ old('client_hourly_rate_currency', 'USD') === $code ? 'selected' : '' }}>
                            {{ $display }}
                        </option>
                    @endforeach
                </select>
            </div>
            <x-form.error for="client_hourly_rate_amount" />
            <x-form.error for="client_hourly_rate_currency" />
        </div>

        <button type="submit"
                formaction="{{ route('turbo.clients-search.store') }}"
                formmethod="post"
                class="btn btn-primary btn-sm w-full">
            {{ __('Create Client') }}
        </button>
    </fieldset>
@endif
