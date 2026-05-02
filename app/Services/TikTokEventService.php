<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokEventService
{
    private string $pixelCode;
    private string $accessToken;
    private string $apiUrl = 'https://business-api.tiktok.com/open_api/v1.3/event/track/';

    public function __construct()
    {
        $this->pixelCode   = config('services.tiktok.pixel_id');
        $this->accessToken = config('services.tiktok.access_token');
    }

    /**
     * Send a TikTok server-side event.
     *
     * @param  string       $eventName   e.g. 'UploadProduct', 'AddToCart', 'Purchase'
     * @param  array        $properties  Event-specific properties (content_id, value, etc.)
     * @param  Request      $request     Current HTTP request (for ip, user_agent)
     * @param  mixed|null   $user        Authenticated user/seller model (for PII hashing)
     * @param  string|null  $eventId     Optional deduplication ID
     * @return void
     */
    public function send(
        string $eventName,
        array $properties,
        Request $request,
        mixed $user = null,
        ?string $eventId = null
    ): void {
        // TikTok Events API v1.3 correct payload structure
        $payload = [
            'event_source'    => 'web',
            'event_source_id' => $this->pixelCode,
            'data'            => [
                [
                    'event'      => $eventName,
                    'event_time' => time(),
                    'event_id'   => $eventId ?? uniqid($eventName . '_', true),
                    'properties' => $this->buildProperties($properties),
                    'context'    => [
                        'user' => $this->buildUserContext($user, $request),
                        'page' => [
                            'url' => $request->fullUrl(),
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Access-Token' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, $payload);

            Log::info("TikTok [{$eventName}] event sent", [
                'event_id' => $payload['data'][0]['event_id'],
                'status'   => $response->status(),
                'body'     => $response->json(),
            ]);
        } catch (\Throwable $e) {
            // Never block the user flow for a tracking failure
            Log::error("TikTok [{$eventName}] event failed", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Convenience wrappers — one method per TikTok event
    // -------------------------------------------------------------------------

    public function uploadProduct($product, Request $request, $user = null): void
    {
        $this->send(
            eventName:  'CustomizeProduct',
            properties: $this->productProperties($product),
            request:    $request,
            user:       $user,
            eventId:    'upload_product_' . $product->id,
        );
    }

    public function viewContent($product, Request $request, $user = null): void
    {
        $this->send(
            eventName:  'ViewContent',
            properties: $this->productProperties($product),
            request:    $request,
            user:       $user,
            eventId:    'view_content_' . $product->id . '_' . time(),
        );
    }

    public function addToCart($product, Request $request, $user = null): void
    {
        $this->send(
            eventName:  'AddToCart',
            properties: $this->productProperties($product),
            request:    $request,
            user:       $user,
            eventId:    'add_to_cart_' . $product->id . '_' . $user?->id,
        );
    }

    public function addToWishlist($product, Request $request, $user = null): void
    {
        $this->send(
            eventName:  'AddToWishlist',
            properties: $this->productProperties($product),
            request:    $request,
            user:       $user,
            eventId:    'wishlist_' . $product->id . '_' . $user?->id,
        );
    }

    public function initiateCheckout($order, Request $request, $user = null): void
    {
        $this->send(
            eventName:  'InitiateCheckout',
            properties: $this->orderProperties($order),
            request:    $request,
            user:       $user,
            eventId:    'checkout_' . $order->id,
        );
    }

    public function addPaymentInfo($order, Request $request, $user = null): void
    {
        $this->send(
            eventName:  'AddPaymentInfo',
            properties: $this->orderProperties($order),
            request:    $request,
            user:       $user,
            eventId:    'payment_info_' . $order->id,
        );
    }

    public function placeAnOrder($order, Request $request, $user = null): void
    {
        $this->send(
            eventName:  'PlaceAnOrder',
            properties: $this->orderProperties($order),
            request:    $request,
            user:       $user,
            eventId:    'place_order_' . $order->id,
        );
    }

    public function purchase($order, Request $request, $user = null): void
    {
        $this->send(
            eventName:  'Purchase',
            properties: $this->orderProperties($order),
            request:    $request,
            user:       $user,
            eventId:    'purchase_' . $order->id,
        );
    }

    public function completeRegistration($user, Request $request): void
    {
        $this->send(
            eventName:  'CompleteRegistration',
            properties: [
                'content_name' => 'User Registration',
            ],
            request:    $request,
            user:       $user,
            eventId:    'registration_' . $user->id,
        );
    }

    public function search(string $searchString, Request $request, $user = null): void
    {
        $this->send(
            eventName:  'Search',
            properties: [
                'search_string' => $searchString,
            ],
            request:    $request,
            user:       $user,
        );
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Build and filter the properties array, removing nulls.
     */
    private function buildProperties(array $properties): array
    {
        $defaults = [
            'currency' => config('services.tiktok.currency', 'NGN'),
        ];

        return array_filter(
            array_merge($defaults, $properties),
            fn($v) => $v !== null && $v !== ''
        );
    }

    /**
     * Build the user context, hashing all PII with SHA-256.
     * TikTok v1.3 field names: phone_number (not phone), ip, user_agent, external_id, email
     */
    private function buildUserContext(mixed $user, Request $request): array
    {
        return array_filter([
            'ip'           => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'email'        => $user?->email
                                 ? hash('sha256', strtolower(trim($user->email)))
                                 : null,
            'phone_number' => $user?->phone
                                 ? hash('sha256', preg_replace('/\D/', '', $user->phone))
                                 : null,
            'external_id'  => $user?->id
                                 ? hash('sha256', (string) $user->id)
                                 : null,
        ]);
    }

    /**
     * Standard properties for a single product.
     */
    private function productProperties(mixed $product): array
    {
        return [
            'content_id'   => (string) $product->id,
            'content_type' => 'product',
            'content_name' => $product->name,
            'value'        => (float) ($product->sale_price ?? $product->price),
        ];
    }

    /**
     * Standard properties for an order (multiple items).
     */
    private function orderProperties(mixed $order): array
    {
        return [
            'content_id'   => (string) $order->id,
            'content_type' => 'product',
            'content_name' => 'Order #' . $order->id,
            'value'        => (float) $order->total,
        ];
    }
}