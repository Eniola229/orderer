<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; 
use Illuminate\Support\Facades\Log;


// Check price drop alerts
Schedule::command('alerts:price-drops')
    ->everySixHours();

// Clean up expired flash sales
Schedule::call(function () {
    \App\Models\FlashSale::where('ends_at', '<', now())
        ->where('is_active', true)
        ->update(['is_active' => false]);
})->hourly();

// Check order status
Schedule::command('orders-sync:sync-shipping-status')
    ->everyFifteenMinutes();

// Check delivery booking
Schedule::command('bookings:check-pending')
    ->everyFiveMinutes();

Schedule::command('monnify:check-pending-bookings')
    ->everyFiveMinutes();

// For ads
Schedule::command('ads:charge')
    ->dailyAt('13:05');

// For Order Payment Status
Schedule::command('korapay:check-pending-orders')
    ->everyFiveMinutes();

Schedule::command('monnify:check-pending-orders')
    ->everyFiveMinutes();

Schedule::command('sitemap:generate')->daily();