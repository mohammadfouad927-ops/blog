<section>

    <section
        x-data="{
        open: false,
        password: ''
    }"
    >

    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form id="data-form" method="post" action="{{route('blog.settings.update')}}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <input type="hidden" name="password" x-model="password">
        <div>
            <x-input-label for="name" :value="__('Username')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" required autofocus autocomplete="first_name" />
            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
        </div>

        <div>
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required autofocus autocomplete="last_name" />
            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal','reauth')">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
            <x-input-error
                class="mt-2" :messages="$errors->get('password')" />
        </div>
        <x-modal name="reauth" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Confirm your password
                </h2>

                <p class="mt-2 text-sm text-gray-600">
                    For security reasons, please confirm your password.
                </p>

                <div class="mt-4">
                    <x-input-label for="confirm_password" value="Password" />

                    <x-text-input
                        id="confirm_password"
                        type="password"
                        class="mt-1 block w-full"
                        x-model="password"
                        required
                        autofocus
                    />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button
                        type="button"
                        x-on:click="$dispatch('close')"
                    >
                        {{__('Cancel')}}
                    </x-secondary-button>

                    <x-primary-button type="submit">
                        {{__('Confirm')}}
                    </x-primary-button>
                </div>
            </div>
        </x-modal>

    </form>

</section>
