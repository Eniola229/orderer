<?php

namespace App\Console\Commands;

use App\Models\DeliveryBooking;
use App\Services\KorapayService;
use App\Services\ShipbubbleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckPendingDeliveryBookings extends Command
{
    protected $signature   = 'bookings:check-pending';
    protected $description = 'Check pending delivery bookings — verify Korapay payment and/or book missing shipments';

    public function __construct(
        protected ShipbubbleService $shipbubble,
        protected \App\Services\TermiiService $termii,
    ) {
        parent::__construct();
    }

    public function handle(KorapayService $korapay): int
    {
        // Grab bookings that need attention:
        // 1) korapay payment still pending, OR
        // 2) paid but shipbubble shipment never created
        $bookings = DeliveryBooking::where(function ($q) {
                $q->where('payment_method', 'korapay')
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
            $this->info('No bookings need attention.');
            return self::SUCCESS;
        }

        $this->info("Found {$bookings->count()} booking(s). Processing...");

        $paid            = 0;
        $failed          = 0;
        $shipmentBooked  = 0;
        $shipmentErrors  = 0;
        $skipped         = 0;
        $errors          = 0;

        $bar = $this->output->createProgressBar($bookings->count());
        $bar->start();

        foreach ($bookings as $booking) {
            try {
                // ── Step 1: resolve payment status ──────────────────────────────
                if ($booking->payment_status === 'pending' && $booking->payment_reference) {
                    $data          = $korapay->verifyTransaction($booking->payment_reference);
                    $korapayStatus = $data['status'] ?? null;

                    if ($korapayStatus === 'success') {
                        $booking->update(['payment_status' => 'paid']);
                        $paid++;
                        // fall through to shipment booking below
                    } elseif (in_array($korapayStatus, ['failed', 'expired', 'reversed'])) {
                        $booking->update([
                            'payment_status' => 'failed',
                            'status'         => 'cancelled',
                        ]);
                        $failed++;
                        $bar->advance();
                        continue; // nothing else to do
                    } else {
                        $skipped++; // still pending on Korapay's end
                        $bar->advance();
                        continue;
                    }
                }

                // ── Step 2: book shipment if not yet created ─────────────────────
                if ($booking->payment_status === 'paid' && is_null($booking->shipbubble_shipment_id)) {
                    $booked = $this->bookWithShipbubble($booking);
                    $booked ? $shipmentBooked++ : $shipmentErrors++;
                }

            } catch (\Exception $e) {
                $errors++;
                Log::error("bookings:check-pending — booking #{$booking->booking_number}: " . $e->getMessage());
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
        // Token lives in rate_data (stored at booking creation) — no session in CLI
        $rateData     = $booking->rate_data ?? [];
        $requestToken = $rateData['request_token'] ?? null;

        if (!$requestToken) {
            Log::warning("bookings:check-pending — no request_token in rate_data for booking #{$booking->booking_number}");
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

            // Send SMS to user
            try {
                $user = $booking->user;
                if ($user && $user->phone) {
                    $trackingCode = $shipment['courier']['tracking_code'] ?? 'available soon';
                    $this->termii->sendBulk(
                        [ltrim($user->phone, '+')],
                        "Hi {$user->first_name}, your delivery booking #{$booking->booking_number} has been confirmed! Tracking #: {$trackingCode}. We'll notify you once it's picked up. Thank you!"
                    );
                }
            } catch (\Exception $e) {
                Log::error("bookings:check-pending — SMS failed for booking #{$booking->booking_number}: " . $e->getMessage());
            }

            return true;

        } catch (\Exception $e) {
            Log::error("bookings:check-pending — Shipbubble failed for booking #{$booking->booking_number}: " . $e->getMessage());
            return false;
        }
    }
}