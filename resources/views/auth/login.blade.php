<x-layouts.auth :title="__('Login')">
    <div class="bg-white shadow-xl rounded-xl max-w-lg w-full p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Welcome Back</h2>
            <p class="text-gray-600 mt-2">Enter your credentials to access your account</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-6" :status="session('status')" />

        <!-- Session Expired Alert (populated by JavaScript) -->
        <div id="session-expired-alert" class="hidden mb-6 p-4 rounded-lg bg-yellow-50 border border-yellow-200" data-controller="flash">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-yellow-800" id="session-expired-message">
                        {{ __('Your session has expired. Please log in again.') }}
                    </p>
                </div>
                <button type="button" class="ml-3 flex-shrink-0 inline-flex text-yellow-400 hover:text-yellow-600 focus:outline-none" data-action="click->flash#remove">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>

        <script>
            // Display session expired message from sessionStorage
            document.addEventListener('DOMContentLoaded', function() {
                if (sessionStorage.getItem('session_expired') === '1') {
                    const alertElement = document.getElementById('session-expired-alert');
                    if (alertElement) {
                        alertElement.classList.remove('hidden');
                    }
                    sessionStorage.removeItem('session_expired');
                }
            });
        </script>

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
