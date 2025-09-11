<turbo-frame id="time-entry-{{ $timeEntry->id }}">
  <div class="card-body space-y-6">

    <!-- Header -->
    <div class="mb-4">
      <h2 class="card-title text-xl mb-1">{{ __('Edit Time Entry') }}</h2>
      <p class="text-base-content/70">{{ __('Update time tracking information for this entry.') }}</p>
    </div>

    <!-- Form -->
    <form action="{{ route('turbo.time-entries.update', $timeEntry) }}" method="POST" class="space-y-6" data-turbo-frame="time-entry-{{ $timeEntry->id }}">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <x-form.search-clients
            searchId="edit-time-entry-{{ $timeEntry->id }}"
            fieldName="client_id"
            inputName="client_name"
            :clientId="$timeEntry->client_id"
            :clientName="$timeEntry->client?->name"
          />
        </div>

        <div>
          <x-form.search-projects
            searchId="edit-time-entry-{{ $timeEntry->id }}"
            fieldName="project_id"
            inputName="project_name"
            :projectId="$timeEntry->project_id"
            :projectName="$timeEntry->project?->name"
          />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Start Time') }}</label>
          <input
            type="datetime-local"
            id="start_time"
            name="start_time"
            value="{{ old('start_time', $timeEntry->start_time?->format('Y-m-d\TH:i')) }}"
            class="w-full px-4 py-4 text-lg border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('start_time') border-red-500 @enderror"
            required
          >
            <x-form.error for="start_time" />
        </div>

        <div>
          <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">{{ __('End Time') }}</label>
          <input
            type="datetime-local"
            id="end_time"
            name="end_time"
            value="{{ old('end_time', $timeEntry->end_time?->format('Y-m-d\TH:i')) }}"
            class="w-full px-4 py-4 text-lg border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('end_time') border-red-500 @enderror"
          >
            <x-form.error for="end_time" />
          </div>
        </div>

      <div>
        <label for="hourly_rate_amount" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Hourly Rate') }}</label>
        <x-form.money-input
          name="hourly_rate_amount"
          currency-name="hourly_rate_currency"
          :value="old('hourly_rate_amount', $timeEntry->hourly_rate?->toDecimal())"
          :currency="old('hourly_rate_currency', $timeEntry->hourly_rate?->currency)"
          :project="$timeEntry->project"
          :client="$timeEntry->client"
          placeholder="0.00"
          :id="'edit_hourly_rate_amount_' . $timeEntry->id"
        />
        <p class="mt-1 text-sm text-gray-600">{{ __('Leave empty to use project/client rate') }}</p>

        <x-form.error for="hourly_rate_amount" />
        <x-form.error for="hourly_rate_currency" />
      </div>

       <div>
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Notes') }}</label>
        <textarea
          id="notes"
          name="notes"
          rows="3"
          class="w-full px-4 py-4 text-lg border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('notes') border-red-500 @enderror"
          placeholder="{{ __('Optional notes about this time entry...') }}"
        >{{ old('notes', $timeEntry->notes) }}</textarea>
        <x-form.error for="notes" />
      </div>

      
      <div class="flex gap-3 justify-end">
        <a href="{{ route('time-entries.index') }}" class="text-gray-600 hover:text-gray-900 px-6 py-3 font-medium transition-colors inline-flex items-center">
          {{ __('Cancel') }}
        </a>
        
        <button type="submit" class="bg-gray-900 text-white px-6 py-3 rounded-md font-medium hover:bg-gray-800 transition-colors inline-flex items-center space-x-2">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          <span>{{ __('Update Time Entry') }}</span>
        </button>
      </div>
    </form>

  </div>
</turbo-frame>
