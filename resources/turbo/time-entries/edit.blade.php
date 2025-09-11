<turbo-frame id="time-entry-{{ $timeEntry->id }}">
  <div class="p-6 bg-base-50 border-b border-base-200 last:border-b-0">
    <div class="card bg-base-100 shadow-lg">
      <div class="card-body space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
          <div>
            <h3 class="card-title text-xl">{{ __('Edit Time Entry') }}</h3>
            <p class="text-base-content/70">{{ __('Update time tracking information for this entry.') }}</p>
          </div>
          <div class="avatar placeholder w-12 h-12">
            <div class="bg-accent text-accent-content rounded-full w-12 h-12 flex items-center justify-center">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Form -->
        <form action="{{ route('time-entries.update', $timeEntry) }}" method="POST" class="space-y-6">
          @csrf
          @method('PUT')

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">

              <!-- Project Selection -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold">{{ __('Project') }}</span>
                  <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
                </label>
                <x-form.search-projects
                  searchId="edit-time-entry-{{ $timeEntry->id }}"
                  fieldName="project_id"
                  inputName="project_name"
                  :projectId="$timeEntry->project_id"
                  :projectName="$timeEntry->project?->name"
                />
                <x-form.error for="project_id" />
              </div>

              <!-- Client Selection -->
              <div class="form-control">
                <label class="label">
                  <span class="label-text font-semibold">{{ __('Client') }}</span>
                  <span class="label-text-alt text-base-content/50">{{ __('Optional (auto-filled)') }}</span>
                </label>
                <x-form.search-clients
                  searchId="edit-time-entry-{{ $timeEntry->id }}"
                  fieldName="client_id"
                  inputName="client_name"
                  :clientId="$timeEntry->client_id"
                  :clientName="$timeEntry->client?->name"
                />
                <x-form.error for="client_id" />
              </div>

              <!-- Notes -->
              <div class="form-control">
                <label class="label" for="edit_notes_{{ $timeEntry->id }}">
                  <span class="label-text font-semibold">{{ __('Notes') }}</span>
                  <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
                </label>
                <textarea id="edit_notes_{{ $timeEntry->id }}" name="notes" rows="4"
                  placeholder="{{ __('What did you work on?') }}"
                  class="textarea textarea-bordered focus:textarea-primary @error('notes') textarea-error @enderror">{{ old('notes', $timeEntry->notes) }}</textarea>
                <x-form.error for="notes" />
              </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">

              <!-- Time Tracking -->
              <div class="card bg-base-200 shadow-sm">
                <div class="card-body">
                  <h3 class="card-title text-lg mb-4">{{ __('Time Tracking') }}</h3>

                  <!-- Start Time -->
                  <div class="form-control">
                    <label class="label" for="edit_start_time_{{ $timeEntry->id }}">
                      <span class="label-text font-semibold">{{ __('Start Time') }}</span>
                      <span class="label-text-alt text-error">*</span>
                    </label>
                    <div class="relative">
                      <input type="datetime-local" id="edit_start_time_{{ $timeEntry->id }}" name="start_time"
                        value="{{ old('start_time', $timeEntry->start_time?->format('Y-m-d\TH:i')) }}"
                        class="input input-bordered w-full pl-10 focus:input-primary @error('start_time') input-error @enderror"
                        required
                        data-controller="time-calculator"
                        data-action="change->time-calculator#calculateDuration"
                        data-time-calculator-target="startTime" />
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </div>
                    </div>
                    <x-form.error for="start_time" />
                  </div>

                  <!-- End Time -->
                  <div class="form-control">
                    <label class="label" for="edit_end_time_{{ $timeEntry->id }}">
                      <span class="label-text font-semibold">{{ __('End Time') }}</span>
                      <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
                    </label>
                    <div class="relative">
                      <input type="datetime-local" id="edit_end_time_{{ $timeEntry->id }}" name="end_time"
                        value="{{ old('end_time', $timeEntry->end_time?->format('Y-m-d\TH:i')) }}"
                        class="input input-bordered w-full pl-10 focus:input-primary @error('end_time') input-error @enderror"
                        data-action="change->time-calculator#calculateDuration"
                        data-time-calculator-target="endTime" />
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </div>
                    </div>
                    <x-form.error for="end_time" />
                  </div>

                  <!-- Duration Display -->
                  <div class="form-control">
                    <label class="label">
                      <span class="label-text font-semibold">{{ __('Duration') }}</span>
                    </label>
                    <div class="flex items-center gap-2">
                      <div class="badge badge-primary badge-lg" data-time-calculator-target="duration">
                        {{ $timeEntry->getFormattedDuration() }}
                      </div>
                      <span class="text-sm text-base-content/70">{{ __('Calculated automatically') }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Hourly Rate Override -->
              <div class="form-control">
                <label class="label" for="edit_hourly_rate_amount_{{ $timeEntry->id }}">
                  <span class="label-text font-semibold">{{ __('Hourly Rate Override') }}</span>
                  <span class="label-text-alt text-base-content/50">{{ __('Optional') }}</span>
                </label>
                <x-form.money-input
                  name="hourly_rate_amount"
                  currency-name="hourly_rate_currency"
                  :value="old('hourly_rate_amount', $timeEntry->hourly_rate?->amount)"
                  :currency="old('hourly_rate_currency', $timeEntry->hourly_rate?->currency ?? 'USD')"
                  placeholder="0.00"
                  :id="'edit_hourly_rate_amount_' . $timeEntry->id"
                />
                <div class="label">
                  <span class="label-text-alt text-base-content/50">{{ __('Leave empty to use project/client rate') }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex gap-2 justify-end pt-4 border-t border-base-200">
            <a href="{{ route('time-entries.index') }}" class="btn btn-ghost">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary flex items-center gap-1">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              {{ __('Update Time Entry') }}
            </button>
          </div>
        </form>

        <!-- Earnings Display -->
        @if($timeEntry->calculateEarnings())
          <div class="stats shadow bg-base-200 mt-4">
            <div class="stat">
              <div class="stat-figure text-success">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
              <div class="stat-title">{{ __('Estimated Earnings') }}</div>
              <div class="stat-value text-success">
                @php
                  $earnings = $timeEntry->calculateEarnings();
                  $currency = App\Enums\Currency::from($earnings->currency);
                @endphp
                {{ $currency->symbol() }}{{ number_format($earnings->amount, 2) }}
              </div>
              <div class="stat-desc">{{ $timeEntry->getFormattedDuration() }} × Rate</div>
            </div>
          </div>
        @endif

      </div>
    </div>
  </div>
</turbo-frame>

<script type="module">
  // Time calculator controller
  class TimeCalculatorController extends Stimulus.Controller {
    static targets = ["startTime", "endTime", "duration"]

    connect() {
      // Initialize with current values
      this.calculateDuration()
    }

    calculateDuration() {
      const startTime = new Date(this.startTimeTarget.value)
      const endTime = new Date(this.endTimeTarget.value)

      if (startTime && endTime && endTime > startTime) {
        const diffMs = endTime - startTime
        const hours = Math.floor(diffMs / (1000 * 60 * 60))
        const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60))

        this.durationTarget.textContent = `${hours}h ${minutes}m`
      } else if (this.durationTarget) {
        // Keep existing duration if no valid end time
        const existingText = this.durationTarget.textContent
        if (!existingText || existingText === '0h 0m') {
          this.durationTarget.textContent = '0h 0m'
        }
      }
    }
  }

  // Register controllers
  Stimulus.register("time-calculator", TimeCalculatorController)
</script>