<?php

/* Route for register, login, forgot password */
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::livewire('/login', 'auth.login')->name('login');
    Route::livewire('/register', 'auth.register');
    Route::livewire('/forgot-password', 'auth.forgot-password');
    Route::livewire('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
});
/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::livewire('/create-wall', 'walls.create-wall')->name('create-wall');
    Route::livewire('/setup-wall/{wall}', 'walls.setup-wall')->name('setup-wall');
    Route::livewire('/moderation/{wall}', 'moderation.moderation')->name('moderation');

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
Route::livewire('/home', 'index')->name('home');
Route::livewire('/', 'index');
Route::livewire('/display/{wall}', 'displaywalls.slideshow-wrapper', ['mode' => 'prod'])->name('slideshow');
Route::livewire('/display/{wall}/{mode}', 'displaywalls.slideshow-wrapper')->name('slideshow.mode');


// Dynamic wall routes (catch {wall} parameter)
Route::livewire('/{wall}', 'images.create-image')->name('create-image');