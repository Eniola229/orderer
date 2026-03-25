<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Buyer\Auth\RegisterController as BuyerRegisterController;
use App\Http\Controllers\Buyer\Auth\LoginController    as BuyerLoginController;

// -------------------------------------------------------
// STOREFRONT
// -------------------------------------------------------
Route::get('/', fn() => view('storefront.home'))->name('home');

// Korapay webhook — no CSRF, no auth
Route::post('/webhooks/korapay', [
    App\Http\Controllers\Seller\WalletController::class, 'webhook'
])->name('korapay.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Buyer auth
Route::middleware('guest')->group(function () {
    Route::get('/register',  [BuyerRegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [BuyerRegisterController::class, 'register']);
    Route::get('/login',     [BuyerLoginController::class,    'showForm'])->name('login');
    Route::post('/login',    [BuyerLoginController::class,    'login']);
});

Route::post('/logout', [BuyerLoginController::class, 'logout'])
     ->middleware('auth')
     ->name('logout');

// Password reset placeholder routes (filled in Phase 7)
Route::get('/password/reset', fn() => view('buyer.auth.forgot-password'))
     ->name('password.request');

// Buyer dashboard placeholder routes (filled Phase 7)
Route::middleware('auth')->prefix('account')->name('buyer.')->group(function () {
    Route::get('/dashboard', fn() => 'Coming Phase 7')->name('dashboard');
    Route::get('/orders',    fn() => 'Coming Phase 7')->name('orders');
    Route::get('/wishlist',  fn() => 'Coming Phase 7')->name('wishlist');
    Route::get('/wallet',    fn() => 'Coming Phase 7')->name('wallet');
    Route::get('/referral',  fn() => 'Coming Phase 7')->name('referral');
    Route::get('/support',   fn() => 'Coming Phase 7')->name('support');
    Route::get('/profile',   fn() => 'Coming Phase 7')->name('profile');
});

// Storefront placeholder routes
Route::get('/shop',                fn() => 'Coming Phase 7')->name('shop.index');
Route::get('/shop/{slug}',         fn() => 'Coming Phase 7')->name('shop.category');
Route::get('/brands',              fn() => 'Coming Phase 7')->name('brands.index');
Route::get('/services',            fn() => 'Coming Phase 7')->name('services.index');
Route::get('/properties',          fn() => 'Coming Phase 7')->name('houses.index');
Route::get('/rider',               fn() => 'Coming Phase 7')->name('rider.booking');
Route::get('/checkout',            fn() => 'Coming Phase 7')->name('checkout');
Route::get('/search',              fn() => 'Coming Phase 7')->name('search');
Route::get('/contact',             fn() => 'Coming Phase 7')->name('contact');
Route::post('/newsletter',         fn() => back())->name('newsletter.subscribe');

// -------------------------------------------------------
// SELLER
// -------------------------------------------------------
require __DIR__.'/seller.php';

// -------------------------------------------------------
// ADMIN
// -------------------------------------------------------
require __DIR__.'/admin.php';