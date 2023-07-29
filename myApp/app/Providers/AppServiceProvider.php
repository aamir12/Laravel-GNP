<?php

namespace App\Providers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if (config('app.env') === 'production' || config('app.env') === 'staging') {
            URL::forceScheme('https');
        }

        Http::macro('kumulos', function () {
            return Http::withBasicAuth(
                config('services.kumulos.key'),
                config('services.kumulos.server_key')
            )->baseUrl(config('services.kumulos.url'));
        });

        PendingRequest::macro('kumulos', function () {
            return $this->withBasicAuth(
                config('services.kumulos.key'),
                config('services.kumulos.server_key')
            )->baseUrl(config('services.kumulos.url'));
        });
    }
}
