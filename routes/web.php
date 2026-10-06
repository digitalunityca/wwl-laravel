<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::post('/contact', ContactController::class)->middleware('throttle:5,1')->name('contact.store');
