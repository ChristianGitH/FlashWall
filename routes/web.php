<?php

use Livewire\Volt\Volt;

Volt::route('/', 'index');

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
    Volt::route('/moderation/{wall}', 'walls.moderation')->name('moderation');

});

Volt::route('/{slug}', 'images.create-image')->name('create-image');

// Autres routes accessibles à tous (si besoin)
Volt::route('/', 'index')->name('home');
