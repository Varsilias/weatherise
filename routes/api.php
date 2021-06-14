<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ChangePasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    // 'middleware' => 'api',
    'prefix' => 'v1/auth'

], function ($router) {

	Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
	Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');

});

Route::prefix('v1')->middleware('api')->group(function () {
    Route::get('email/verify/{id}',  [VerificationController::class,'verify'])->name('verification.verify');
    Route::get('email/resend', [VerificationController::class,'resend'])->name('verification.resend');
    Route::post('sendPasswordResetLink', [ResetPasswordController::class, 'sendEmail']);
    Route::post('resetPassword', [ChangePasswordController::class, 'passwordResetProcess']);
});

Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::apiResource('location', LocationController::class);
});

