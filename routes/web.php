<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Buyer\Auth\RegisterController  as BuyerRegisterController;
use App\Http\Controllers\Buyer\Auth\LoginController     as BuyerLoginController;
use App\Http\Controllers\Buyer\Auth\PasswordResetController as BuyerPasswordReset;
use App\Http\Controllers\Buyer\DashboardController      as BuyerDashboard;
use App\Http\Controllers\Buyer\OrderController          as BuyerOrders;
use App\Http\Controllers\Buyer\WalletController         as BuyerWallet;
use App\Http\Controllers\Buyer\WishlistController       as BuyerWishlist;
use App\Http\Controllers\Buyer\ReferralController       as BuyerReferral;
use App\Http\Controllers\Buyer\ProfileController        as BuyerProfile;
use App\Http\Controllers\Buyer\SupportController        as BuyerSupport;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

// -------------------------------------------------------
// STOREFRONT (public)
// -------------------------------------------------------
Route::get('/',                     [StorefrontController::class, 'home'])->name('home');
Route::get('/shop',                 [StorefrontController::class, 'shop'])->name('shop.index');
Route::get('/shop/{category}',      [StorefrontController::class, 'shopCategory'])->name('shop.category');
Route::get('/shop/{category}/{sub}',[StorefrontController::class, 'shopCategory'])->name('shop.subcategory');
Route::get('/product/{slug}',       [StorefrontController::class, 'product'])->name('product.show');
Route::get('/search',               [StorefrontController::class, 'search'])->name('search');
Route::get('/brands',               [StorefrontController::class, 'brands'])->name('brands.index');
Route::get('/brands/{slug}',        [StorefrontController::class, 'brandShow'])->name('brands.show');
Route::post('/newsletter',          [StorefrontController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');

// Ad click tracking & redirect
Route::get('/ads/{ad}/click', [App\Http\Controllers\AdClickController::class, 'click'])
     ->name('ads.click');

// Contact
Route::get('/contact',       [App\Http\Controllers\StorefrontController::class, 'contact'])->name('contact');
Route::post('/contact/send', [App\Http\Controllers\ContactController::class,    'send'])->name('contact.send');

// Rider booking (form submission)
Route::post('/rider/book',   [App\Http\Controllers\RiderBookingController::class, 'book'])
     ->middleware('auth')
     ->name('rider.book');

// Brand reviews
Route::post('/brands/{brand}/review',
    [App\Http\Controllers\BrandReviewController::class, 'store'])
    ->middleware('auth')
    ->name('brands.review');

// Houses (storefront)
Route::get('/properties',    [App\Http\Controllers\StorefrontController::class, 'houses'])->name('houses.index');
Route::get('properties/{slug}', [StorefrontController::class, 'housesshow'])->name('houses.show');
// Services (storefront)
Route::get('services', [App\Http\Controllers\StorefrontController::class, 'services'])->name('services.index');
Route::get('services/{slug}', [App\Http\Controllers\StorefrontController::class, 'serviceShow'])->name('services.show');
//Validate address
Route::post('/address/validate', function (\Illuminate\Http\Request $request, \App\Services\ShipbubbleService $shipbubble) {
    $request->validate([
        'name'    => ['required', 'string'],
        'phone'   => ['required', 'string'],
        'address' => ['required', 'string'],
        'country' => ['required', 'string'],
    ]);

    try {
        $result = $shipbubble->validateAddress([
            'name'    => $request->name,
            'email'   => $request->email ?? 'noreply@orderer.com',
            'phone'   => $request->phone,
            'address' => $request->address,
            'city'    => $request->city ?? $request->state,
            'state'   => $request->state ?? $request->city,
            'country' => $request->country,  // ← now dynamic
        ]);

        $addressCode = $result['data']['address_code'] ?? null;

        if (!$addressCode) {
            return response()->json([
                'success' => false,
                'message' => 'Could not validate this address. Please provide more detail.',
            ], 422);
        }

        return response()->json([
            'success'      => true,
            'address_code' => $addressCode,
            'address'      => $result['data']['address'] ?? $request->address,
        ]);

    } catch (\Exception $e) {
        $raw = $e->getMessage();
        $msg = null;
        if (preg_match('/\{.*\}/s', $raw, $m)) {
            $decoded = json_decode($m[0], true);
            $msg = $decoded['message'] ?? null;
        }

        return response()->json([
            'success' => false,
            'message' => $msg ?? 'Address validation failed. Please check and try again.',
        ], 422);
    }
})->name('address.validate');

// Cart (session-based, no auth required to add)
Route::post('/cart/add',    [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear',  [CartController::class, 'clear'])->name('cart.clear');

// Checkout (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/checkout',          [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/place',   [CheckoutController::class, 'place'])->name('checkout.place');
    Route::get('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
});

// Rider booking placeholder
Route::get('/rider',  fn() => view('storefront.rider'))->name('rider.booking');

// -------------------------------------------------------
// BUYER AUTH
// -------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/register',           [BuyerRegisterController::class, 'showForm'])->name('register');
    Route::post('/register',          [BuyerRegisterController::class, 'register']);
    Route::get('/login',              [BuyerLoginController::class,    'showForm'])->name('login');
    Route::post('/login',             [BuyerLoginController::class,    'login']);

    // Password reset
    Route::get('/password/reset',          [BuyerPasswordReset::class, 'showForgotForm'])->name('password.request');
    Route::post('/password/email',         [BuyerPasswordReset::class, 'sendResetLink'])->name('password.email');
    Route::get('/password/reset/{token}',  [BuyerPasswordReset::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset',         [BuyerPasswordReset::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [BuyerLoginController::class, 'logout'])
     ->middleware('auth')
     ->name('logout');

    Route::post('/rider/rates',   [App\Http\Controllers\RiderBookingController::class, 'getRates'])
         ->middleware('auth')
         ->name('rider.rates');
    Route::post('/rider/book',    [App\Http\Controllers\RiderBookingController::class, 'book'])
         ->middleware('auth')
         ->name('rider.book');
    Route::get('/rider/callback', [App\Http\Controllers\RiderBookingController::class, 'callback'])
         ->middleware('auth')
         ->name('rider.callback');
    Route::get('/rider/track/{booking}', [App\Http\Controllers\RiderBookingController::class, 'track'])
         ->middleware('auth')
         ->name('rider.track');

    // ── Checkout rates ───────────────────────────────────
    Route::post('/checkout/rates', [App\Http\Controllers\CheckoutController::class, 'getRates'])
         ->middleware('auth')
         ->name('checkout.rates');
         
// -------------------------------------------------------
// BUYER DASHBOARD (authenticated)
// -------------------------------------------------------
Route::middleware('auth')->prefix('account')->name('buyer.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [BuyerDashboard::class, 'index'])->name('dashboard');

    // Orders
    Route::get('/orders',                    [BuyerOrders::class, 'index'])->name('orders');
    Route::get('/orders/{order}',            [BuyerOrders::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/confirm',    [BuyerOrders::class, 'confirmDelivery'])->name('orders.confirm');
    // ── Rider booking ────────────────────────────────────
    Route::get('/rider',          fn() => view('storefront.rider'))->name('rider.booking');


    // Wallet
    Route::get('/wallet',           [BuyerWallet::class, 'index'])->name('wallet');
    Route::post('/wallet/topup',    [BuyerWallet::class, 'topup'])->name('wallet.topup');
    Route::get('/wallet/callback',  [BuyerWallet::class, 'callback'])->name('wallet.callback');

    // Wishlist
    Route::get('/wishlist',              [BuyerWishlist::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle',      [BuyerWishlist::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{wishlist}',[BuyerWishlist::class, 'remove'])->name('wishlist.remove');

    // Referral
    Route::get('/referral', [BuyerReferral::class, 'index'])->name('referral');

    // Profile
    Route::get('/profile',           [BuyerProfile::class, 'index'])->name('profile');
    Route::put('/profile',           [BuyerProfile::class, 'update'])->name('profile.update');
    Route::put('/profile/password',  [BuyerProfile::class, 'updatePassword'])->name('profile.password');

    // Support
    Route::get('/support',              [BuyerSupport::class, 'index'])->name('support');
    Route::get('/support/create',       [BuyerSupport::class, 'create'])->name('support.create');
    Route::post('/support',             [BuyerSupport::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}',     [BuyerSupport::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [BuyerSupport::class, 'reply'])->name('support.reply');

    // Bookings
    Route::get('/bookings',           [App\Http\Controllers\RiderBookingController::class, 'myBookings'])->name('bookings');
    Route::get('/bookings/{booking}', [App\Http\Controllers\RiderBookingController::class, 'showBooking'])->name('bookings.show');
    Route::get('/bookings/{booking}/track', [App\Http\Controllers\RiderBookingController::class, 'track'])->name('bookings.track');
});

// -------------------------------------------------------
// SELLER routes — loaded from routes/seller.php
// -------------------------------------------------------
require __DIR__ . '/seller.php';

// -------------------------------------------------------
// ADMIN routes — loaded from routes/admin.php
// -------------------------------------------------------
require __DIR__ . '/admin.php';

// Korapay webhook (no CSRF)
Route::post('/webhooks/korapay', [
    App\Http\Controllers\Seller\WalletController::class, 'webhook'
])->name('korapay.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
