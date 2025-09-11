<x-layouts.auth :title="__('Login')">
    <div class="card bg-base-100 shadow-xl max-w-sm w-full">
        <div class="card-body">
            <div class="text-center mb-6">
                <h2 class="card-title justify-center text-2xl font-bold">Welcome Back</h2>
                <p class="text-base-content/70">Enter your credentials to access your account</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form action="{{ route('login.store') }}" method="post" class="space-y-6" data-turbo-action="replace">
                @csrf

                <!-- Email Address -->
                <div class="form-control">
                    <label class="label" for="email">
                        <span class="label-text font-medium">{{ __('Email address') }}</span>
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
                <div class="form-control">
                    <div class="flex items-center justify-between mb-2">
                        <label class="label" for="password">
                            <span class="label-text font-medium">{{ __('Password') }}</span>
                        </label>
                        @if (Route::has('password.request'))
                            <x-link class="label-text-alt link link-primary" :href="route('password.request')">
                                {{ __('Forgot password?') }}
                            </x-link>
                        @endif
                    </div>
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
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="remember" id="remember_me" class="checkbox checkbox-primary" />
                            <span class="label-text">{{ __('Remember me') }}</span>
                        </label>
                    </div>
                @endhotwirenative

                <div class="form-control">
                    <x-form.button.primary type="submit" class="btn-lg">
                        {{ __('Sign In') }}
                    </x-form.button.primary>
                </div>
            </form>

            @if (Route::has('register'))
                <div class="divider">OR</div>
                <div class="text-center">
                    <span class="text-base-content/70">{{ __('Don\'t have an account?') }}</span>
                    <x-link :href="route('register')" class="link link-primary font-medium">
                        {{ __('Create account') }}
                    </x-link>
                </div>
            @endif
        </div>
    </div>
</x-layouts.auth>
