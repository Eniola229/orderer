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

    /**
     * Fetch list of banks for a given country code.
     * countryCode: NG, KE, ZA, GH, etc.
     */
    public function getBanks(string $countryCode): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->publicKey,
        ])->get($this->baseUrl . '/misc/banks', ['countryCode' => $countryCode]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch banks: ' . $response->body());
        }

        return $response->json('data');
    }

    /**
     * Fetch mobile money operators for a given country code.
     * countryCode: KE, GH, etc.
     */
    public function getMobileMoneyOperators(string $countryCode): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])->get($this->baseUrl . '/misc/mobile-money', ['countryCode' => $countryCode]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch MMOs: ' . $response->body());
        }

        return $response->json('data');
    }

    /**
     * Resolve (verify) a bank account before payout — optional but recommended.
     * currency: NG or KE
     */
    public function resolveBankAccount(string $bankCode, string $accountNumber, string $currency): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->post($this->baseUrl . '/misc/banks/resolve', [
            'bank'     => $bankCode,
            'account'  => $accountNumber,
            'currency' => $currency,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Bank resolve failed: ' . $response->body());
        }

        return $response->json('data');
    }

    /**
     * Disburse a payout to a seller's bank account.
     *
     * For local currency payouts (NGN, KES, GHS, ZAR, etc.) funded from your USD balance,
     * Korapay handles the FX automatically.
     *
     * $destination shape:
     * [
     *   'type'         => 'bank_account',          // or 'mobile_money'
     *   'amount'       => 50.00,                   // amount in destination currency
     *   'currency'     => 'NGN',                   // destination currency
     *   'narration'    => 'Withdrawal payout',
     *   'bank_account' => [
     *       'bank'    => '044',                    // bank code from getBanks()
     *       'account' => '0123456789',
     *   ],
     *   // OR for mobile money:
     *   'mobile_money' => [
     *       'operator'      => 'safaricom-ke',     // slug from getMobileMoneyOperators()
     *       'mobile_number' => '254712345678',
     *   ],
     *   'customer' => [
     *       'name'  => 'John Doe',
     *       'email' => 'john@example.com',
     *   ],
     * ]
     */
    public function disbursePayout(string $reference, array $destination, array $metadata = []): array
    {
        $payload = [
            'reference'   => $reference,
            'destination' => $destination,
        ];

        if (!empty($metadata)) {
            $payload['metadata'] = $metadata;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ])->post($this->baseUrl . '/transactions/disburse', $payload);

        // IMPORTANT: Do NOT treat 502/503/504/500 as failed — always verify first.
        if ($response->serverError()) {
            // Re-check status via verifyPayout() before marking failed
            throw new \Exception('Korapay server error — verify payout before marking failed: ' . $response->body());
        }

        if (!$response->successful()) {
            throw new \Exception('Korapay payout failed: ' . $response->body());
        }

        // Returns: { status, message, data: { amount, fee, currency, status, reference, ... } }
        return $response->json('data');
    }

    /**
     * Query a payout's current status by reference.
     * Always call this after any server error before treating as failed.
     */
    public function verifyPayout(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])->get($this->baseUrl . '/transactions/' . $reference);

        if (!$response->successful()) {
            throw new \Exception('Korapay payout verification failed: ' . $response->body());
        }

        return $response->json('data');
    }
}