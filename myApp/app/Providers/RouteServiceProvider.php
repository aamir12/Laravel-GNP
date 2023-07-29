<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')->middleware('Localization')->group( function() {
                Route::prefix('auth')
                    ->group(base_path('routes/api/auth.php'));

                Route::prefix('user')
                    ->middleware('auth:api', 'cors', 'user')
                    ->group(base_path('routes/api/user.php'));

                Route::prefix('admin')
                    ->middleware('auth:api', 'cors', 'admin')
                    ->group(base_path('routes/api/admin.php'));

                Route::prefix('client')
                    ->middleware('client.secure')
                    ->group(base_path('routes/api/client.php'));
            });

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
