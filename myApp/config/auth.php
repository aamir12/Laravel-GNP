<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

    /*
    |--------------------------------------------------------------------------
    | Password Validation Rules
    |--------------------------------------------------------------------------
    |
    | Here you may define the validation rules that will be applied during
    | registration. This affects the register API, the change password API
    | and also the client-side validation applied to the registration form.
    |
    */

    'password_validation' => [
        'min' => env('PASSWORD_VALIDATION_MIN'),
        'max' => env('PASSWORD_VALIDATION_MAX'),
        'requires_lowercase_char' => env('PASSWORD_REQUIRES_LOWERCASE_CHAR'),
        'requires_uppercase_char' => env('PASSWORD_REQUIRES_UPPERCASE_CHAR'),
        'requires_number' => env('PASSWORD_REQUIRES_NUMBER'),
        'requires_symbol' => env('PASSWORD_REQUIRES_SYMBOL'),
    ],

    'password_expiry' => env('PASSWORD_EXPIRE_SECURITY', false),
    'password_expiry_days' => env('EXPIRE_PASSWORD_IN_DAYS', 90),
    'login_attempts_before_lock' => env('LOGIN_ATTEMPTS_BEFORE_LOCK', 4),
    'password_lock_time_in_minutes' => env('PASSWORD_LOCK_TIME_IN_MINUTES', 1),

    /*
    |--------------------------------------------------------------------------
    | External ID Label
    |--------------------------------------------------------------------------
    |
    | The label that should be used for the external ID field on the
    | registration form.
    |
    */
    'external_id_label' => env('EXTERNAL_ID_LABEL'),

    /*
    |--------------------------------------------------------------------------
    | External ID Account Activation
    |--------------------------------------------------------------------------
    |
    | If this setting is true, the external_id user field will be used instead
    | of activation_code for activating new users.
    |
    */
    'external_id_account_activation' => env('EXTERNAL_ID_ACCOUNT_ACTIVATION', false),
];
