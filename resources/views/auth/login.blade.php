<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <h2 class="text-2xl font-bold mb-4 text-center">{{ __('Login') }}</h2>
        </div>

        <!-- Email Address -->
        <div class="input-group mt-4 relative">
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="login" />
            <x-input-label for="login" :value="__('Email or Username')" class="absolute top-[50%] left-3 bg-white -translate-y-1/2 duration-300 ease-in text-lg" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="input-group relative mt-4">
            <x-text-input id="password" class="block mt-1 w-full"
            type="password"
            name="password"
            required autocomplete="current-password" />

            <x-input-label for="password" :value="__('Password')" class="absolute top-[50%] left-3 bg-white -translate-y-1/2 duration-300 ease-in text-lg"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <style>
            .input-group input:focus+label, .input-group input:valid+label{
                top:-1px;
                font-size: 14px;
            }
        </style>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
