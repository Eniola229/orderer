<?php

namespace App\Http\Controllers;

use App\Models\DeliveryBooking;
use App\Models\MonnifyTransaction;
use App\Models\ShipmentTracking;
use App\Services\ShipbubbleService;
use App\Services\WalletService;
use App\Services\KorapayService;
use App\Services\MonnifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiderBookingController extends Controller
{
    public function __construct(
        protected ShipbubbleService $shipbubble,
        protected WalletService     $wallet,
        protected KorapayService    $korapay,
        protected MonnifyService    $monnify,
    ) {}

    // -------------------------------------------------------------------------
    // Rates (unchanged)
    // -------------------------------------------------------------------------

    public function getRates(Request $request)
    {
        $request->validate([
            'pickup_city'      => ['required', 'string'],
            'pickup_country'   => ['required', 'string'],
            'delivery_city'    => ['required', 'string'],
            'delivery_country' => ['required', 'string'],
            'weight_kg'        => ['nullable', 'numeric', 'min:0.1'],
            'sender_name'      => ['nullable', 'string'],
            'sender_phone'     => ['nullable', 'string'],
            'recipient_name'   => ['nullable', 'string'],
            'recipient_phone'  => ['nullable', 'string'],
        ]);

        try {
            $senderValidation = $this->shipbubble->validateAddress([
                'name'    => $request->sender_name ?? auth('web')->user()->full_name,
                'email'   => auth('web')->user()->email,
                'phone'   => $request->sender_phone ?? auth('web')->user()->phone ?? '',
                'address' => $request->pickup_address ?? '',
                'city'    => $request->pickup_city,
                'state'   => $request->pickup_state ?? $request->pickup_city,
                'country' => $request->pickup_country,
            ]);

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

            $response = $this->shipbubble->getRates([
                'sender_address_code'   => $senderAddressCode,
                'reciever_address_code' => $recipientAddressCode,
                'weight'                => max((float)($request->weight_kg ?? 0.5), 0.1),
                'value'                 => (float)($request->declared_value ?? 10),
                'length'                => 20,
                'width'                 => 20,
                'height'                => 20,
                'category_id'           => 2178251,
                'item_name'             => $request->item_description ?? 'Package',
            ]);

            $rateData = $response['data'] ?? $response;

            if (!empty($rateData['request_token'])) {
                session(['rider_request_token' => $rateData['request_token']]);
            }

            return response()->json([
                'success' => true,
                'rates'   => $rateData['couriers'] ?? [],
            ]);

        } catch (\Exception $e) {
            $rawMessage = $e->getMessage();
            $apiMessage = null;
            if (preg_match('/\{.*\}/s', $rawMessage, $match)) {
                $decoded    = json_decode($match[0], true);
                $apiMessage = $decoded['message'] ?? null;
            }

            \Log::error('RiderBooking: failed to fetch courier rates', ['error' => $rawMessage]);

            return response()->json([
                'success' => false,
                'message' => $apiMessage ?? 'Could not find a courier for this address. Please check the details and try again.',
            ], 422);
        }
    }

    // -------------------------------------------------------------------------
    // Book — wallet / korapay / monnify
    // -------------------------------------------------------------------------

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
            'payment_method'   => ['required', 'in:wallet,korapay,monnify'],
        ]);

        $fee         = (float) $request->fee;
        $user        = auth('web')->user();
        $totalCharge = $fee + \App\Models\DeliveryBooking::SERVICE_FEE;

        if ($request->payment_method === 'wallet' && $user->wallet_balance < $totalCharge) {
            return back()->with('error', "Insufficient wallet balance. You have ₦{$user->wallet_balance}, need ₦{$totalCharge} (shipping + ₦200 service fee).");
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
                'service_fee'      => \App\Models\DeliveryBooking::SERVICE_FEE,
                'payment_status'   => 'pending',
                'status'           => 'pending',
                'rate_data'        => json_decode($request->rate_data ?? '{}', true),
            ]);

            // ── Wallet ────────────────────────────────────────────────────────
            if ($request->payment_method === 'wallet') {
                $this->wallet->debit(
                    $user,
                    $totalCharge,
                    'debit',
                    "Delivery booking #{$booking->booking_number} — {$request->service_name} + service fee"
                );

                $this->bookWithShipbubble($booking, $request, $user);
                DB::commit();
                
                if ($user->phone) {
                    $termii = app(\App\Services\TermiiService::class);
                    $termii->sendBulk(
                        [ltrim($user->phone, '+')],
                        "Hi {$user->first_name}, your delivery booking #{$booking->booking_number} has been confirmed! Total: ₦" . number_format($booking->fee + $booking->service_fee, 2) . ". We'll notify you once the shipment is picked up. Thank you!"
                    );
                }

                return redirect()->route('buyer.bookings.show', $booking->id)
                    ->with('success', "Booking confirmed! #{$booking->booking_number}. Your shipment is being processed.");
            }

            // ── Korapay ───────────────────────────────────────────────────────
            if ($request->payment_method === 'korapay') {
                $reference = $this->korapay->generateReference('BKG');
                $booking->update(['payment_reference' => $reference]);
                $this->korapay->createTransaction($user, $totalCharge, 'order_payment', $reference);
                DB::commit();

                $checkoutData = $this->korapay->initializeCheckout(
                    $user->email,
                    $user->full_name,
                    (float) $totalCharge,
                    $reference,
                    route('rider.callback'),
                    '',
                    ['booking_id' => $booking->id]
                );

                return redirect($checkoutData['checkout_url']);
            }

            // ── Monnify — return JSON with SDK config, no redirect ─────────────
            if ($request->payment_method === 'monnify') {
                $reference = $this->monnify->generateReference('BKG');
                $booking->update(['payment_reference' => $reference]);
                $this->monnify->createTransaction($user, $totalCharge, 'order_payment', $reference);
                DB::commit();

                // If the request expects JSON (AJAX), return SDK config
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success'          => true,
                        'paymentReference' => $reference,
                        'amount'           => $totalCharge,
                        'customerName'     => $user->full_name,
                        'email'            => $user->email,
                        'apiKey'           => config('services.monnify.api_key'),
                        'contractCode'     => config('services.monnify.contract_code'),
                        'bookingId'        => $booking->id,
                    ]);
                }

                // Fallback: regular form submit — store config in session and redirect
                session([
                    'monnify_booking_config' => [
                        'paymentReference' => $reference,
                        'amount'           => $totalCharge,
                        'customerName'     => $user->full_name,
                        'email'            => $user->email,
                        'apiKey'           => config('services.monnify.api_key'),
                        'contractCode'     => config('services.monnify.contract_code'),
                        'bookingId'        => $booking->id,
                    ],
                ]);

                return redirect()->route('rider.booking.monnify.pay');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('RiderBooking book error: ' . $e->getMessage());
            return back()->with('error', 'Booking failed. Please try again.');
        }
    }

    // -------------------------------------------------------------------------
    // Monnify — AJAX verify after SDK onComplete
    // -------------------------------------------------------------------------

    public function monnifyVerify(Request $request)
    {
        $request->validate([
            'reference'  => ['required', 'string'],
            'booking_id' => ['required'],
        ]);

        $reference = $request->reference;
        $bookingId = $request->booking_id;

        $txn = MonnifyTransaction::where('reference', $reference)->first()
            ?? MonnifyTransaction::where('monnify_reference', $reference)->first();

        if (!$txn) {
            return response()->json(['success' => false, 'message' => 'Transaction not found.'], 404);
        }

        // Idempotent
        if ($txn->status === 'success') {
            return response()->json([
                'success'     => true,
                'message'     => 'Payment already confirmed.',
                'redirect_url'=> route('buyer.bookings.show', $bookingId),
            ]);
        }

        try {
            $data = $this->monnify->verifyTransaction($txn->reference);

            \Log::info('RiderBooking Monnify verify', [
                'reference'     => $reference,
                'paymentStatus' => $data['paymentStatus'] ?? 'unknown',
            ]);

            if (($data['paymentStatus'] ?? '') === 'PAID') {
                $user    = auth('web')->user();
                $amount  = (float) ($data['amountPaid'] ?? $txn->amount);
                $booking = DeliveryBooking::findOrFail($bookingId);

                $txn->update([
                    'status'            => 'success',
                    'monnify_reference' => $data['transactionReference'] ?? null,
                    'gateway_response'  => $data,
                ]);

                $booking->update(['payment_status' => 'paid']);

                // Book with Shipbubble
                $this->bookWithShipbubble($booking, null, $user);

                $user = auth('web')->user();

                if ($user->phone) {
                    $termii = app(\App\Services\TermiiService::class);
                    $termii->sendBulk(
                        [ltrim($user->phone, '+')],
                        "Hi {$user->first_name}, your order #{$order->order_number} has been placed successfully! Total: ₦" . number_format($order->total, 2) . ". Thank you for shopping with us."
                    );
                }

                return response()->json([
                    'success'      => true,
                    'message'      => "Payment confirmed! Booking #{$booking->booking_number} is being processed.",
                    'redirect_url' => route('buyer.bookings.show', $booking->id),
                ]);
            }

            $txn->update(['status' => 'failed', 'gateway_response' => $data]);

            return response()->json([
                'success' => false,
                'message' => 'Payment status: ' . ($data['paymentStatus'] ?? 'unknown'),
            ]);

        } catch (\Exception $e) {
            \Log::error('RiderBooking Monnify verify error', [
                'error'     => $e->getMessage(),
                'reference' => $reference,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not verify payment. Contact support if you were charged.',
            ], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Korapay callback (unchanged)
    // -------------------------------------------------------------------------

    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('rider.booking')->with('error', 'Invalid reference.');
        }

        try {
            $data = $this->korapay->verifyTransaction($reference);

            if ($data['status'] === 'success') {
                $booking = DeliveryBooking::where('payment_reference', $reference)->first();

                if ($booking) {
                    $booking->update(['payment_status' => 'paid']);
                    $this->bookWithShipbubble($booking, null, auth('web')->user());

                $user = auth('web')->user();

                if ($user && $user->phone) {
                    $totalAmount = $booking->fee + $booking->service_fee;
                    $termii = app(\App\Services\TermiiService::class);
                    $termii->sendBulk(
                        [ltrim($user->phone, '+')],
                        "Hi {$user->first_name}, your delivery booking #{$booking->booking_number} has been confirmed! Total: ₦" . number_format($totalAmount, 2) . ". We'll notify you once the shipment is picked up. Thank you!"
                    );
                }

                    return redirect()->route('buyer.bookings.show', $booking->id)
                        ->with('success', "Payment successful! Booking #{$booking->booking_number} confirmed.");
                }
            }

            return redirect()->route('rider.booking')->with('error', 'Payment could not be verified.');

        } catch (\Exception $e) {
            return redirect()->route('rider.booking')->with('error', 'Callback error: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Tracking, listings, show (unchanged)
    // -------------------------------------------------------------------------

    public function track(DeliveryBooking $booking)
    {
        if ($booking->user_id !== auth('web')->id()) abort(403);

        $trackingEvents = [];

        if ($booking->shipbubble_shipment_id) {
            $trackingData = $this->shipbubble->track($booking->shipbubble_shipment_id);

            $apiStatus = $trackingData['status'] ?? null;
            if ($apiStatus) {
                $mappedStatus = match(strtolower($apiStatus)) {
                    'cancelled'              => 'cancelled',
                    'completed'              => 'delivered',
                    'picked_up', 'in_transit'=> 'in_transit',
                    'confirmed'              => 'confirmed',
                    default                  => $booking->status,
                };
                $booking->update(['status' => $mappedStatus]);
            }

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
            $trackingEvents = $booking->tracking()->orderByDesc('event_at')->get()->toArray();
        }

        $booking->refresh();

        return view('storefront.rider-tracking', compact('booking', 'trackingEvents'));
    }

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

    // -------------------------------------------------------------------------
    // Internal helpers (unchanged)
    // -------------------------------------------------------------------------

    protected function bookWithShipbubble(DeliveryBooking $booking, ?Request $request, $user): void
    {
        $requestToken = session('rider_request_token');
        if (!$requestToken) return;

        try {
            $shipment = $this->shipbubble->createShipment(
                $booking->service_code,
                $booking->courier_id,
                [
                    'name'    => $user->full_name,
                    'email'   => $user->email,
                    'phone'   => $user->phone ?? '',
                    'address' => $booking->pickup_address,
                    'city'    => $booking->pickup_city,
                    'state'   => $booking->pickup_city,
                    'country' => $booking->pickup_country,
                ],
                [
                    'name'    => 'Recipient',
                    'email'   => $user->email,
                    'phone'   => '',
                    'address' => $booking->delivery_address,
                    'city'    => $booking->delivery_city,
                    'state'   => $booking->delivery_city,
                    'country' => $booking->delivery_country,
                ],
                [
                    'weight' => $booking->weight_kg ?? 0.5,
                    'length' => 20,
                    'width'  => 20,
                    'height' => 20,
                    'items'  => [[
                        'name'     => $booking->item_description,
                        'quantity' => 1,
                        'value'    => $booking->declared_value ?? 10,
                    ]],
                ],
                $requestToken
            );

            $booking->update([
                'shipbubble_shipment_id'  => $shipment['order_id'] ?? null,
                'tracking_number'         => $shipment['courier']['tracking_code'] ?? null,
                'tracking_url'            => $shipment['tracking_url'] ?? null,
                'estimated_delivery_date' => $shipment['estimated_delivery_date'] ?? null,
                'status'                  => 'confirmed',
                'payment_status'          => 'paid',
            ]);

            session()->forget('rider_request_token');

        } catch (\Exception $e) {
            \Log::error('Shipbubble booking error for BKG #' . $booking->booking_number . ': ' . $e->getMessage());
            $booking->update(['status' => 'pending']);
        }
    }
}