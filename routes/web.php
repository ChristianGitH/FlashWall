<?php

use Livewire\Volt\Volt;

/* Route for register, login, forgot password */
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/register', 'auth.register');
    Volt::route('/forgot-password', 'auth.forgot-password');
    Volt::route('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
});
/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Volt::route('/create-wall', 'walls.create-wall')->name('create-wall');
    Volt::route('/setup-wall/{wall}', 'walls.setup-wall')->name('setup-wall');
    Volt::route('/moderation/{wall}', 'moderation.moderation')->name('moderation');

});

/*
|--------------------------------------------------------------------------
| Static & language
|--------------------------------------------------------------------------
*/

// Routes pour le changement de langue
Route::get('language/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'fr'])) {
        abort(404);
    }

    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});

// Localized Terms page (serve from language-prefixed URLs)
Route::get('{locale}/terms', function ($locale) {
    if (! in_array($locale, ['en', 'fr'])) {
        abort(404);
    }
    app()->setLocale($locale);
    return view('static.submitters_terms');
})->name('terms');

// Localized Legal page (serve from language-prefixed URLs)
Route::get('{locale}/legal', function ($locale) {
    if (! in_array($locale, ['en', 'fr'])) {
        abort(404);
    }
    app()->setLocale($locale);
    return view('static.legal');
})->name('legal');


// Keep a plain /terms URL pointing to the default language (redirect)
Route::redirect('/terms', '/en/terms');
Route::redirect('/legal', '/en/legal');


/*
|--------------------------------------------------------------------------
| Public pages
|--------------------------------------------------------------------------
*/
// Home page - index/flashwall.app (must come BEFORE /{wall} route)
Volt::route('/home', 'index')->name('home');
Volt::route('/', 'index');
Volt::route('/display/{wall}', 'displaywalls.slideshow-wrapper', ['mode' => 'prod'])->name('slideshow');
Volt::route('/display/{wall}/{mode}', 'displaywalls.slideshow-wrapper')->name('slideshow.mode');


// Dynamic wall routes (catch {wall} parameter)
Volt::route('/{wall}', 'images.create-image')->name('create-image');