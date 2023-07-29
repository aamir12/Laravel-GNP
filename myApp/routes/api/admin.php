<?php

use App\Http\Controllers\Admin\AdminAchievementController;
use App\Http\Controllers\Admin\AdminCompetitionController;
use App\Http\Controllers\Admin\AdminLeagueController;
use App\Http\Controllers\Admin\AdminKPIController;
use App\Http\Controllers\Admin\AdminPrizeController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\BrandingController;
use App\Http\Controllers\Admin\DeliverableController;
use App\Http\Controllers\Admin\EmailConfigController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WinnerController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| This file is where we define all our routes for the admin API.
|
*/

Route::prefix('users')
    ->controller(UserManagementController::class)
    ->group(function () {
        Route::post('/bulk-create', 'bulkCreate');
        Route::post('/bulk-update', 'bulkUpdate');
        Route::get('/list', 'list');
        Route::get('/get', 'get');
        Route::post('/delete', 'delete');
    });

Route::prefix('groups')
    ->controller(GroupController::class)
    ->group(function () {
        Route::post('/create', 'create');
        Route::post('/update', 'update');
        Route::post('/delete', 'delete');
        Route::get('/list', 'list');
        Route::get('/get', 'get');
        Route::post('/add-users', 'addUser');
        Route::post('/remove-users', 'removeUser');
    });

Route::prefix('competitions')
    ->controller(AdminCompetitionController::class)
    ->group(function () {
        Route::post('/create', 'create');
        Route::post('/update', 'update');
        Route::get('/list', 'list');
        Route::get('/entrants', 'getWithEntrants');
        Route::get('/get', 'get');
    });

Route::prefix('prizes')
    ->controller(AdminPrizeController::class)
    ->group(function () {
        Route::post('/create', 'create');
        Route::post('/bulk-create', 'bulkCreate');
        Route::post('/update', 'update');
        Route::get('/list', 'list');
        Route::get('/get', 'get');
        Route::post('/delete', 'delete');
    });

Route::get('/winners/list', [WinnerController::class, 'list']);

Route::prefix('leagues')
    ->controller(AdminLeagueController::class)
    ->group(function () {
        Route::post('/create', 'create');
        Route::post('/update', 'update');
        Route::get('/get', 'get');
        Route::get('/list', 'list');
        Route::post('/delete', 'delete');
    });

Route::prefix('achievements')
    ->controller(AdminAchievementController::class)
    ->group(function () {
        Route::post('/create', 'create');
        Route::post('/update', 'update');
        Route::get('/get', 'get');
        Route::get('/list', 'list');
        Route::post('/delete', 'delete');
    });

Route::prefix('emails')->group(function () {
    Route::post('/send', [EmailConfigController::class, 'sendEmail']);
});

Route::prefix('kpi')
    ->controller(AdminKPIController::class)
    ->group(function () {
        Route::post('/create', 'create');
        Route::get('/list', 'list');
    });

Route::prefix('email-config')
    ->controller(EmailConfigController::class)
    ->group(function () {
        Route::get('/get', 'get');
        Route::get('/list', 'list');
        Route::post('/update', 'update');
    });

Route::prefix('branding')
    ->controller(BrandingController::class)
    ->group(function () {
        Route::get('/get', 'get');
        Route::post('/update', 'update');
    });

Route::prefix('deliverables')
    ->controller(DeliverableController::class)
    ->group(function () {
        Route::get('/list', 'list');
        Route::get('/get', 'get');
        Route::post('/update', 'update');
    });

Route::post('/profile/update', [AdminProfileController::class, 'update']);

Route::prefix('transactions')
    ->controller(AdminTransactionController::class)
    ->group(function () {
        Route::get('/list', 'list');
        Route::post('/update', 'update');
        Route::post('/bulk-update', 'bulkUpdate');
    });