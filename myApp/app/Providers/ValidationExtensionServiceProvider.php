<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidationExtensionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('valid_parent_id', 'App\Classes\Validators\GroupValidator@validateParentId');
        Validator::extend('valid_user_external_id', 'App\Classes\Validators\UserValidator@validateUserExternalId');
        Validator::extendDependent('not_present_with', 'App\Classes\Validators\NotPresentWithValidator@validateNotPresentWith');
        Validator::extend('date_multi_format', 'App\Classes\Validators\DateMultiFormatValidator@validateDateFormats');
    }
}
