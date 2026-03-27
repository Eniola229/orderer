<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ShipbubbleService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.shipbubble.api_key');
        $this->baseUrl = config('services.shipbubble.base_url');
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    /**
     * Validate a Nigerian address — Shipbubble requires address validation
     * before getting rates.
     */
    public function validateAddress(array $address): array
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/shipping/address/validate', $address);

        if (!$response->successful()) {
            throw new \Exception('Address validation failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get available shipping rates for a shipment.
     */
    public function getRates(array $data): array
    {
        $payload = [
            'sender_address_code'   => $data['sender_address_code'],
            'reciever_address_code' => $data['reciever_address_code'], 
            'pickup_date'           => now()->format('Y-m-d'),

            'category_id'           => $data['category_id'],

            'package_items' => [
                [
                    'name'        => $data['item_name'] ?? 'Package',
                    'description' => 'General goods',
                    'unit_weight' => $data['weight'],
                    'unit_amount' => $data['value'],
                    'quantity'    => 1,
                ]
            ],

            'package_dimension' => [
                'length' => $data['length'],
                'width'  => $data['width'],
                'height' => $data['height'],
            ],
        ];

        //\Log::info('Shipbubble PAYLOAD', $payload);

        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/shipping/fetch_rates', $payload);

        if (!$response->successful()) {
            throw new \Exception('Could not fetch shipping rates: ' . $response->body());
        }

        return $response->json('data') ?? [];
    }

    /**
     * Create / book a shipment after the user has selected a rate.
     */
    public function createShipment(
        string $serviceCode,
        string $courierId,  // Add this parameter
        array  $sender,
        array  $recipient,
        array  $package,
        string $requestToken
    ): array {
        $payload = [
            'courier_id'        => $courierId,  // Add courier_id
            'service_code'      => $serviceCode,
            'sender_details'    => $sender,
            'recipient_details' => $recipient,
            'package_details'   => $package,
            'request_token'     => $requestToken,
        ];

        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/shipping/labels', $payload);

        if (!$response->successful()) {
            throw new \Exception('Shipment booking failed: ' . $response->body());
        }

        return $response->json('data') ?? [];
    }

    /**
     * Track a shipment by Shipbubble order_id (e.g. "SB-BB7EDE9F").
     * The tracking endpoint is GET /shipping/labels?order_id=...
     */
    public function track(string $orderId): array
    {
        // \Log::info('Shipbubble track() — request', [
        //     'order_id' => $orderId,
        //     'url'      => $this->baseUrl . '/shipping/labels/list/' . $orderId,
        // ]);

        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl . '/shipping/labels/list/' . $orderId);

        // \Log::info('Shipbubble track() — raw response', [
        //     'status' => $response->status(),
        //     'body'   => $response->body(),
        // ]);

        if (!$response->successful()) {
            throw new \Exception('Tracking failed: ' . $response->body());
        }

        $body = $response->json();

        //  API returns data as a plain array [], not data.results[]
        $result = $body['data'][0] ?? [];

        //\Log::info('Shipbubble track() — parsed result', $result);

        return [
            'status'         => $result['status'] ?? 'unknown',
            'package_status' => $result['package_status'] ?? [],  // ← use this when events is empty
            'events'         => $result['events'] ?? [],
            'tracking_url'   => $result['tracking_url'] ?? null,
            'tracking_code'  => $result['courier']['tracking_code'] ?? null,
            'waybill'        => $result['waybill_document'] ?? null,
            'courier'        => $result['courier'] ?? [],
        ];
    }
    /**
     * Cancel a shipment.
     */
    public function cancelShipment(string $shipmentId): bool
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl . '/shipping/cancel', [
                'shipment_id' => $shipmentId,
            ]);

        return $response->successful();
    }

    /**
     * Build a standardised sender array from a Seller model.
     */
    public function buildSenderFromSeller(\App\Models\Seller $seller): array
    {
        return [
            'name'    => $seller->business_name,
            'email'   => $seller->email,
            'phone'   => $seller->phone ?? '',
            'address' => $seller->business_address ?? 'Lagos',
            'city'    => 'Lagos',
            'state'   => 'Lagos',
            'country' => 'NG',
        ];
    }

    /**
     * Build a standardised recipient array from an Order.
     */
    public function buildRecipientFromOrder(\App\Models\Order $order): array
    {
        return [
            'name'    => $order->shipping_name,
            'email'   => $order->user->email ?? '',
            'phone'   => $order->shipping_phone,
            'address' => $order->shipping_address,
            'city'    => $order->shipping_city,
            'state'   => $order->shipping_state,
            'country' => $order->shipping_country ?? 'NG',
        ];
    }
}