<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GenerateUsernameController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| This file is where we define all our routes related to authentication for
| both admin and regular users.
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verify/{token}', [AuthController::class, 'verifyUser']);
Route::get('/generate-username', [GenerateUsernameController::class, 'generateUsername']);

Route::prefix('password')->group(function () {
    Route::post('/forgot', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth:api', 'cors')->group(function () {
    Route::get('/me', [AuthController::class, 'loggedInUser']);
    Route::post('/password/change', [AuthController::class, 'changePassword']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
