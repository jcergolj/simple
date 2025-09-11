<x-layouts.app :title="__('Preferences')">
    <div class="max-w-2xl mx-auto">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="{{ route('settings') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                {{ __('Back to Settings') }}
            </a>
        </div>

        <!-- Page Header -->
        <div class="text-center py-4 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Preferences') }}</h1>
            <p class="text-gray-600">{{ __('Customize your application settings and display preferences') }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <form action="{{ route('settings.preferences.update') }}" method="post" class="space-y-6" data-controller="bridge--form" data-action="turbo:submit-start->bridge--form#submitStart turbo:submit-end->bridge--form#submitEnd">
                @csrf
                @method('put')

                <!-- Date Format -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Date Format') }}</label>
                    <div class="space-y-2">
                        @foreach($dateFormatOptions as $value => $label)
                            <label class="flex items-center">
                                <input
                                    type="radio"
                                    name="date_format"
                                    value="{{ $value }}"
                                    class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300"
                                    @checked(old('date_format', $dateFormat->value) === $value)
                                    required
                                >
                                <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-1">
                        <p class="text-sm text-gray-600">{{ __('Choose how dates are displayed throughout the application.') }}</p>
                    </div>
                    <x-form.error for="date_format" />
                </div>

                <!-- Time Format -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Time Format') }}</label>
                    <div class="space-y-2">
                        @foreach($timeFormatOptions as $value => $label)
                            <label class="flex items-center">
                                <input
                                    type="radio"
                                    name="time_format"
                                    value="{{ $value }}"
                                    class="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300"
                                    @checked(old('time_format', $timeFormat->value) === $value)
                                    required
                                >
                                <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-1">
                        <p class="text-sm text-gray-600">{{ __('Choose between 12-hour (with AM/PM) or 24-hour time format.') }}</p>
                    </div>
                    <x-form.error for="time_format" />
                </div>

                <!-- Hourly Rate -->
                <div>
                    <label for="hourly_rate_amount" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Default Hourly Rate') }}</label>
                    <x-form.money-input
                        name="hourly_rate_amount"
                        currency-name="hourly_rate_currency"
                        :value="old('hourly_rate_amount', $hourly_rate?->toDecimal())"
                        :currency="old('hourly_rate_currency', $hourly_rate?->currency ?? 'USD')"
                        placeholder="0.00"
                        id="hourly_rate_amount"
                    />
                    <div class="mt-1">
                        <p class="text-sm text-gray-600">{{ __('This will be used as the default rate for new clients and projects.') }}</p>
                    </div>
                    <x-form.error for="hourly_rate_amount" />
                    <x-form.error for="hourly_rate_currency" />
                </div>

                <div class="flex justify-end">
                    <x-form.button.primary type="submit" data-bridge--form-target="submit" data-bridge-title="{{ __('Save') }}">
                        {{ __('Save Changes') }}
                    </x-form.button.primary>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>