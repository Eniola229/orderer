<?php
use App\Http\Controllers\Marketer\Auth\LoginController as MarketerLoginController;
use App\Http\Controllers\Marketer\DashboardController as MarketerDashboard;
use App\Http\Controllers\Admin\MarketerController;

// ── Marketer portal ───────────────────────────────────────────────────────────
Route::prefix('marketer')->name('marketer.')->group(function () {

    // Guest only
    Route::middleware('guest.marketer')->group(function () {
        Route::get('/login',  [MarketerLoginController::class, 'showForm'])->name('login');
        Route::post('/login', [MarketerLoginController::class, 'login']);
    });

    // Authenticated marketer
    Route::middleware('auth.marketer')->group(function () {
        Route::post('/logout',           [MarketerLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard',         [MarketerDashboard::class, 'index'])->name('dashboard');
        Route::get('/profile',           [MarketerDashboard::class, 'profile'])->name('profile');
        Route::post('/generate-code',    [MarketerDashboard::class, 'generateCode'])->name('generate-code');
        Route::post('/regenerate-code',  [MarketerDashboard::class, 'regenerateCode'])->name('regenerate-code');
    });
});
