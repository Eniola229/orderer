<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Auto-release escrow for orders shipped 7+ days ago
        $schedule->command('escrow:auto-release')->daily()->at('02:00');

        // Check price drop alerts — run every 6 hours
        $schedule->command('alerts:price-drops')->everySixHours();

        // Auto-award badges based on criteria — daily
        $schedule->command('badges:auto-award')->daily()->at('03:00');

        // Clean up expired flash sales
        $schedule->call(function () {
            \App\Models\FlashSale::where('ends_at', '<', now())
                ->where('is_active', true)
                ->update(['is_active' => false]);
        })->hourly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}