<?php
use App\Http\Controllers\CheckoutController;


Route::post('/webhooks/korapay/payout', [CheckoutController::class, 'handlePayout']);