<?php
// =====================================================
// app/Http/Controllers/RiderBookingController.php
// =====================================================
namespace App\Http\Controllers;

use App\Models\DeliveryBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RiderBookingController extends Controller
{
    public function book(Request $request)
    {
        if (!auth('web')->check()) {
            return redirect()->route('login')
                ->with('info', 'Please sign in to book a rider.');
        }

        $request->validate([
            'delivery_type'    => ['required', 'in:local,international'],
            'item_description' => ['required', 'string', 'max:255'],
            'pickup_address'   => ['required', 'string'],
            'pickup_city'      => ['required', 'string', 'max:100'],
            'delivery_address' => ['required', 'string'],
            'delivery_city'    => ['required', 'string', 'max:100'],
            'weight_kg'        => ['nullable', 'numeric', 'min:0.1'],
        ]);

        // Parse city/country from combined field
        $pickupParts   = explode(',', $request->pickup_city);
        $deliveryParts = explode(',', $request->delivery_city);

        DeliveryBooking::create([
            'booking_number'   => 'BKG-' . strtoupper(Str::random(8)),
            'user_id'          => auth('web')->id(),
            'delivery_type'    => $request->delivery_type,
            'pickup_address'   => $request->pickup_address,
            'pickup_city'      => trim($pickupParts[0] ?? $request->pickup_city),
            'pickup_country'   => trim($pickupParts[1] ?? 'NG'),
            'delivery_address' => $request->delivery_address,
            'delivery_city'    => trim($deliveryParts[0] ?? $request->delivery_city),
            'delivery_country' => trim($deliveryParts[1] ?? 'NG'),
            'item_description' => $request->item_description,
            'weight_kg'        => $request->weight_kg,
            'status'           => 'pending',
            'payment_status'   => 'pending',
        ]);

        return back()->with('success', 'Booking submitted! A rider will be assigned shortly. You will be contacted for pricing.');
    }
}
