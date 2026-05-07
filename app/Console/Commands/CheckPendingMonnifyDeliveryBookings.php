<?php

namespace App\Console\Commands;

use App\Models\DeliveryBooking;
use App\Services\MonnifyService;
use App\Services\ShipbubbleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPendingMonnifyDeliveryBookings extends Command
{
    protected $signature   = 'monnify:check-pending-bookings';
    protected $description = 'Check pending Monnify delivery bookings — verify payment and/or book missing shipments';

    public function __construct(
        protected ShipbubbleService $shipbubble,
    ) {
        parent::__construct();
    }

    public function handle(MonnifyService $monnify): int
    {
        // Grab bookings that need attention:
        // 1) monnify payment still pending, OR
        // 2) paid but shipbubble shipment never created
        $bookings = DeliveryBooking::where(function ($q) {
                $q->where('payment_method', 'monnify')
                  ->where('payment_status', 'pending')
                  ->whereNotNull('payment_reference');
            })
            ->orWhere(function ($q) {
                $q->where('payment_status', 'paid')
                  ->whereNull('shipbubble_shipment_id');
            })
            ->where('created_at', '>=', now()->subHours(48))
            ->with('user')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No pending Monnify bookings need attention.');
            return self::SUCCESS;
        }

        $this->info("Found {$bookings->count()} booking(s). Processing...");

        $paid           = 0;
        $failed         = 0;
        $shipmentBooked = 0;
        $shipmentErrors = 0;
        $skipped        = 0;
        $errors         = 0;

        $bar = $this->output->createProgressBar($bookings->count());
        $bar->start();

        foreach ($bookings as $booking) {
            try {
                // ── Step 1: resolve payment status ───────────────────────────
                if ($booking->payment_status === 'pending' && $booking->payment_reference) {
                    $data          = $monnify->verifyTransaction($booking->payment_reference);
                    // Monnify returns uppercase: PAID, OVERPAID, FAILED, EXPIRED, REVERSED, CANCELLED, PENDING
                    $monnifyStatus = $data['paymentStatus'] ?? null;

                    if (in_array($monnifyStatus, ['PAID', 'OVERPAID'])) {
                        $booking->update(['payment_status' => 'paid']);
                        $paid++;
                        // fall through to shipment booking below

                    } elseif (in_array($monnifyStatus, ['FAILED', 'EXPIRED', 'REVERSED', 'CANCELLED'])) {
                        $booking->update([
                            'payment_status' => 'failed',
                            'status'         => 'cancelled',
                        ]);
                        $failed++;
                        $bar->advance();
                        continue; // nothing else to do

                    } else {
                        // PENDING, PARTIALLY_PAID — leave it alone
                        $skipped++;
                        $bar->advance();
                        continue;
                    }
                }

                // ── Step 2: book shipment if not yet created ─────────────────
                if ($booking->payment_status === 'paid' && is_null($booking->shipbubble_shipment_id)) {
                    $booked = $this->bookWithShipbubble($booking);
                    $booked ? $shipmentBooked++ : $shipmentErrors++;
                }

            } catch (\Exception $e) {
                $errors++;
                Log::error("monnify:check-pending-bookings — booking #{$booking->booking_number}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Result', 'Count'],
            [
                ['Newly paid',              $paid],
                ['Failed/Expired',          $failed],
                ['Shipment booked',         $shipmentBooked],
                ['Shipment errors',         $shipmentErrors],
                ['Skipped (still pending)', $skipped],
                ['Errors',                  $errors],
            ]
        );

        return self::SUCCESS;
    }

    protected function bookWithShipbubble(DeliveryBooking $booking): bool
    {
        $rateData     = $booking->rate_data ?? [];
        $requestToken = $rateData['request_token'] ?? null;

        if (!$requestToken) {
            Log::warning("monnify:check-pending-bookings — no request_token in rate_data for booking #{$booking->booking_number}");
            return false;
        }

        $user = $booking->user;

        $sender = [
            'name'    => $user->full_name,
            'email'   => $user->email,
            'phone'   => $user->phone ?? '',
            'address' => $booking->pickup_address,
            'city'    => $booking->pickup_city,
            'state'   => $booking->pickup_city,
            'country' => $booking->pickup_country,
        ];

        $recipient = [
            'name'    => 'Recipient',
            'email'   => $user->email,
            'phone'   => '',
            'address' => $booking->delivery_address,
            'city'    => $booking->delivery_city,
            'state'   => $booking->delivery_city,
            'country' => $booking->delivery_country,
        ];

        $package = [
            'weight' => max($booking->weight_kg ?? 0.5, 0.5),
            'length' => 20,
            'width'  => 20,
            'height' => 20,
            'items'  => [[
                'name'     => $booking->item_description,
                'quantity' => 1,
                'value'    => max((float) ($booking->declared_value ?? 10), 10),
            ]],
        ];

        try {
            $shipment = $this->shipbubble->createShipment(
                $booking->service_code,
                (string) $booking->courier_id,
                $sender,
                $recipient,
                $package,
                $requestToken
            );

            $booking->update([
                'shipbubble_shipment_id'  => $shipment['order_id'] ?? null,
                'tracking_number'         => $shipment['courier']['tracking_code'] ?? null,
                'tracking_url'            => $shipment['tracking_url'] ?? null,
                'estimated_delivery_date' => $shipment['estimated_delivery_date'] ?? null,
                'status'                  => 'confirmed',
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("monnify:check-pending-bookings — Shipbubble failed for booking #{$booking->booking_number}: " . $e->getMessage());
            return false;
        }
    }
}