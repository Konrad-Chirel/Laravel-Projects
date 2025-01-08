<?php

use App\Http\Controllers\GoogleAuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('welcome');
})->name('home');
Route::get('/auth/google/redirect', [GoogleAuthController::class,'redirect']);
Route::get('/auth/google/callback', [GoogleAuthController::class,'callback']);
