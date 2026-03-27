<?php

namespace App\Http\Controllers;

use App\Models\DeliveryBooking;
use App\Models\ShipmentTracking;
use App\Services\ShipbubbleService;
use App\Services\WalletService;
use App\Services\KorapayService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RiderBookingController extends Controller
{
    public function __construct(
        protected ShipbubbleService $shipbubble,
        protected WalletService     $wallet,
        protected KorapayService    $korapay
    ) {}

    /**
     * Get shipping rates for rider booking form — called via AJAX.
     */
public function getRates(Request $request)
    {
        $request->validate([
            'pickup_city'     => ['required', 'string'],
            'pickup_country'  => ['required', 'string'],
            'delivery_city'   => ['required', 'string'],
            'delivery_country'=> ['required', 'string'],
            'weight_kg'       => ['nullable', 'numeric', 'min:0.1'],
            'sender_name'     => ['nullable', 'string'],
            'sender_phone'    => ['nullable', 'string'],
            'recipient_name'  => ['nullable', 'string'],
            'recipient_phone' => ['nullable', 'string'],
        ]);

        try {
            // Step 1 — validate sender address to get address_code
            $senderValidation = $this->shipbubble->validateAddress([
                'name'    => $request->sender_name ?? auth('web')->user()->full_name,
                'email'   => auth('web')->user()->email,
                'phone'   => $request->sender_phone ?? auth('web')->user()->phone ?? '',
                'address' => $request->pickup_address ?? '',
                'city'    => $request->pickup_city,
                'state'   => $request->pickup_state ?? $request->pickup_city,
                'country' => $request->pickup_country,
            ]);

            // Step 2 — validate recipient address to get address_code
            $recipientValidation = $this->shipbubble->validateAddress([
                'name'    => $request->recipient_name ?? 'Recipient',
                'email'   => $request->recipient_email ?? auth('web')->user()->email,
                'phone'   => $request->recipient_phone ?? '',
                'address' => $request->delivery_address ?? '',
                'city'    => $request->delivery_city,
                'state'   => $request->delivery_state ?? $request->delivery_city,
                'country' => $request->delivery_country,
            ]);

            $senderAddressCode    = $senderValidation['data']['address_code']    ?? null;
            $recipientAddressCode = $recipientValidation['data']['address_code'] ?? null;

            if (!$senderAddressCode || !$recipientAddressCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not validate one or both addresses. Please provide a more detailed address.',
                ], 422);
            }

            // Step 3 — fetch rates
            $response = $this->shipbubble->getRates([
                'sender_address_code'   => $senderAddressCode,
                'reciever_address_code' => $recipientAddressCode,

                'weight'      => max((float)($request->weight_kg ?? 0.5), 0.1),
                'value'       => (float)($request->declared_value ?? 10),
                'length'      => 20,
                'width'       => 20,
                'height'      => 20,
                'category_id' => 2178251,

                'item_name'   => $request->item_description ?? 'Package',
            ]);

            // Log full Shipbubble response for debugging
            //\Log::info('Shipbubble full rates response', $response);

           $rateData = $response['data'] ?? $response;

            if (!empty($rateData['request_token'])) {
                session(['rider_request_token' => $rateData['request_token']]);
            }

            $couriers = $rateData['couriers'] ?? [];

            return response()->json([
                'success' => true,
                'rates'   => $couriers,
            ]);

        } catch (\Exception $e) {
            $rawMessage = $e->getMessage();

            // Try to extract the real API message from JSON in the exception
            $apiMessage = null;
            if (preg_match('/\{.*\}/s', $rawMessage, $match)) {
                $decoded    = json_decode($match[0], true);
                $apiMessage = $decoded['message'] ?? null;
            }

            \Log::error('RiderBooking: failed to fetch courier rates', [
                'error'            => $rawMessage,
                'pickup_city'      => $request->pickup_city,
                'pickup_country'   => $request->pickup_country,
                'delivery_city'    => $request->delivery_city,
                'delivery_country' => $request->delivery_country,
            ]);

            return response()->json([
                'success' => false,
                'message' => $apiMessage ?? 'Could not find a courier for this address. Please check the details and try again.',
            ], 422);
        }
    }

    /**
     * Store a booking and redirect to payment.
     */
    public function book(Request $request)
    {
         \Log::info('Booking request data:', $request->all());

        if (!auth('web')->check()) {
            return redirect()->route('login')->with('info', 'Please sign in to book a delivery.');
        }

        $request->validate([
            'delivery_type'    => ['required', 'in:local,international'],
            'item_description' => ['required', 'string', 'max:255'],
            'pickup_address'   => ['required', 'string'],
            'pickup_city'      => ['required', 'string'],
            'pickup_country'   => ['required', 'string'],
            'delivery_address' => ['required', 'string'],
            'delivery_city'    => ['required', 'string'],
            'delivery_country' => ['required', 'string'],
            'weight_kg'        => ['nullable', 'numeric', 'min:0.1'],
            'declared_value'   => ['nullable', 'numeric', 'min:0'],
            'service_code'     => ['required', 'string'],
            'courier_id'       => ['required', 'string'],
            'carrier'          => ['required', 'string'],
            'service_name'     => ['required', 'string'],
            'fee'              => ['required', 'numeric', 'min:0'],
            'payment_method'   => ['required', 'in:wallet,korapay'],
        ]);

        $fee  = (float) $request->fee;
        $user = auth('web')->user();

        if ($request->payment_method === 'wallet') {
            if ($user->wallet_balance < $fee) {
                return back()->with('error', "Insufficient wallet balance. You have \${$user->wallet_balance}, need \${$fee}.");
            }
        }

        DB::beginTransaction();

        try {
            $booking = DeliveryBooking::create([
                'user_id'          => $user->id,
                'delivery_type'    => $request->delivery_type,
                'pickup_address'   => $request->pickup_address,
                'pickup_city'      => $request->pickup_city,
                'pickup_country'   => $request->pickup_country,
                'delivery_address' => $request->delivery_address,
                'delivery_city'    => $request->delivery_city,
                'delivery_country' => $request->delivery_country,
                'item_description' => $request->item_description,
                'weight_kg'        => $request->weight_kg ?? 0.5,
                'declared_value'   => $request->declared_value ?? 0,
                'carrier'          => $request->carrier,
                'courier_id'       => $request->courier_id, 
                'service_code'     => $request->service_code,
                'service_name'     => $request->service_name,
                'fee'              => $fee,
                'payment_status'   => 'pending',
                'status'           => 'pending',
                'rate_data'        => json_decode($request->rate_data ?? '{}', true),
            ]);

            if ($request->payment_method === 'wallet') {
                // Debit wallet
                $this->wallet->debit(
                    $user,
                    $fee,
                    'debit',
                    "Rider booking #{$booking->booking_number} — {$request->service_name}"
                );

                // Book with Shipbubble
                $this->bookWithShipbubble($booking, $request, $user);

                DB::commit();

                return redirect()->route('buyer.bookings.show', $booking->id)
                    ->with('success', "Booking confirmed! #{$booking->booking_number}. Your shipment is being processed.");

            } else {
                // Korapay
                $reference = $this->korapay->generateReference('BKG');
                $booking->update(['payment_reference' => $reference]);

                $this->korapay->createTransaction($user, $fee, 'order_payment', $reference);

                DB::commit();

                $checkoutData = $this->korapay->initializeCheckout(
                    $user->email,
                    $user->full_name,
                    (float) $fee,
                    $reference,
                    route('rider.callback'),
                    '',                          // ← notificationUrl, leave empty for now
                    ['booking_id' => $booking->id]
                );

                return redirect($checkoutData['checkout_url']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Booking failed');
        }
    }

    /**
     * Korapay callback after rider booking payment.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('rider.booking')->with('error', 'Invalid reference.');
        }

        try {
            $data = $this->korapay->verifyTransaction($reference);

            if ($data['status'] === 'success') {
                $booking = DeliveryBooking::where('payment_reference', $reference ?? '')->first();

                if ($booking) {
                    $booking->update(['payment_status' => 'paid']);

                    // Now book with Shipbubble
                    $this->bookWithShipbubble($booking, null, auth('web')->user());

                    return redirect()->route('buyer.bookings.show', $booking->id)
                        ->with('success', "Payment successful! Booking #{$booking->booking_number} confirmed.");
                }
            }

            return redirect()->route('rider.booking')
                ->with('error', 'Payment could not be verified.');

        } catch (\Exception $e) {
            return redirect()->route('rider.booking')
                ->with('error', 'Callback error: ' . $e->getMessage());
        }
    }

    /**
     * Track a booking.
     */
public function track(DeliveryBooking $booking)
{
    if ($booking->user_id !== auth('web')->id()) abort(403);

    // \Log::info('Track called', [
    //     'booking_id'      => $booking->id,
    //     'status'          => $booking->status,
    //     'tracking_number' => $booking->tracking_number,  // ← use this
    // ]);

    $trackingEvents = [];

 if ($booking->shipbubble_shipment_id) {  // e.g. "SB-BB7EDE9F"
    $trackingData = $this->shipbubble->track($booking->shipbubble_shipment_id);

    // Sync status
    $apiStatus = $trackingData['status'] ?? null;
    if ($apiStatus) {
        $mappedStatus = match(strtolower($apiStatus)) {
            'cancelled'  => 'cancelled',
            'completed'  => 'delivered',
            'picked_up', 'in_transit' => 'in_transit',
            'confirmed'  => 'confirmed',
            'confirmed'  => 'processing',
            default      => $booking->status,
        };
        $booking->update(['status' => $mappedStatus]);
    }

    // Events use 'captured' and 'message' per docs
    $trackingEvents = $trackingData['events'] ?? [];
    foreach ($trackingEvents as $event) {
        ShipmentTracking::firstOrCreate(
            [
                'delivery_booking_id' => $booking->id,
                'event_at'            => $event['captured'] ?? now(),
                'status'              => $event['message'] ?? '',
            ],
            [
                'tracking_number' => $booking->tracking_number,
                'carrier'         => $booking->carrier,
                'description'     => $event['message'] ?? '',
                'location'        => $event['location'] ?? '',
            ]
        );
    }
    } else {
        // \Log::warning('Track skipped — no tracking_number yet', [
        //     'booking_id' => $booking->id,
        // ]);

        $trackingEvents = $booking->tracking()
            ->orderByDesc('event_at')
            ->get()
            ->toArray();
    }

    $booking->refresh();

    return view('storefront.rider-tracking', compact('booking', 'trackingEvents'));
}
     

    /**
     * List buyer's bookings.
     */
    public function myBookings()
    {
        $bookings = DeliveryBooking::where('user_id', auth('web')->id())
            ->latest()
            ->paginate(15);

        return view('buyer.bookings.index', compact('bookings'));
    }

    public function showBooking(DeliveryBooking $booking)
    {
        if ($booking->user_id !== auth('web')->id()) abort(403);
        return view('buyer.bookings.show', compact('booking'));
    }

    /**
     * Internal: call Shipbubble to actually create the shipment after payment.
     */
    protected function bookWithShipbubble(
        DeliveryBooking $booking,
        ?Request        $request,
        $user
    ): void {
        $requestToken = session('rider_request_token');

        if (!$requestToken) return; // fallback mode, skip API call

        try {
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
                'weight'  => $booking->weight_kg ?? 0.5,
                'length'  => 20,
                'width'   => 20,
                'height'  => 20,
                'items'   => [[
                    'name'     => $booking->item_description,
                    'quantity' => 1,
                    'value'    => $booking->declared_value ?? 10,
                ]],
            ];

            $shipment = $this->shipbubble->createShipment(
                $booking->service_code,
                $booking->courier_id,   
                $sender,
                $recipient,
                $package,
                $requestToken
            );

            $booking->update([
                'shipbubble_shipment_id'  => $shipment['order_id'] ?? null,        // ← "SB-BB7EDE9F"
                'tracking_number'         => $shipment['courier']['tracking_code'] ?? null, // ← courier's own code
                'tracking_url'            => $shipment['tracking_url'] ?? null,
                'estimated_delivery_date' => $shipment['estimated_delivery_date'] ?? null,
                'status'                  => 'confirmed',
                'payment_status'          => 'paid',
            ]);
            session()->forget('rider_request_token');

        } catch (\Exception $e) {
            // Do not fail the booking — just log and mark as pending manual processing
            \Log::error('Shipbubble booking error for BKG #' . $booking->booking_number . ': ' . $e->getMessage());
            $booking->update(['status' => 'pending']);
        }
    }

    protected function fallbackRates(string $pickupCountry, string $deliveryCountry): array
    {
        $isLocal = strtoupper($pickupCountry) === strtoupper($deliveryCountry);

        return $isLocal ? [
            [
                'service_code' => 'standard_local',
                'courier'      => ['name' => 'Standard Rider'],
                'service'      => ['name' => 'Standard (1-3 days)'],
                'total'        => 3.50,
                'delivery_eta' => '1-3 business days',
            ],
            [
                'service_code' => 'express_local',
                'courier'      => ['name' => 'Express Rider'],
                'service'      => ['name' => 'Express (Same day)'],
                'total'        => 8.00,
                'delivery_eta' => 'Same day',
            ],
        ] : [
            [
                'service_code' => 'intl_economy',
                'courier'      => ['name' => 'DHL'],
                'service'      => ['name' => 'Economy International (7-14 days)'],
                'total'        => 20.00,
                'delivery_eta' => '7-14 business days',
            ],
            [
                'service_code' => 'intl_express',
                'courier'      => ['name' => 'DHL Express'],
                'service'      => ['name' => 'Express International (3-5 days)'],
                'total'        => 50.00,
                'delivery_eta' => '3-5 business days',
            ],
        ];
    }
}