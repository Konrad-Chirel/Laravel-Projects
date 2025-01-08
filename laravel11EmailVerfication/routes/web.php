<?php

use App\Mail\Email;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send', function () {
    Mail::to('mdgkonrad@gmail.com')->send(new Email());
    return "Email sent";

});