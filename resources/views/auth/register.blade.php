<x-guest-layout>
    <form method="POST" action="{{ route('dashboard.register') }}">
        @csrf
        <div>
            <h2 class="text-2xl font-bold text-center mb-4">{{ __('Dashboard Register') }}</h2>
        </div>
        <!-- First Name -->
        <div class="input-group relative mt-4">
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="first_name" />
            <x-input-label class=" absolute top[50%] left-3 text-lg -translate-y-1/2 duration-300 ease-in" for="first_name" :value="__('First Name')" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>


        <!-- Last Name -->
        <div class="input-group relative mt-4">
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autofocus autocomplete="last_name" />
            <x-input-label class=" absolute top[50%] left-3 text-lg -translate-y-1/2 duration-300 ease-in" for="last_name" :value="__('Last Name')" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Name -->
        <div class="input-group relative mt-4">
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-label class=" absolute top[50%] left-3 text-lg -translate-y-1/2 duration-300 ease-in" for="name" :value="__('Username')" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="input-group relative mt-4">
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-label class=" absolute top[50%] left-3 text-lg -translate-y-1/2 duration-300 ease-in" for="email" :value="__('Email')" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="input-group relative mt-4">
        
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-label class=" absolute top[50%] left-3 text-lg -translate-y-1/2 duration-300 ease-in" for="password" :value="__('Password')" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="input-group relative mt-4">
            
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
            type="password"
            name="password_confirmation" required autocomplete="new-password" />
            
            <x-input-label class=" absolute top[50%] left-3 text-lg -translate-y-1/2 duration-300 ease-in" for="password_confirmation" :value="__('Confirm Password')" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <style>
            .input-group input:focus+label, .input-group input:valid+label{
                top:-1px;
                font-size: 14px;
            }
        </style>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
