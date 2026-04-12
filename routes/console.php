<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;  // ← Add this

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-release escrow for orders shipped 3+ days ago
Schedule::command('escrow:auto-release')->hourly();

// Check price drop alerts — run every 6 hours
Schedule::command('alerts:price-drops')->everySixHours();

// Auto-award badges based on criteria — daily
//Schedule::command('badges:auto-award')->daily()->at('03:00');

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