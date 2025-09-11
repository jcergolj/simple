<x-layouts.app :title="__('Delete profile')">
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
            <h1 class="text-3xl font-bold text-red-900 mb-2">{{ __('Delete Account') }}</h1>
            <p class="text-gray-600">{{ __('Delete your account and all of its resources') }}</p>
        </div>

        <!-- Warning Card -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        {{ __('This action cannot be undone') }}
                    </h3>
                    <p class="mt-2 text-sm text-red-700">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <form action="{{ route('settings.profile.destroy') }}" method="post" class="space-y-6" data-controller="bridge--form" data-action="turbo:submit-start->bridge--form#submitStart turbo:submit-end->bridge--form#submitEnd">
                @csrf

                <!-- Current Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Current password') }}</label>
                    <x-form.password-input
                        id="password"
                        type="password"
                        name="password"
                        :data-error="$errors->has('password')"
                        required
                        autofocus
                        autocomplete="current-password"
                        :placeholder="__('Enter your current password')"
                    />
                    <x-form.error for="password" />
                </div>

                <div class="flex justify-end">
                    <x-form.button.danger type="submit" data-bridge--form-target="submit" data-bridge-title="{{ __('Delete') }}" data-bridge-destructive="true">
                        {{ __('Delete Account Permanently') }}
                    </x-form.button.danger>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
