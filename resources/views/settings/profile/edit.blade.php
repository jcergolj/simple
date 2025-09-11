<x-layouts.app :title="__('Profile')">
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
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Edit Profile') }}</h1>
            <p class="text-gray-600">{{ __('Update your personal information') }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <form action="{{ route('settings.profile.update') }}" method="post" class="space-y-6" data-controller="bridge--form" data-action="turbo:submit-start->bridge--form#submitStart turbo:submit-end->bridge--form#submitEnd">
                @csrf
                @method('put')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Name') }}</label>
                    <x-form.text-input
                        id="name"
                        name="name"
                        :value="old('name', $name)"
                        :data-error="$errors->has('name')"
                        required
                        autofocus
                        autocomplete="name"
                        :placeholder="__('Full name')"
                    />
                    <x-form.error for="name" />
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email address') }}</label>
                    <x-form.text-input
                        id="email"
                        name="email"
                        type="email"
                        :value="old('email', $email)"
                        :data-error="$errors->has('email')"
                        required
                        autocomplete="email"
                        :placeholder="__('email@example.com')"
                    />
                    <x-form.error for="email" />

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                            <p class="text-sm text-yellow-800 mb-2">
                                {{ __('Your email address is unverified.') }}
                            </p>
                            <button form="resend-email-verification" class="text-sm font-medium text-yellow-800 hover:text-yellow-900 underline">
                                {{ __('Re-send the verification email') }}
                            </button>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end">
                    <x-form.button.primary type="submit" data-bridge--form-target="submit" data-bridge-title="{{ __('Save') }}">
                        {{ __('Save Changes') }}
                    </x-form.button.primary>
                </div>
            </form>
        </div>

        <form action="{{ route('verification.resend') }}" method="post" id="resend-email-verification">
            @csrf
        </form>
    </div>
</x-layouts.app>
