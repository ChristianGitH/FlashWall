<?php

/* Route for register, login, forgot password */
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\LanguageController;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::livewire('/login', 'auth.login')->name('login');
    Route::livewire('/register', 'auth.register')->name('register');
});
/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Email Verification Routes
    Route::livewire('/email/verify', 'auth.verify-email')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/profile')->with('status', 'Email verified successfully!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('resent', true);
    })->middleware(['throttle:6,1'])->name('verification.send');
    
    Route::livewire('/profile', 'users.profile')->name('profile');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/create-wall', 'walls.create-wall')->name('create-wall');
    Route::livewire('/setup-wall/{wall}', 'walls.setup-wall')->name('setup-wall');
    Route::livewire('/moderation/{wall}', 'moderation.moderation')->name('moderation');
    Route::livewire('/moderation-mobile/{wall}', 'moderation.moderation-mobile')->name('moderation-mobile');
});
/*
|--------------------------------------------------------------------------
| Static & language
|--------------------------------------------------------------------------
*/
// Language switcher route
Route::get('language/{locale}', [LanguageController::class, 'switch']);



/*
|--------------------------------------------------------------------------
| Public pages
|--------------------------------------------------------------------------
*/
// Home page - index/flashwall.app (must come BEFORE /{wall} route)
//Route::livewire('/', 'index');
Route::livewire('/display/{wall}', 'displaywalls.slideshow-wrapper', ['mode' => 'prod'])->name('slideshow');
Route::livewire('/display/{wall}/{mode}', 'displaywalls.slideshow-wrapper')->name('slideshow.mode');
Route::livewire('/plans', 'plans')->name('plans');

// Password reset routes, should be accissible for guests, and users
Route::livewire('/forgot-password', 'auth.forgot-password')->name('password.forgot');
Route::livewire('/reset-password/{token}', 'auth.reset-password')->name('password.reset');



/*
|--------------------------------------------------------------------------
| Static & translated pages
|--------------------------------------------------------------------------
*/

// Localized static page route.
// Example: /en/wedding or /fr/mariage
Route::get('/', [StaticPageController::class, 'show_home'])->name('home');
Route::get('{locale}/{slug}', [StaticPageController::class, 'show'])->name('static.page');

// Catch-all route for locale-less static page slugs.
// This ensures plain slugs like /legal or /mentions-legales are still
// redirected to the current application locale.
// Desactivated for now, as it can cause conflicts with dynamic wall routes (/{wall})
// Route::get('{page}', [StaticPageController::class, 'redirect']);

// Dynamic wall routes (catch {wall} parameter)
Route::livewire('{wall}', 'images.create-image')->name('create-image');
