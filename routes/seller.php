<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\Auth\LoginController    as SellerLoginController;
use App\Http\Controllers\Seller\Auth\RegisterController as SellerRegisterController;
use App\Http\Controllers\Seller\DashboardController     as SellerDashboard;

Route::prefix('seller')->name('seller.')->group(function () {

    // Guest only
    Route::middleware('guest.seller')->group(function () {
        Route::get('/login',     [SellerLoginController::class,    'showForm'])->name('login');
        Route::post('/login',    [SellerLoginController::class,    'login']);
        Route::get('/register',  [SellerRegisterController::class, 'showForm'])->name('register');
        Route::post('/register', [SellerRegisterController::class, 'register']);
    });

    // Pending approval page (authenticated but not yet approved)
    Route::get('/pending', fn() => view('seller.auth.pending'))->name('pending');

    // Authenticated + approved seller
    Route::middleware('auth.seller')->group(function () {
        Route::post('/logout',   [SellerLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [SellerDashboard::class,       'index'])->name('dashboard');
        // Products, orders, wallet, ads — added Phase 4 onwards
    });
});