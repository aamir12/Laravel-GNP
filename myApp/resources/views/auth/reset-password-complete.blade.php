<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4 text-xl" :errors="$errors" />

        @unless ($errors->any())
            <div class="fill-current text-gray-500 text-3xl">
                {{ __('Password Reset Successful!') }}
            </div>
        @endunless
    </div>
</x-guest-layout>
