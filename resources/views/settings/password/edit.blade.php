<x-layouts.app :title="__('Update password')">
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
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Change Password') }}</h1>
            <p class="text-gray-600">{{ __('Ensure your account is using a long, random password to stay secure') }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <form action="{{ route('settings.password.update') }}" method="post" class="space-y-6" data-controller="bridge--form" data-action="turbo:submit-start->bridge--form#submitStart turbo:submit-end->bridge--form#submitEnd">
                @csrf
                @method('put')

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Current password') }}</label>
                    <x-form.password-input
                        id="current_password"
                        name="current_password"
                        :data-error="$errors->has('current_password')"
                        required
                        autofocus
                        autocomplete="current-password"
                        :placeholder="__('Current password')"
                    />
                    <x-form.error for="current_password" />
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('New password') }}</label>
                    <x-form.password-input
                        id="password"
                        name="password"
                        :data-error="$errors->has('password')"
                        required
                        autocomplete="new-password"
                        :placeholder="__('New password')"
                    />
                    <x-form.error for="password" />
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Confirm password') }}</label>
                    <x-form.password-input
                        id="password_confirmation"
                        name="password_confirmation"
                        :data-error="$errors->has('password_confirmation')"
                        required
                        autocomplete="new-password"
                        :placeholder="__('Confirm password')"
                    />
                    <x-form.error for="password_confirmation" />
                </div>

                <div class="flex justify-end">
                    <x-form.button.primary type="submit" data-bridge--form-target="submit" data-bridge-title="{{ __('Save') }}">
                        {{ __('Update Password') }}
                    </x-form.button.primary>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
