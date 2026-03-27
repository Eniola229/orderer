<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\Auth\LoginController         as SellerLoginController;
use App\Http\Controllers\Seller\Auth\RegisterController      as SellerRegisterController;
use App\Http\Controllers\Seller\Auth\PasswordResetController as SellerPasswordReset;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\ServiceController;
use App\Http\Controllers\Seller\HouseController;
use App\Http\Controllers\Seller\ProfileController;
use App\Http\Controllers\Seller\OrderController          as SellerOrderController;
use App\Http\Controllers\Seller\WalletController         as SellerWalletController;
use App\Http\Controllers\Seller\WithdrawalController;
use App\Http\Controllers\Seller\AdController             as SellerAdController;
use App\Http\Controllers\Seller\BrandController;
use App\Http\Controllers\Seller\SupportController        as SellerSupportController;
use App\Http\Controllers\Seller\NotificationController   as SellerNotificationController;

Route::prefix('seller')->name('seller.')->group(function () {

    // ── Guest only ──────────────────────────────────────────
    Route::middleware('guest.seller')->group(function () {
        Route::get('/login',     [SellerLoginController::class,    'showForm'])->name('login');
        Route::post('/login',    [SellerLoginController::class,    'login']);
        Route::get('/register',  [SellerRegisterController::class, 'showForm'])->name('register');
        Route::post('/register', [SellerRegisterController::class, 'register']);

        // Password reset
        Route::get('/password/reset',          [SellerPasswordReset::class, 'showForgotForm'])->name('password.request');
        Route::post('/password/email',         [SellerPasswordReset::class, 'sendResetLink'])->name('password.email');
        Route::get('/password/reset/{token}',  [SellerPasswordReset::class, 'showResetForm'])->name('password.reset');
        Route::post('/password/reset',         [SellerPasswordReset::class, 'resetPassword'])->name('password.update');
    });

    // ── Authenticated seller ────────────────────────────────
    Route::middleware('auth.seller')->group(function () {

        Route::post('/logout',   [SellerLoginController::class, 'logout'])->name('logout');
        Route::get('/pending',   fn() => view('seller.auth.pending'))->name('pending');

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Products
        Route::resource('products', ProductController::class);
        Route::delete('/products/image/{image}',
            [ProductController::class, 'deleteImage'])->name('products.image.delete');

        // Services
        Route::get('/services',               [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create',        [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services',              [ServiceController::class, 'store'])->name('services.store');
        Route::delete('/services/{service}',  [ServiceController::class, 'destroy'])->name('services.destroy');

        // Properties
        Route::get('/properties',             [HouseController::class, 'index'])->name('houses.index');
        Route::get('/properties/create',      [HouseController::class, 'create'])->name('houses.create');
        Route::post('/properties',            [HouseController::class, 'store'])->name('houses.store');
        Route::delete('/properties/{house}',  [HouseController::class, 'destroy'])->name('houses.destroy');

        // Profile
        Route::get('/profile',  [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile',  [ProfileController::class, 'update'])->name('profile.update');

        // Orders
        Route::get('/orders',                    [SellerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}',            [SellerOrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status',     [SellerOrderController::class, 'updateStatus'])->name('orders.status');

        // Wallet
        Route::get('/wallet',            [SellerWalletController::class, 'index'])->name('wallet.index');
        Route::post('/wallet/topup',     [SellerWalletController::class, 'topupWallet'])->name('wallet.topup');
        Route::post('/wallet/topup-ads', [SellerWalletController::class, 'topupAds'])->name('wallet.topup.ads');
        Route::get('/wallet/callback',   [SellerWalletController::class, 'topupCallback'])->name('wallet.topup.callback');

        // Withdrawals
        Route::get('/withdrawals',         [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::get('/withdrawals/create',  [WithdrawalController::class, 'create'])->name('withdrawals.create');
        Route::post('/withdrawals',        [WithdrawalController::class, 'store'])->name('withdrawals.store');
        Route::post('/withdrawals/banks', [WithdrawalController::class, 'getBanks'])->name('withdrawals.banks');
        Route::post('/withdrawals/resolve-account', [WithdrawalController::class, 'resolveAccount'])->name('withdrawals.resolve-account');

        // Ads
        Route::get('/ads',               [SellerAdController::class, 'index'])->name('ads.index');
        Route::get('/ads/create',        [SellerAdController::class, 'create'])->name('ads.create');
        Route::post('/ads',              [SellerAdController::class, 'store'])->name('ads.store');
        Route::delete('/ads/{ad}',       [SellerAdController::class, 'destroy'])->name('ads.destroy');
        Route::put('/ads/{ad}/pause',    [SellerAdController::class, 'pause'])->name('ads.pause');
        Route::put('/ads/{ad}/resume',   [SellerAdController::class, 'resume'])->name('ads.resume');

        // Brand
        Route::get('/brand',             [BrandController::class, 'index'])->name('brand.index');
        Route::post('/brand',            [BrandController::class, 'store'])->name('brand.store');
        Route::put('/brand/{brand}',     [BrandController::class, 'update'])->name('brand.update');

        // Support
        Route::get('/support',                        [SellerSupportController::class, 'index'])->name('support');
        Route::get('/support/create',                 [SellerSupportController::class, 'create'])->name('support.create');
        Route::post('/support',                       [SellerSupportController::class, 'store'])->name('support.store');
        Route::get('/support/{ticket}',               [SellerSupportController::class, 'show'])->name('support.show');
        Route::post('/support/{ticket}/reply',        [SellerSupportController::class, 'reply'])->name('support.reply');

        // Notifications
        Route::get('/notifications',                  [SellerNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read',            [SellerNotificationController::class, 'markRead'])->name('notifications.read');
        Route::put('/notifications/{notification}',   [SellerNotificationController::class, 'markSingle'])->name('notifications.single');

    });
});
