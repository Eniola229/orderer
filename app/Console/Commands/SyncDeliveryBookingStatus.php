<?php

namespace App\Console\Commands;

use App\Models\DeliveryBooking;
use App\Models\ShipmentTracking;
use App\Services\ShipbubbleService;
use App\Services\TermiiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncDeliveryBookingStatus extends Command
{
    protected $signature   = 'bookings-sync:sync-shipping-status';
    protected $description = 'Check Shipbubble for shipping updates on delivery bookings and update statuses';

    public function __construct(
        protected ShipbubbleService $shipbubble,
        protected TermiiService     $termii,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        // Fetch all active bookings that have a shipbubble_shipment_id
        $activeBookings = DeliveryBooking::whereNotIn('status', ['delivered', 'cancelled', 'completed'])
            ->whereNotNull('shipbubble_shipment_id')
            ->with('user')
            ->get();

        if ($activeBookings->isEmpty()) {
            $this->info('No active delivery bookings with a Shipbubble shipment ID found.');
            return self::SUCCESS;
        }

        $this->info("Checking {$activeBookings->count()} delivery booking(s)...");

        foreach ($activeBookings as $booking) {
            try {
                $trackingData = $this->shipbubble->track($booking->shipbubble_shipment_id);
                $apiStatus    = $trackingData['status'] ?? null;

                if (!$apiStatus) {
                    $this->warn("  Booking {$booking->booking_number} (Shipment: {$booking->shipbubble_shipment_id}) — no status returned, skipping.");
                    continue;
                }

                $mappedStatus = match (strtolower($apiStatus)) {
                    'delivered', 'completed'                    => 'delivered',
                    'picked_up', 'in_transit', 'transit'        => 'in_transit',
                    'confirmed', 'processing'                   => 'confirmed',
                    'cancelled'                                 => 'cancelled',
                    default                                     => null,
                };

                if (!$mappedStatus) {
                    $this->line("  Booking {$booking->booking_number} — unmapped status '{$apiStatus}', skipping.");
                    continue;
                }

                if ($booking->status === $mappedStatus) {
                    continue; // Nothing changed for this booking
                }

                $previousStatus = $booking->status;

                // Update booking status
                $booking->update(['status' => $mappedStatus]);
                $this->info("  Booking {$booking->booking_number} — {$previousStatus} → {$mappedStatus}");

                // Save tracking event
                ShipmentTracking::firstOrCreate(
                    [
                        'delivery_booking_id' => $booking->id,
                        'event_at'            => $trackingData['captured'] ?? now(),
                        'status'              => $apiStatus,
                    ],
                    [
                        'tracking_number' => $booking->tracking_number,
                        'carrier'         => $booking->carrier,
                        'description'     => $trackingData['message'] ?? $apiStatus,
                        'location'        => $trackingData['location'] ?? '',
                    ]
                );

                // Send SMS to user about status update
                try {
                    $user = $booking->user;
                    if ($user && $user->phone) {
                        $statusMessage = match($mappedStatus) {
                            'delivered' => "delivered! Your package has been successfully delivered.",
                            'in_transit' => "in transit! Your package is on the way. Tracking: " . ($trackingData['tracking_number'] ?? $booking->tracking_number ?? 'available soon'),
                            'confirmed' => "confirmed! Your delivery booking has been confirmed and is being processed.",
                            'cancelled' => "cancelled. Please contact support if this was a mistake.",
                            default     => "updated to {$mappedStatus}. Check your email for details."
                        };
                        
                        $this->termii->sendBulk(
                            [ltrim($user->phone, '+')],
                            "Hi {$user->first_name}, delivery booking #{$booking->booking_number} has been {$statusMessage} Thank you for using our delivery service."
                        );
                    }
                } catch (\Exception $e) {
                    Log::error("SyncDeliveryBookingStatus — SMS failed for booking #{$booking->booking_number}: " . $e->getMessage());
                }

            } catch (\Exception $e) {
                $this->error("  Booking {$booking->booking_number} (Shipment: {$booking->shipbubble_shipment_id}) — failed: {$e->getMessage()}");
                Log::error("SyncDeliveryBookingStatus failed for booking {$booking->booking_number}: {$e->getMessage()}");
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}