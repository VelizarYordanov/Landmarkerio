<?php

use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/google-autocomplete', function () {
    return view('googleAutocomplete');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/get-user-id', [App\Http\Controllers\UserController::class, 'getCurrentUserId']);
});

Route::post('/favourite-places', [App\Http\Controllers\FavouritePlaceController::class, 'store']);

Route::get('profile', function(){
    return view('user/profile');
})->name('profile');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
