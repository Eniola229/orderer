<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; 
use Illuminate\Support\Facades\Log;


// Check price drop alerts — run every 6 hours
Schedule::command('alerts:price-drops')->everySixHours();


// Clean up expired flash sales
Schedule::call(function () {
    \App\Models\FlashSale::where('ends_at', '<', now())
        ->where('is_active', true)
        ->update(['is_active' => false]);
})->hourly();

// Check order status 
Schedule::command('orders:sync-shipping-status')->everyFifteenMinutes();

// For ads
Schedule::command('ads:charge')->dailyAt('13:05');

//For Order Payment Status
Schedule::command('korapay:check-pending-orders')
         ->everyFiveMinutes()
         ->withoutOverlapping()
         ->onOneServer();