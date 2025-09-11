<x-layouts.auth :title="__('Login')">
    <div class="bg-white shadow-xl rounded-xl max-w-lg w-full p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Welcome Back</h2>
            <p class="text-gray-600 mt-2">Enter your credentials to access your account</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-6" :status="session('status')" />

        <form action="{{ route('login.store') }}" method="post" class="space-y-6" data-turbo-action="replace">
            @csrf

            <!-- Email Address -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700" for="email">
                    {{ __('Email address') }}
                </label>
                <x-form.text-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    :data-error="$errors->has('email')"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                />
                <x-form.error for="email" />
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700" for="password">
                    {{ __('Password') }}
                </label>
                <x-form.password-input
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                />
            </div>

            <!-- Remember Me -->
            @hotwirenative
                <input type="hidden" name="remember_me" value="1" />
            @else
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember_me" class="h-4 w-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900" />
                    <label for="remember_me" class="ml-2 block text-sm text-gray-700">{{ __('Remember me') }}</label>
                </div>
            @endhotwirenative

            <div class="pt-4">
                <x-form.button.primary type="submit" class="w-full py-3 text-lg">
                    {{ __('Sign In') }}
                </x-form.button.primary>
            </div>
        </form>

        @if (Route::has('register') && !\App\Models\User::exists())
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="text-center">
                    <span class="text-gray-600">{{ __('Don\'t have an account?') }}</span>
                    <x-link :href="route('register')" class="text-gray-900 font-medium hover:underline">
                        {{ __('Create account') }}
                    </x-link>
                </div>
            </div>
        @endif
    </div>
</x-layouts.auth>
