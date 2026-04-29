<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\KorapayService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckPendingKorapayOrders extends Command
{
    protected $signature   = 'korapay:check-pending-orders';
    protected $description = 'Check pending Korapay orders and update payment status';

    public function handle(KorapayService $korapay): int
    {
        $pendingOrders = Order::where('payment_method', 'korapay')
            ->where('payment_status', 'pending')
            ->whereNotNull('payment_reference')
            ->where('created_at', '>=', now()->subHours(24))
            ->get();

        if ($pendingOrders->isEmpty()) {
            $this->info('No pending Korapay orders found.');
            return self::SUCCESS;
        }

        $this->info("Found {$pendingOrders->count()} pending order(s). Checking...");

        $paid      = 0;
        $failed    = 0;
        $skipped   = 0;
        $errors    = 0;

        $bar = $this->output->createProgressBar($pendingOrders->count());
        $bar->start();

        foreach ($pendingOrders as $order) {
            try {
                $data          = $korapay->verifyTransaction($order->payment_reference);
                $korapayStatus = $data['status'] ?? null;

                if ($korapayStatus === 'success') {
                    DB::transaction(fn() => $order->update(['payment_status' => 'paid']));
                    $paid++;
                } elseif (in_array($korapayStatus, ['failed', 'expired', 'reversed'])) {
                    DB::transaction(fn() => $order->update([
                        'payment_status' => 'failed',
                        'status'         => 'cancelled',
                    ]));
                    $failed++;
                } else {
                    $skipped++; // still pending on Korapay's side
                }
            } catch (\Exception $e) {
                $errors++;
                \Log::error("korapay:check-pending-orders — order #{$order->order_number}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Result', 'Count'],
            [
                ['Paid',    $paid],
                ['Failed',  $failed],
                ['Skipped (still pending)', $skipped],
                ['Errors',  $errors],
            ]
        );

        return self::SUCCESS;
    }
}