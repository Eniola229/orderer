<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryBooking;
use App\Models\ShipmentTracking;
use App\Services\ShipbubbleService;
use Illuminate\Http\Request;

class DeliveryBookingController extends Controller
{
    public function __construct(protected ShipbubbleService $shipbubble) {}

    /**
     * List all delivery bookings with filters.
     */
    public function index(Request $request)
    {
        if (!auth('admin')->user()->canView()) abort(403);

        $query = DeliveryBooking::with('user')
            ->latest();

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Delivery type filter
        if ($request->filled('delivery_type')) {
            $query->where('delivery_type', $request->delivery_type);
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by booking number or user email/name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%")
                         ->orWhere('full_name', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->paginate(20)->withQueryString();

        // Stats (unfiltered totals + filtered)
        $stats = [
            'total'        => DeliveryBooking::count(),
            'pending'      => DeliveryBooking::where('status', 'pending')->count(),
            'delivered'    => DeliveryBooking::where('status', 'delivered')->count(),
            'revenue'      => DeliveryBooking::where('payment_status', 'paid')->sum('fee'),
            'service_fees' => DeliveryBooking::where('payment_status', 'paid')->sum('service_fee'),
        ];

        return view('admin.delivery-bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Show a single booking detail — calls Shipbubble track() on load
     * to sync the latest status and events, same as the user-side track().
     */
    public function show(DeliveryBooking $deliveryBooking)
    {
        if (!auth('admin')->user()->canView()) abort(403);

        $deliveryBooking->load(['user', 'tracking']);

        $trackingEvents = [];

        if ($deliveryBooking->shipbubble_shipment_id) {
            try {
                $trackingData = $this->shipbubble->track($deliveryBooking->shipbubble_shipment_id);

                // Sync status — same mapping as user-side track()
                $apiStatus = $trackingData['status'] ?? null;
                if ($apiStatus) {
                    $mappedStatus = match (strtolower($apiStatus)) {
                        'cancelled'               => 'cancelled',
                        'completed'               => 'delivered',
                        'picked_up', 'in_transit' => 'in_transit',
                        'confirmed', 'processing' => 'confirmed',
                        default                   => $deliveryBooking->status,
                    };
                    $deliveryBooking->update(['status' => $mappedStatus]);
                }

                // Store tracking events — same as user-side track()
                $trackingEvents = $trackingData['events'] ?? [];
                foreach ($trackingEvents as $event) {
                    ShipmentTracking::firstOrCreate(
                        [
                            'delivery_booking_id' => $deliveryBooking->id,
                            'event_at'            => $event['captured'] ?? now(),
                            'status'              => $event['message'] ?? '',
                        ],
                        [
                            'tracking_number' => $deliveryBooking->tracking_number,
                            'carrier'         => $deliveryBooking->carrier,
                            'description'     => $event['message'] ?? '',
                            'location'        => $event['location'] ?? '',
                        ]
                    );
                }

                $deliveryBooking->refresh();

            } catch (\Exception $e) {
                // Don't break the page — just fall back to stored events
                \Log::warning('Admin booking show — Shipbubble track failed', [
                    'booking_id' => $deliveryBooking->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        // If no live events (no shipbubble_shipment_id or track failed), use stored ones
        if (empty($trackingEvents)) {
            $trackingEvents = $deliveryBooking->tracking
                ->sortByDesc('event_at')
                ->values()
                ->toArray();
        }

        return view('admin.delivery-bookings.show', compact('deliveryBooking', 'trackingEvents'));
    }


    /**
     * Update booking status from admin panel.
     */
    public function updateStatus(Request $request, DeliveryBooking $deliveryBooking)
    {
        if (!auth('admin')->user()->canEditOrders()) abort(403);

        $request->validate([
            'status' => ['required', 'in:pending,confirmed,picked_up,in_transit,delivered,cancelled'],
        ]);

        $deliveryBooking->update(['status' => $request->status]);

        return back()->with('success', "Booking #{$deliveryBooking->booking_number} status updated to " . ucfirst($request->status) . '.');
    }
}