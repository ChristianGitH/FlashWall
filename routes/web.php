<?php

use Livewire\Volt\Volt;

/* Route for register, login, forgot password */
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/register', 'auth.register');
    Volt::route('/forgot-password', 'auth.forgot-password');
    Volt::route('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('/create-wall', 'walls.create-wall')->name('create-wall');
    Volt::route('/setup-wall/{wall}', 'walls.setup-wall')->name('setup-wall');
    Volt::route('/moderation/{wall}', 'moderation.moderation')->name('moderation');

});

Volt::route('/{wall}', 'images.create-image')->name('create-image');

// Autres routes accessibles à tous (si besoin)
Volt::route('/', 'index')->name('home');
Volt::route('/display/{wall}', 'displaywalls.slideshow-wrapper')->name('slideshow');


// Routes pour le changement de langue
Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});