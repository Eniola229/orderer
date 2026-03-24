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
        // All admin modules added Phase 10
    });
});