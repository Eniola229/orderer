<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController  as AdminDashboard;

Route::prefix('admin')->name('admin.')->group(function () {

    // Guest only
    Route::middleware('guest.admin')->group(function () {
        Route::get('/login',  [AdminLoginController::class, 'showForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login']);
    });

    // Authenticated admin
    Route::middleware('auth.admin')->group(function () {
        Route::post('/logout',   [AdminLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboard::class,       'index'])->name('dashboard');
        // Ads management
        Route::get('/ads',                            [App\Http\Controllers\Admin\AdController::class, 'index'])->name('ads.index');
        Route::get('/ads/pending',                    [App\Http\Controllers\Admin\AdController::class, 'pending'])->name('ads.pending');
        Route::put('/ads/{ad}/approve',               [App\Http\Controllers\Admin\AdController::class, 'approve'])->name('ads.approve');
        Route::put('/ads/{ad}/reject',                [App\Http\Controllers\Admin\AdController::class, 'reject'])->name('ads.reject');
        Route::put('/ads/{ad}/suspend',               [App\Http\Controllers\Admin\AdController::class, 'suspend'])->name('ads.suspend');

        // Ad categories
        Route::get('/ads/categories',                 [App\Http\Controllers\Admin\AdController::class, 'categories'])->name('ads.categories');
        Route::post('/ads/categories',                [App\Http\Controllers\Admin\AdController::class, 'storeCategory'])->name('ads.categories.store');
        Route::put('/ads/categories/{category}/toggle',[App\Http\Controllers\Admin\AdController::class, 'toggleCategory'])->name('ads.categories.toggle');

        // Banner slots
        Route::get('/ads/slots',                      [App\Http\Controllers\Admin\AdController::class, 'slots'])->name('ads.slots');
        Route::post('/ads/slots',                     [App\Http\Controllers\Admin\AdController::class, 'storeSlot'])->name('ads.slots.store');
        Route::put('/ads/slots/{slot}/price',         [App\Http\Controllers\Admin\AdController::class, 'updateSlotPrice'])->name('ads.slots.price');
        Route::put('/ads/slots/{slot}/toggle',        [App\Http\Controllers\Admin\AdController::class, 'toggleSlot'])->name('ads.slots.toggle');

        // Admin placeholder routes (filled in later phases)
        Route::get('/sellers',        fn() => 'Phase 10')->name('sellers.index');
        Route::get('/sellers/pending',fn() => 'Phase 10')->name('sellers.pending');
        Route::get('/buyers',         fn() => 'Phase 10')->name('buyers.index');
        Route::get('/products',       fn() => 'Phase 10')->name('products.index');
        Route::get('/products/pending',fn()=> 'Phase 10')->name('products.pending');
        Route::get('/orders',         fn() => 'Phase 10')->name('orders.index');
        Route::get('/orders/disputes',fn() => 'Phase 10')->name('orders.disputes');
        Route::get('/finance/transactions', fn() => 'Phase 10')->name('finance.transactions');
        Route::get('/finance/escrow', fn() => 'Phase 10')->name('finance.escrow');
        Route::get('/withdrawals',    fn() => 'Phase 10')->name('withdrawals.index');
        Route::get('/categories',     fn() => 'Phase 10')->name('categories.index');
        Route::get('/categories/create', fn()=> 'Phase 10')->name('categories.create');
        Route::get('/brands',         fn() => 'Phase 10')->name('brands.index');
        Route::get('/support',        fn() => 'Phase 10')->name('support.index');
        Route::get('/support/open',   fn() => 'Phase 10')->name('support.open');
        Route::get('/admins',         fn() => 'Phase 10')->name('admins.index');
        Route::get('/admins/create',  fn() => 'Phase 10')->name('admins.create');
        Route::post('/admins',        fn() => 'Phase 10')->name('admins.store');
        Route::get('/logs',           fn() => 'Phase 10')->name('logs.index');
    });
});