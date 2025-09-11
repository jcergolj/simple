@if($clients->count() > 0)
    @foreach($clients as $client)
        <a href="#"
           class="block px-4 py-2 text-sm hover:bg-base-200 cursor-pointer border-b border-base-300 last:border-b-0"
           data-id="{{ $client->id }}">
            {{ $client->name }}
        </a>
    @endforeach
@else
    <!-- Client creation fields - NO FORM, just div for styling -->
    <div class="p-4 space-y-4 border border-base-300 rounded-lg" data-search-clients-target="createForm">
        <div class="text-sm text-base-content/70 mb-4">
            {{ __('No clients found. Create a new one?') }}
        </div>

        <div class="form-control">
            <label class="label" for="new_client_name">
                <span class="label-text font-semibold">{{ __('Client Name') }}</span>
                <span class="label-text-alt text-error">*</span>
            </label>
            <div class="relative">
                <input type="text" id="new_client_name" name="new_client_name" value="{{ $query ?? '' }}"
                    placeholder="{{ __('Enter client name') }}"
                    class="input input-bordered w-full pl-10"
                    data-search-clients-target="newClientName" />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="form-control">
            <label class="label" for="new_client_hourly_rate_amount">
                <span class="label-text font-semibold">{{ __('Hourly Rate') }}</span>
                <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
            </label>
            <x-form.money-input
                name="new_client_hourly_rate_amount"
                currency-name="new_client_hourly_rate_currency"
                placeholder="0.00"
                id="new_client_hourly_rate_amount"
                data-search-clients-target="newClientRate"
                currency-attributes='data-search-clients-target="newClientCurrency"'
                class="money-input-container"
            />

            <x-form.error for="new_client_hourly_rate_amount" />
            <x-form.error for="new_client_hourly_rate_currency" />
        </div>

        <div class="flex justify-end">
            <button type="button"
                    class="btn btn-primary btn-2xl"
                    data-action="click->search-clients#createClientFromFields"
                    data-create-url="{{ route('turbo.clients.store') }}">
                {{ __('Create Client') }}
            </button>
        </div>
    </div>
@endif
