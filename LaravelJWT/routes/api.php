<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
  Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 */

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class,'login']);
    Route::post('register', [AuthController::class,'register']);
    Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class,'reset'])->name('password.reset');

    Route::middleware(['auth:api'])->group(function() {
        Route::post('refresh', [AuthController::class,'refresh']);
        Route::post('me', [AuthController::class,'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

# Looking to send emails in production? Check out our Email API/SMTP product!
