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

// Home page - index/flashwall.app (must come BEFORE /{wall} route)
Volt::route('/home', 'index')->name('home');

// Dynamic wall routes (catch {wall} parameter)
Volt::route('/{wall}', 'images.create-image')->name('create-image');

// Autres routes accessibles à tous (si besoin)
Volt::route('/', 'index');
Volt::route('/display/{wall}', 'displaywalls.slideshow-wrapper', ['mode' => 'prod'])->name('slideshow');
Volt::route('/display/{wall}/{mode}', 'displaywalls.slideshow-wrapper')->name('slideshow.mode');


// Localized Terms page (serve from language-prefixed URLs)
Route::get('{locale}/terms', function ($locale) {
    if (! in_array($locale, ['en', 'fr'])) {
        abort(404);
    }
    app()->setLocale($locale);
    return view('submitters_terms');
})->name('terms');

// Keep a plain /terms URL pointing to the default language (redirect)
Route::redirect('/terms', '/en/terms');



// Routes pour le changement de langue
Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});