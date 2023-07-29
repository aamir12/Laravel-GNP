<?php

use App\Http\Controllers\User\AchievementController;
use App\Http\Controllers\User\CompetitionController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\LeagueController;
use App\Http\Controllers\User\KPIController;
use App\Http\Controllers\User\PrizeController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ScoreController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\UserAddressController;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| This file is where we define all our routes for the user API.
|
*/

Route::get('/scores/get', [ScoreController::class, 'getScore']);

Route::prefix('competitions')
    ->controller(CompetitionController::class)
    ->group(function () {
        Route::post('/enter', 'enterCompetition');
        Route::post('/reveal', 'revealCompetition');
        Route::get('/list', 'listCompetitions');
    });

Route::prefix('lotteries')
    ->controller(CompetitionController::class)
    ->group(function () {
        Route::get('/summary', 'lotterySummary');
        Route::post('/enter', 'enterLottery');
        Route::get('/list', 'listLotteries');
        Route::get('/list/open', 'listOpenLotteries');
        Route::get('/list/running', 'listRunningLotteries');
        Route::get('/list/closed', 'listClosedLotteries');
        Route::get('/list/unrevealed', 'listUnrevealedLotteries');
    });

Route::prefix('leagues')
    ->controller(LeagueController::class)
    ->group(function () {
        Route::post('/leave', 'leaveLeague');
        Route::post('/create-private', 'createPrivateLeague');
        Route::post('/invites/send', 'inviteToLeague');
        Route::post('/invites/accept', 'acceptLeagueInvite');
        Route::post('/invites/reject', 'rejectLeagueInvite');
        Route::post('/update-private', 'updatePrivateLeague');
        Route::get('/get', 'getUserLeague');
        Route::get('/list', 'listUserLeagues');
        Route::post('/shut-down', 'shutDownPrivateLeague');
    });

Route::prefix('prizes')->group(function () {
    Route::post('/claim', [PrizeController::class, 'claim']);
});

Route::prefix('achievements')->group(function () {
    Route::post('/claim', [AchievementController::class, 'claim']);
});

Route::get('/dashboard', [DashboardController::class, 'dashboard']);
Route::post('/balance/withdraw', [TransactionController::class, 'userBalanceWithdraw']);
Route::get('/balance', [TransactionController::class, 'getBalance']);

Route::prefix('profile')->group(function () {
    Route::post('/update', [ProfileController::class, 'update']);
});

Route::prefix('kpi')->group(function () {
    Route::get('/list', [KPIController::class, 'listKPI']);
});

Route::prefix('address')
    ->controller(UserAddressController::class)
    ->group(function () {
        Route::post('/create', 'create');
        Route::get('/get', 'get');
        Route::get('/list', 'list');
        Route::post('/update', 'update');
        Route::post('/delete', 'delete');
    });