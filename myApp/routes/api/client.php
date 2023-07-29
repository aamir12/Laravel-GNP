<?php

use App\Http\Controllers\Client\ClientKpiController;

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
|
| This file is where we define all our routes for registered clients (e.g.
| routes which are meant to be used by a first-party app like a mobile app).
|
*/

Route::prefix('kpi')->group(function () {
    Route::post('/create', [ClientKpiController::class, 'addKPI']);
});
