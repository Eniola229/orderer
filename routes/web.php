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
use App\Http\Controllers\LegalController;
use App\Http\Controllers\OgImageController;
use App\Http\Controllers\BuyNowController;


// -------------------------------------------------------
// STOREFRONT (public)
// -------------------------------------------------------

Route::get('/og-image/{type}/{slug}', [OgImageController::class, 'generate'])->name('og.image');

Route::get('/',                     [StorefrontController::class, 'home'])->name('home');
Route::get('/shop',                 [StorefrontController::class, 'shop'])->name('shop.index');
Route::get('/shop/{category}',      [StorefrontController::class, 'shopCategory'])->name('shop.category');
Route::get('/shop/{category}/{sub}',[StorefrontController::class, 'shopCategory'])->name('shop.subcategory');
Route::get('/product/{slug}',       [StorefrontController::class, 'product'])->name('product.show');
Route::get('/search',               [StorefrontController::class, 'search'])->name('search');
Route::get('/brands',               [StorefrontController::class, 'brands'])->name('brands.index');
Route::get('/brands/{slug}',        [StorefrontController::class, 'brandShow'])->name('brands.show');
Route::post('/newsletter',          [StorefrontController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');
//Waitlist
Route::get('/waitlist', fn() => view('waitlist'))->name('waitlist');

// ── Legal Pages ───────────────────────────────────────────────────────────────
Route::prefix('legal')->name('legal.')->group(function () {
    Route::get('/terms-and-conditions',    [LegalController::class, 'terms'])->name('terms');
    Route::get('/privacy-policy',          [LegalController::class, 'privacy'])->name('privacy');
    Route::get('/refund-policy',           [LegalController::class, 'refundPolicy'])->name('refund');
    Route::get('/seller-terms',            [LegalController::class, 'sellerTerms'])->name('seller-terms');
    Route::get('/buyer-terms',             [LegalController::class, 'buyerTerms'])->name('buyer-terms');
    Route::get('/shipping-policy',         [LegalController::class, 'shippingPolicy'])->name('shipping');
    Route::get('/cookie-policy',           [LegalController::class, 'cookiePolicy'])->name('cookies');
    Route::get('/aml-policy',              [LegalController::class, 'amlPolicy'])->name('aml');
    Route::get('/acceptable-use-policy',   [LegalController::class, 'acceptableUse'])->name('acceptable-use');
    Route::get('/disclaimer',              [LegalController::class, 'disclaimer'])->name('disclaimer');
});

//Review a product
Route::post('/product/{product}/review', [StorefrontController::class, 'submitReview'])->name('product.review')->middleware('auth');



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
            'email'   => $request->email ?? 'noreply@ordererweb.com',
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
//Cart
Route::get('/cart/count',   [CartController::class, 'count'])  ->name('cart.count');
Route::get('/cart/sidebar', [CartController::class, 'sidebar'])->name('cart.sidebar');
Route::post('/cart/add',    [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear',  [CartController::class, 'clear'])->name('cart.clear');
 
// Checkout (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/checkout',          [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/place',   [CheckoutController::class, 'place'])->name('checkout.place');
    Route::get('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');

    //FOR BUY NOW
    Route::get('/buy-now/callback', [BuyNowController::class, 'callback'])->name('buy-now.callback');

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
    Route::get('/password/reset',          [BuyerPasswordReset::class, 'showForgotForm'])->name('front.password.request');
    Route::post('/password/email',         [BuyerPasswordReset::class, 'sendResetLink'])->name('front.password.email');
    Route::get('/password/reset/{token}',  [BuyerPasswordReset::class, 'showResetForm'])->name('front.password.reset');
    Route::post('/password/reset',         [BuyerPasswordReset::class, 'resetPassword'])->name('front.password.update');
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
    Route::put('orders/{order}/confirm-item', [BuyerOrders::class, 'confirmItem'])->name('orders.confirm.item');
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
    Route::get('/support/{ticket}/messages', [BuyerSupport::class, 'messages'])->name('support.messages');

    // Bookings
    Route::get('/bookings',           [App\Http\Controllers\RiderBookingController::class, 'myBookings'])->name('bookings');
    Route::get('/bookings/{booking}', [App\Http\Controllers\RiderBookingController::class, 'showBooking'])->name('bookings.show');
    Route::get('/bookings/{booking}/track', [App\Http\Controllers\RiderBookingController::class, 'track'])->name('bookings.track');


});

Route::middleware(['auth:web'])->prefix('buy-now')->name('buy-now.')->group(function () {

    // Step 1 – store item in session, redirect to buy-now checkout
    Route::post('/', [App\Http\Controllers\BuyNowController::class, 'initiate'])
        ->name('initiate');

    // Step 2 – show the checkout page pre-filled with the single item
    Route::get('/checkout', [App\Http\Controllers\BuyNowController::class, 'checkout'])
        ->name('checkout');

    // Step 3 – place the order
    Route::post('/place', [App\Http\Controllers\BuyNowController::class, 'place'])
        ->name('place');

    // AJAX – fetch shipping rates for the buy-now item
    Route::post('/rates', [App\Http\Controllers\BuyNowController::class, 'getRates'])
        ->name('rates');

});

// -------------------------------------------------------
// SELLER routes — loaded from routes/seller.php
// -------------------------------------------------------
require __DIR__ . '/seller.php';

// -------------------------------------------------------
// ADMIN routes — loaded from routes/admin.php
// -------------------------------------------------------
require __DIR__ . '/admin.php';

// -------------------------------------------------------
// MARKTER routes — loaded from routes/marketer.php
// -------------------------------------------------------
require __DIR__ . '/marketer.php';

// Korapay webhook (no CSRF)
Route::post('/webhooks/korapay', [
    App\Http\Controllers\Seller\WalletController::class, 'webhook'
])->name('korapay.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
