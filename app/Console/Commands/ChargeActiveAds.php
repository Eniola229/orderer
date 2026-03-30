<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Illuminate\Console\Command;

class ChargeActiveAds extends Command
{
    protected $signature   = 'ads:charge';
    protected $description = 'Deduct daily cost from active ads and mark exhausted/expired ones';

    public function handle(): void
    {
        // First mark any ads past their end_date as expired
        $expired = Ad::where('status', 'active')
            ->where('end_date', '<', now())
            ->get();

        foreach ($expired as $ad) {
            $ad->update(['status' => 'expired']);
            $this->line("  Expired ad [{$ad->id}] — {$ad->title}");
        }

        // Now charge all still-active ads
        $ads = Ad::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        if ($ads->isEmpty()) {
            $this->info('No active ads to charge.');
            return;
        }

        $charged   = 0;
        $exhausted = 0;

        foreach ($ads as $ad) {
            $newSpent = round((float) $ad->amount_spent + (float) $ad->cost_per_day, 2);
            $newSpent = min($newSpent, (float) $ad->budget); // never exceed budget

            $ad->update(['amount_spent' => $newSpent]);

            // Mark as exhausted if budget is fully used
            if ($newSpent >= (float) $ad->budget) {
                $ad->update(['status' => 'exhausted']);
                $exhausted++;
                $this->line("  Exhausted ad [{$ad->id}] — {$ad->title} (budget fully spent)");
            }

            $charged++;
        }

        $this->info("Done. Charged: {$charged} ads. Exhausted: {$exhausted} ads. Expired: {$expired->count()} ads.");
    }
}