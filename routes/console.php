<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; 
use Illuminate\Support\Facades\Log;


// Check price drop alerts
Schedule::command('alerts:price-drops')
    ->everySixHours()
    ->withoutOverlapping();

// Clean up expired flash sales
Schedule::call(function () {
    \App\Models\FlashSale::where('ends_at', '<', now())
        ->where('is_active', true)
        ->update(['is_active' => false]);
})->hourly()
  ->withoutOverlapping();

// Check order status
Schedule::command('orders-sync:sync-shipping-status')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

// Check delivery booking
Schedule::command('bookings:check-pending')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('monnify:check-pending-bookings')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// For ads
Schedule::command('ads:charge')
    ->dailyAt('13:05')
    ->withoutOverlapping();

// For Order Payment Status
Schedule::command('korapay:check-pending-orders')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('monnify:check-pending-orders')
    ->everyFiveMinutes()
    ->withoutOverlapping();