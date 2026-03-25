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
        $this->baseUrl   = config('services.korapay.base_url');
    }

    /**
     * Initialize a checkout session
     */
    public function initializeCheckout(
        string $email,
        float  $amount,
        string $reference,
        string $callbackUrl,
        array  $metadata = []
    ): array {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->post($this->baseUrl . '/charges/initialize', [
            'reference'    => $reference,
            'amount'       => $amount,
            'currency'     => 'USD',
            'customer'     => ['email' => $email],
            'redirect_url' => $callbackUrl,
            'metadata'     => $metadata,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Korapay initialization failed: ' . $response->body());
        }

        return $response->json('data');
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])->get($this->baseUrl . '/charges/' . $reference);

        if (!$response->successful()) {
            throw new \Exception('Korapay verification failed.');
        }

        return $response->json('data');
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $computed = hash_hmac('sha256', $payload, $this->secretKey);
        return hash_equals($computed, $signature);
    }

    /**
     * Generate a unique payment reference
     */
    public function generateReference(string $prefix = 'ORD'): string
    {
        return $prefix . '-' . strtoupper(Str::random(16));
    }

    /**
     * Create a pending transaction record
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