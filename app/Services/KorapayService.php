<?php

namespace App\Services;

use App\Models\KorapayTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class KorapayService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.korapay.secret_key');
        $this->publicKey = config('services.korapay.public_key');
        // Base URL should be: https://api.korapay.com/merchant/api/v1
        $this->baseUrl   = config('services.korapay.base_url');
    }

    /**
     * Initialize a checkout session (Checkout Redirect flow).
     * Returns ['checkout_url' => '...', 'reference' => '...']
     */
    public function initializeCheckout(
        string $email,
        string $customerName,
        float  $amount,
        string $reference,
        string $redirectUrl,
        string $notificationUrl = '',  // ← webhook URL per Korapay docs
        array  $metadata = []
    ): array {
        $payload = [
            'reference'    => $reference,
            'amount'       => $amount,          // Korapay expects a number (e.g. 10.00 for USD)
            'currency'     => 'USD',
            'customer'     => [
                'email' => $email,
                'name'  => $customerName,       // ← docs support customer.name
            ],
            'redirect_url' => $redirectUrl,
        ];

        // Only add optional fields if provided
        if (!empty($notificationUrl)) {
            $payload['notification_url'] = $notificationUrl;
        }

        if (!empty($metadata)) {
            $payload['metadata'] = $metadata;   // max 5 keys, key names max 20 chars
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->post($this->baseUrl . '/charges/initialize', $payload);
        // ↑ baseUrl must already include /merchant/api/v1
        // e.g. https://api.korapay.com/merchant/api/v1

        if (!$response->successful()) {
            throw new \Exception('Korapay initialization failed: ' . $response->body());
        }

        // Korapay returns: { "status": true, "data": { "reference": "...", "checkout_url": "..." } }
        return $response->json('data');
    }

    /**
     * Verify a transaction by reference.
     * Always call this SERVER-SIDE after redirect, never from frontend.
     */
    public function verifyTransaction(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])->get($this->baseUrl . '/charges/' . $reference);

        if (!$response->successful()) {
            throw new \Exception('Korapay verification failed: ' . $response->body());
        }

        // Returns full transaction data; check ['data']['status'] === 'success'
        return $response->json('data');
    }

    /**
     * Verify webhook signature.
     *
     * IMPORTANT: Korapay signs ONLY the `data` object from the payload,
     * not the entire raw body. Header key is: x-korapay-signature
     *
     * Usage in your webhook controller:
     *   $payload   = json_decode($request->getContent(), true);
     *   $signature = $request->header('x-korapay-signature');
     *   $valid     = $this->korapay->verifyWebhookSignature($payload['data'], $signature);
     */
    public function verifyWebhookSignature(array $dataObject, string $signature): bool
    {
        // Must use json_encode on the data object, not the raw body string
        $computed = hash_hmac('sha256', json_encode($dataObject), $this->secretKey);
        return hash_equals($computed, $signature);
    }

    /**
     * Generate a unique payment reference.
     */
    public function generateReference(string $prefix = 'ORD'): string
    {
        return $prefix . '-' . strtoupper(Str::random(16));
    }

    /**
     * Create a pending transaction record in the database.
     */
    public function createTransaction(
        $payable,
        float  $amount,
        string $type,
        string $reference
    ): KorapayTransaction {
        return KorapayTransaction::create([
            'reference'    => $reference,
            'payable_type' => get_class($payable),
            'payable_id'   => $payable->id,
            'amount'       => $amount,
            'currency'     => 'USD',
            'type'         => $type,
            'status'       => 'pending',
        ]);
    }
}