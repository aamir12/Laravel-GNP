<?php

use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Other\ImageController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/healthz', function () {
    return response('ok');
});

Route::get('/register', [RegistrationController::class, 'index']);
Route::get('image/{filename}', [ImageController::class, 'getImage']);

Route::get('/password/forgot', [PasswordResetLinkController::class, 'create']);

// require __DIR__.'/auth.php';
