@php
$string = '^';

if (config('auth.password_validation.requires_lowercase_char')) {
    $string .= '(?=.*[a-z])';
}
if (config('auth.password_validation.requires_uppercase_char')) {
    $string .= '(?=.*[A-Z])';
}
if (config('auth.password_validation.requires_number')) {
    $string .= '(?=.*[0-9])';
}
if (config('auth.password_validation.requires_symbol')) {
    $string .= '(?=.*[\d\x])(?=.*[!$#%@&])';
}
if (config('auth.password_validation.min') && config('auth.password_validation.max')) {
    $string .= '.{'
        . (config('auth.password_validation.min') ?? '') . ','
        . (config('auth.password_validation.max') ?? '') . '}';
}

$string .= '$';

$formAction = (config('app.env') === 'production' || config('app.env') === 'staging') ? secure_url('api/auth/register') : url('api/auth/register');


@endphp

<x-guest-layout>
    <x-auth-card>
        <x-application-logo class="text-center text-4xl" style="color: {{ $branding->primary_color }}" logo="{{ $branding->logo }}" />
        <div id="container">
            <form method="POST" action="{{ $formAction }}" id="register" onsubmit="event.preventDefault(); return false;">

                @if (config('auth.external_id_label'))
                    <!-- External ID -->
                    <div class="mt-4">
                        <x-label for="external-id" :value="config('auth.external_id_label')" />
                        <x-input id="external-id" class="block mt-1 w-full" type="text" name="external_id" autofocus placeholder="Enter {{ config('auth.external_id_label') }}..."/>
                        <div class="mt-2 text-red-600 hidden" id="invalid-external-id">{{ config('auth.external_id_label') }} is required.</div>
                    </div>
                @endif

                @if (! config('app.generate_usernames'))
                <!-- First Name -->
                <div class="mt-4">
                    <x-label for="first-name" :value="__('First Name')" />
                    <x-input id="first-name" class="block mt-1 w-full" type="text" name="first_name" placeholder="Enter first name..." />
                    <div class="mt-2 text-red-600 hidden" id="invalid-first-name">First name is required and may only contain letters.</div>
                </div>

                <!-- Last Name -->
                <div class="mt-4">
                    <x-label for="last-name" :value="__('Last Name')" />
                    <x-input id="last-name" class="block mt-1 w-full" type="text" name="last_name" placeholder="Enter last name..." />
                    <div class="mt-2 text-red-600 hidden" id="invalid-last-name">Last name is required and may only contain letters.</div>
                </div>
            @endif

                <!-- Email Address -->
                <div class="mt-4">
                    <x-label for="email" :value="__('Email')" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $email)" placeholder="Enter email..."/>
                    <div class="mt-2 text-red-600 hidden" id="invalid-email">This field is required and must be a valid email address.</div>
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-label for="password" :value="__('Password')" />
                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" placeholder="Enter password..." data-password-rules="{{ $string }}" autocomplete="new-password" />
                    <div class="mt-2 text-red-600 hidden" id="invalid-password">This field is required. Password must be {{ config('auth.password_validation.min') . '-' . config('auth.password_validation.max'); }} characters and must contain at least 1 lowercase, uppercase, numeric and symbol characters.</div>
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="cpassword" placeholder="Enter password again..." autocomplete="new-password" />
                    <div class="mt-2 text-red-600 hidden" id="invalid-password_confirmation">The passwords entered do not match.</div>
                </div>
                <div id="error-summary-alert" class="hidden mt-4 text-red-600" role="alert">
                    <strong>Registration Failed</strong><br>
                    <ul id="error-summary-list"></ul>
                </div>
                <div class="mt-4">
                    <p class="text-sm">By clicking "Register" you are accepting the <a class="text-blue-600" href="{{ $branding->terms_url }}">Terms & Conditions</a> and <a class="text-blue-600" href="{{ $branding->privacy_url }}">Privacy Policy</a>.</p>
                </div>
                <div class="mt-4">
                    <x-button disabled id="submit" type="submit" style="
                        background: {{ $branding->primary_color}};
                        color: {{ \App\Classes\Helper::getContrastColor($branding->primary_color) }};
                    ">
                        <span id="spinner" class="hidden">
                            <svg class="animate-spin h-5 w-5 mr-3 inline-block" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>Processing...
                        </span>
                        <span id="register-text">{{ __('Register') }}</span>
                    </x-button>
                </div>
            </form>
            <div id="success-msg" class="hidden">
                <h2 class="text-center text-7xl py-10">Success!</h2>
                <p class="text-center mb-6 px-5">
                    You have registered. The Earnie app will become available for you to install when your trial begins. We will email you a link at that time.
                </p>
                {{-- <p class="text-center mb-6 px-5">Download the Earnie app on your phone.</p>
                <div class="flex flex-col items-center">
                    <div class="mb-4">
                        <a href={{ config('deeplinks.ios') }}>
                            <img src={{ asset('images/app-store-badge.svg')}} width=180>
                        </a>
                    </div>
                    <div>
                        <a href={{ config('deeplinks.android') }}>
                            <img src={{ asset('images/google-play-badge.png')}} width=180>
                        </a>
                    </div>
                </div> --}}
            </div>
            <div class="mt-12 text-center text-gray-500 text-sm">
                <a class="p-3" href="{{ $branding->terms_url }}">Terms & Conditions</a>
                <a class="p-3" href="{{ $branding->privacy_url }}">Privacy Policy</a>
                <a class="p-3" href="{{ config('app.support_url') }}">Contact Support</a>
            </div>
        </div>

    </x-auth-card>
</x-guest-layout>
