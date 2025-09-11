<x-layouts.auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form action="{{ route('register.store') }}" method="post" class="flex flex-col gap-6" data-turbo-action="replace">
            @csrf

            <!-- Name -->
            <div>
                <x-form.label for="name">{{ __('Name') }}</x-form.label>

                <x-form.text-input id="name" name="name" :value="old('name')" :data-error="$errors->has('name')" required autofocus
                    autocomplete="name" :placeholder="__('Full name')" class="mt-2" />

                <x-form.error for="name" />
            </div>

            <!-- Email Address -->
            <div>
                <x-form.label for="email">{{ __('Email address') }}</x-form.label>

                <x-form.text-input id="email" name="email" type="email" :value="old('email')" :data-error="$errors->has('email')"
                    required autocomplete="email" :placeholder="__('email@example.com')" class="mt-2" />

                <x-form.error for="email" />
            </div>

            <!-- Password -->
            <div>
                <x-form.label for="password">{{ __('Password') }}</x-form.label>

                <x-form.password-input id="password" name="password" :data-error="$errors->has('password')" required
                    autocomplete="new-password" :placeholder="__('Password')" class="mt-2" />

                <x-form.error for="password" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-form.label for="password_confirmation">{{ __('Confirm password') }}</x-form.label>

                <x-form.password-input id="password_confirmation" name="password_confirmation" required
                    autocomplete="new-password" :placeholder="__('Confirm password')" class="mt-2" />

                <x-form.error for="password_confirmation" />
            </div>

            <!-- Hourly Rate -->
            <div>
                <x-form.label for="hourly_rate_amount">{{ __('Hourly Rate (Optional)') }}</x-form.label>

                <div class="flex gap-2 mt-2">
                    <div class="flex-1">
                        <x-form.text-input id="hourly_rate_amount" name="hourly_rate_amount" type="number"
                            :value="old('hourly_rate_amount')" :data-error="$errors->has('hourly_rate_amount')"
                            :placeholder="__('0.00')" class="w-full" step="0.01" min="0" />
                        <x-form.error for="hourly_rate_amount" />
                    </div>

                    <div class="w-32">
                        <select id="hourly_rate_currency" name="hourly_rate_currency"
                            class="select select-bordered w-full @error('hourly_rate_currency') select-error @enderror">
                            <option value="">{{ __('Currency') }}</option>
                            @foreach(\App\Enums\Currency::commonOptions() as $value => $label)
                                <option value="{{ $value }}" @selected(old('hourly_rate_currency', 'USD') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <x-form.error for="hourly_rate_currency" />
                    </div>
                </div>
                <p class="text-sm text-base-content/70 mt-1">{{ __('Set your default hourly rate for time tracking') }}</p>
            </div>

            <div class="flex items-center justify-end">
                <x-form.button.primary type="submit" class="w-full">{{ __('Create account') }}</x-form.button.primary>
            </div>
        </form>

        <div class="space-x-1 text-center text-sm text-base-600">
            {{ __('Already have an account?') }}
            <x-link :href="route('login')">{{ __('Log in') }}</x-link>
        </div>
    </div>
</x-layouts.auth>
