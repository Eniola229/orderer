<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\Auth\LoginController    as SellerLoginController;
use App\Http\Controllers\Seller\Auth\RegisterController as SellerRegisterController;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\ServiceController;
use App\Http\Controllers\Seller\HouseController;
use App\Http\Controllers\Seller\ProfileController;

Route::prefix('seller')->name('seller.')->group(function () {

    // Guest only
    Route::middleware('guest.seller')->group(function () {
        Route::get('/login',     [SellerLoginController::class,    'showForm'])->name('login');
        Route::post('/login',    [SellerLoginController::class,    'login']);
        Route::get('/register',  [SellerRegisterController::class, 'showForm'])->name('register');
        Route::post('/register', [SellerRegisterController::class, 'register']);
        // Password reset
        Route::get('/password/reset',         [App\Http\Controllers\Seller\Auth\PasswordResetController::class, 'showForgotForm'])->name('password.request');
        Route::post('/password/email',        [App\Http\Controllers\Seller\Auth\PasswordResetController::class, 'sendResetLink'])->name('password.email');
        Route::get('/password/reset/{token}', [App\Http\Controllers\Seller\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
        Route::post('/password/reset',        [App\Http\Controllers\Seller\Auth\PasswordResetController::class, 'resetPassword'])->name('password.update');
    });

    Route::get('/password/reset', fn() => view('seller.auth.forgot-password'))
         ->name('password.request');

    // Authenticated seller
    Route::middleware('auth.seller')->group(function () {

        Route::post('/logout',   [SellerLoginController::class, 'logout'])->name('logout');
        Route::get('/pending',   fn() => view('seller.auth.pending'))->name('pending');
        Route::get('/dashboard', [DashboardController::class,   'index'])->name('dashboard');

        // Products
        Route::resource('products', ProductController::class);
        Route::delete('/products/image/{image}', [ProductController::class, 'deleteImage'])
             ->name('products.image.delete');

        // Services
        Route::get('/services',         [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create',  [ServiceController::class, 'create'])->name('services.create');
        Route::post('/services',        [ServiceController::class, 'store'])->name('services.store');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

        // Properties
        Route::get('/properties',               [HouseController::class, 'index'])->name('houses.index');
        Route::get('/properties/create',        [HouseController::class, 'create'])->name('houses.create');
        Route::post('/properties',              [HouseController::class, 'store'])->name('houses.store');
        Route::delete('/properties/{house}',    [HouseController::class, 'destroy'])->name('houses.destroy');

        // Profile
        Route::get('/profile',  [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile',  [ProfileController::class, 'update'])->name('profile.update');

        // Placeholder routes — built in later phases
        // Orders
        Route::get('/orders',              [App\Http\Controllers\Seller\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}',      [App\Http\Controllers\Seller\OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status',[App\Http\Controllers\Seller\OrderController::class, 'updateStatus'])->name('orders.status');

        // Wallet
        Route::get('/wallet',              [App\Http\Controllers\Seller\WalletController::class, 'index'])->name('wallet.index');
        Route::post('/wallet/topup',       [App\Http\Controllers\Seller\WalletController::class, 'topupWallet'])->name('wallet.topup');
        Route::post('/wallet/topup-ads',   [App\Http\Controllers\Seller\WalletController::class, 'topupAds'])->name('wallet.topup.ads');
        Route::get('/wallet/callback',     [App\Http\Controllers\Seller\WalletController::class, 'topupCallback'])->name('wallet.topup.callback');

        // Withdrawals
        Route::get('/withdrawals',         [App\Http\Controllers\Seller\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::get('/withdrawals/create',  [App\Http\Controllers\Seller\WithdrawalController::class, 'create'])->name('withdrawals.create');
        Route::post('/withdrawals',        [App\Http\Controllers\Seller\WithdrawalController::class, 'store'])->name('withdrawals.store');
        // Ads
        Route::get('/ads',                  [App\Http\Controllers\Seller\AdController::class, 'index'])->name('ads.index');
        Route::get('/ads/create',           [App\Http\Controllers\Seller\AdController::class, 'create'])->name('ads.create');
        Route::post('/ads',                 [App\Http\Controllers\Seller\AdController::class, 'store'])->name('ads.store');
        Route::delete('/ads/{ad}',          [App\Http\Controllers\Seller\AdController::class, 'destroy'])->name('ads.destroy');
        Route::put('/ads/{ad}/pause',       [App\Http\Controllers\Seller\AdController::class, 'pause'])->name('ads.pause');
        Route::put('/ads/{ad}/resume',      [App\Http\Controllers\Seller\AdController::class, 'resume'])->name('ads.resume');
        // Brand
        Route::get('/brand',            [App\Http\Controllers\Seller\BrandController::class, 'index'])->name('brand.index');
        Route::post('/brand',           [App\Http\Controllers\Seller\BrandController::class, 'store'])->name('brand.store');
        Route::put('/brand/{brand}',    [App\Http\Controllers\Seller\BrandController::class, 'update'])->name('brand.update');

        // Support
        Route::get('/support',                          [App\Http\Controllers\Seller\SupportController::class, 'index'])->name('support');
        Route::get('/support/create',                   [App\Http\Controllers\Seller\SupportController::class, 'create'])->name('support.create');
        Route::post('/support',                         [App\Http\Controllers\Seller\SupportController::class, 'store'])->name('support.store');
        Route::get('/support/{ticket}',                 [App\Http\Controllers\Seller\SupportController::class, 'show'])->name('support.show');
        Route::post('/support/{ticket}/reply',          [App\Http\Controllers\Seller\SupportController::class, 'reply'])->name('support.reply');

        // Notifications
        Route::get('/notifications',                    [App\Http\Controllers\Seller\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read',              [App\Http\Controllers\Seller\NotificationController::class, 'markRead'])->name('notifications.read');
        Route::put('/notifications/{notification}',     [App\Http\Controllers\Seller\NotificationController::class, 'markSingle'])->name('notifications.single');
    });
});