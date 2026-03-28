<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\BuyerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\HouseController;

Route::prefix('admin')->name('admin.')->group(function () {

    // Guest only
    Route::middleware('guest.admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login']);
    });

    // Authenticated admin
    Route::middleware('auth.admin')->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // ==================== Sellers ====================
        Route::get('/sellers', [SellerController::class, 'index'])->name('sellers.index');
        Route::get('/sellers/pending', [SellerController::class, 'pending'])->name('sellers.pending');
        Route::get('/sellers/{seller}', [SellerController::class, 'show'])->name('sellers.show');
        Route::put('/sellers/{seller}/approve', [SellerController::class, 'approve'])->name('sellers.approve');
        Route::put('/sellers/{seller}/reject', [SellerController::class, 'reject'])->name('sellers.reject');
        Route::put('/sellers/{seller}/suspend', [SellerController::class, 'suspend'])->name('sellers.suspend');
        Route::put('/sellers/{seller}/unsuspend', [SellerController::class, 'unsuspend'])->name('sellers.unsuspend');
        Route::put('/sellers/{seller}/wallet', [SellerController::class, 'adjustWallet'])->name('sellers.wallet');

        // ==================== Services ====================
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/pending', [ServiceController::class, 'pending'])->name('services.pending');
        Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
        Route::put('/services/{service}/approve', [ServiceController::class, 'approve'])->name('services.approve');
        Route::put('/services/{service}/reject', [ServiceController::class, 'reject'])->name('services.reject');
        Route::put('/services/{service}/suspend', [ServiceController::class, 'suspend'])->name('services.suspend');

        // ==================== Houses ====================
        Route::get('/houses', [HouseController::class, 'index'])->name('houses.index');
        Route::get('/houses/pending', [HouseController::class, 'pending'])->name('houses.pending');
        Route::get('/houses/{house}', [HouseController::class, 'show'])->name('houses.show');
        Route::put('/houses/{house}/approve', [HouseController::class, 'approve'])->name('houses.approve');
        Route::put('/houses/{house}/reject', [HouseController::class, 'reject'])->name('houses.reject');
        Route::put('/houses/{house}/suspend', [HouseController::class, 'suspend'])->name('houses.suspend');

        // ==================== Buyers ====================
        Route::get('/buyers', [BuyerController::class, 'index'])->name('buyers.index');
        Route::get('/buyers/{user}', [BuyerController::class, 'show'])->name('buyers.show');
        Route::put('/buyers/{user}/wallet', [BuyerController::class, 'adjustWallet'])->name('buyers.wallet');
        Route::put('/buyers/{user}/suspend', [BuyerController::class, 'suspend'])->name('buyers.suspend');
        Route::put('/buyers/{user}/unsuspend', [BuyerController::class, 'unsuspend'])->name('buyers.unsuspend');

        // ==================== Products ====================
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/pending', [ProductController::class, 'pending'])->name('products.pending');
        Route::put('/products/{product}/approve', [ProductController::class, 'approve'])->name('products.approve');
        Route::put('/products/{product}/reject', [ProductController::class, 'reject'])->name('products.reject');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::put('/products/{product}/suspend', [ProductController::class, 'suspend'])->name('products.suspend');

        // ==================== Orders ====================
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/disputes', [OrderController::class, 'disputes'])->name('orders.disputes');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/complete', [OrderController::class, 'forceComplete'])->name('orders.complete');
        Route::put('/orders/{order}/refund', [OrderController::class, 'forceRefund'])->name('orders.refund');

        // ==================== Finance ====================
        Route::get('/finance/transactions', [FinanceController::class, 'transactions'])->name('finance.transactions');
        Route::get('/finance/escrow', [FinanceController::class, 'escrow'])->name('finance.escrow');
        Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::put('/withdrawals/{wd}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::put('/withdrawals/{wd}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');

        // ==================== Categories & Brands ====================
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::post('/categories/{category}/sub', [CategoryController::class, 'storeSubcategory'])->name('categories.subcategory');
        
        Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
        Route::put('/brands/{brand}/suspend', [BrandController::class, 'suspend'])->name('brands.suspend');
        Route::put('/brands/{brand}/activate', [BrandController::class, 'activate'])->name('brands.activate');

        // ==================== Support ====================
        Route::get('/support', [SupportController::class, 'index'])->name('support.index');
        Route::get('/support/open', [SupportController::class, 'open'])->name('support.open');
        Route::get('/support/{ticket}', [SupportController::class, 'show'])->name('support.show');
        Route::post('/support/{ticket}/reply', [SupportController::class, 'reply'])->name('support.reply');
        Route::put('/support/{ticket}/resolve', [SupportController::class, 'resolve'])->name('support.resolve');
        Route::put('/support/{ticket}/close', [SupportController::class, 'close'])->name('support.close');

        // ==================== Admin Management ====================
        Route::get('/admins', [AdminController::class, 'index'])->name('admins.index');
        Route::get('/admins/create', [AdminController::class, 'create'])->name('admins.create');
        Route::post('/admins', [AdminController::class, 'store'])->name('admins.store');
        Route::get('/admins/{adminUser}/edit', [AdminController::class, 'edit'])->name('admins.edit');
        Route::put('/admins/{adminUser}', [AdminController::class, 'update'])->name('admins.update');
        Route::put('/admins/{adminUser}/suspend', [AdminController::class, 'suspend'])->name('admins.suspend');
        Route::put('/admins/{adminUser}/activate', [AdminController::class, 'activate'])->name('admins.activate');

        // ==================== Ads ====================
        Route::get('/ads', [AdController::class, 'index'])->name('ads.index');
        Route::get('/ads/{ad}', [AdController::class, 'show'])->name('ads.show');
        Route::get('/ads/pending', [AdController::class, 'pending'])->name('ads.pending');
        Route::put('/ads/{ad}/approve', [AdController::class, 'approve'])->name('ads.approve');
        Route::put('/ads/{ad}/reject', [AdController::class, 'reject'])->name('ads.reject');
        Route::put('/ads/{ad}/suspend', [AdController::class, 'suspend'])->name('ads.suspend');
        Route::get('/ads/categories', [AdController::class, 'categories'])->name('ads.categories');
        Route::post('/ads/categories', [AdController::class, 'storeCategory'])->name('ads.categories.store');
        Route::put('/ads/categories/{category}/toggle', [AdController::class, 'toggleCategory'])->name('ads.categories.toggle');
        Route::get('/ads/slots', [AdController::class, 'slots'])->name('ads.slots');
        Route::post('/ads/slots', [AdController::class, 'storeSlot'])->name('ads.slots.store');
        Route::put('/ads/slots/{slot}/price', [AdController::class, 'updateSlotPrice'])->name('ads.slots.price');
        Route::put('/ads/slots/{slot}/toggle', [AdController::class, 'toggleSlot'])->name('ads.slots.toggle');

        // Admin Profile
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.update-password');

        // ==================== Logs ====================
        Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    });
});