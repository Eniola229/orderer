<?php

namespace App\Services;

use App\Models\MonnifyTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MonnifyService
{
    protected string $apiKey;
    protected string $secretKey;
    protected string $contractCode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey       = config('services.monnify.api_key');
        $this->secretKey    = config('services.monnify.secret_key');
        $this->contractCode = config('services.monnify.contract_code');
        $this->baseUrl      = config('services.monnify.base_url');
    }

    // -------------------------------------------------------------------------
    // Authentication
    // -------------------------------------------------------------------------

    protected function cacheKey(): string
    {
        return 'monnify_access_token_' . md5($this->apiKey);
    }

    /**
     * Fetch a fresh token from Monnify, cache it, and return it.
     * Always clears any existing cached token first.
     */
    public function refreshAccessToken(): string
    {
        Cache::forget($this->cacheKey());

        $credentials = base64_encode($this->apiKey . ':' . $this->secretKey);

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type'  => 'application/json',
        ])->post($this->baseUrl . '/api/v1/auth/login');

        if (!$response->successful()) {
            throw new \Exception('Monnify authentication failed: ' . $response->body());
        }

        $body      = $response->json();
        $token     = $body['responseBody']['accessToken']  ?? null;
        $expiresIn = $body['responseBody']['expiresIn']    ?? 3600;

        if (!$token) {
            throw new \Exception('Monnify auth response missing accessToken: ' . $response->body());
        }

        // Cache for (expiresIn - 5 min) seconds, minimum 60s
        $ttl = max(60, (int) $expiresIn - 300);
        Cache::put($this->cacheKey(), $token, $ttl);

        \Log::info('Monnify: new access token cached', ['ttl' => $ttl, 'expiresIn' => $expiresIn]);

        return $token;
    }

    /**
     * Return a valid access token, fetching a fresh one if none is cached.
     */
    public function getAccessToken(): string
    {
        return Cache::get($this->cacheKey()) ?? $this->refreshAccessToken();
    }

    /**
     * Return pre-built HTTP headers with a valid Bearer token.
     */
    protected function authHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type'  => 'application/json',
        ];
    }

    /**
     * Central HTTP helper that automatically retries once on 401
     * by refreshing the token and re-sending the request.
     *
     * Usage:
     *   $response = $this->request('get', '/api/v2/...', ['query' => [...]);
     *   $response = $this->request('post', '/api/v1/...', ['json' => [...]]);
     */
    protected function request(string $method, string $endpoint, array $options = []): \Illuminate\Http\Client\Response
    {
        $send = function () use ($method, $endpoint, $options) {
            $http = Http::withHeaders($this->authHeaders());

            $url = $this->baseUrl . $endpoint;

            return match (strtolower($method)) {
                'get'  => $http->get($url, $options['query'] ?? []),
                'post' => $http->post($url, $options['json'] ?? []),
                'put'  => $http->put($url, $options['json'] ?? []),
                default => throw new \Exception("Unsupported HTTP method: {$method}"),
            };
        };

        $response = $send();

        // Token was rejected — refresh once and retry
        if ($response->status() === 401) {
            \Log::warning('Monnify 401 — refreshing token and retrying', ['endpoint' => $endpoint]);
            $this->refreshAccessToken();
            $response = $send();
        }

        return $response;
    }

    // -------------------------------------------------------------------------
    // Collections (Accept Payments)
    // -------------------------------------------------------------------------

    public function initializeCheckout(
        string $email,
        string $customerName,
        float  $amount,
        string $reference,
        string $redirectUrl,
        string $paymentDescription = 'Payment',
        array  $metadata = [],
        array  $paymentMethods = ['CARD', 'ACCOUNT_TRANSFER', 'USSD', 'PHONE_NUMBER']
    ): array {
        $payload = [
            'amount'             => $amount,
            'customerName'       => $customerName,
            'customerEmail'      => $email,
            'paymentReference'   => $reference,
            'paymentDescription' => $paymentDescription,
            'currencyCode'       => 'NGN',
            'contractCode'       => $this->contractCode,
            'redirectUrl'        => $redirectUrl,
            'paymentMethods'     => $paymentMethods,
        ];

        if (!empty($metadata)) {
            $payload['metadata'] = $metadata;
        }

        $response = $this->request('post', '/api/v1/merchant/transactions/init-transaction', [
            'json' => $payload,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Monnify initialization failed: ' . $response->body());
        }

        return $response->json('responseBody');
    }

    public function verifyTransaction(string $paymentReference): array
    {
        $response = $this->request('get', '/api/v2/merchant/transactions/query', [
            'query' => ['paymentReference' => $paymentReference],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Monnify verification failed: ' . $response->body());
        }

        return $response->json('responseBody');
    }

    // -------------------------------------------------------------------------
    // Webhooks
    // -------------------------------------------------------------------------

    public function verifyWebhookSignature(string $rawBody, string $signature): bool
    {
        $computed = hash_hmac('sha512', $rawBody, $this->secretKey);
        return hash_equals($computed, $signature);
    }

    // -------------------------------------------------------------------------
    // Disbursements (Payouts)
    // -------------------------------------------------------------------------

    public function resolveBankAccount(string $bankCode, string $accountNumber): array
    {
        $response = $this->request('post', '/api/v1/disbursements/account/validate', [
            'json' => [
                'accountNumber' => $accountNumber,
                'bankCode'      => $bankCode,
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Monnify bank resolve failed: ' . $response->body());
        }

        return $response->json('responseBody');
    }

    public function disbursePayout(
        string $reference,
        float  $amount,
        string $destinationBankCode,
        string $destinationAccountNumber,
        string $destinationAccountName,
        string $narration = 'Payout',
        string $sourceAccountNumber = '',
        bool   $async = false,
        array  $metadata = []
    ): array {
        $payload = [
            'amount'                   => $amount,
            'reference'                => $reference,
            'narration'                => $narration,
            'destinationBankCode'      => $destinationBankCode,
            'destinationAccountNumber' => $destinationAccountNumber,
            'destinationAccountName'   => $destinationAccountName,
            'currency'                 => 'NGN',
            'async'                    => $async,
        ];

        if (!empty($sourceAccountNumber)) {
            $payload['sourceAccountNumber'] = $sourceAccountNumber;
        }

        if (!empty($metadata)) {
            $payload['metadata'] = $metadata;
        }

        \Log::info('Monnify payout request', ['reference' => $reference, 'payload' => $payload]);

        $response = $this->request('post', '/api/v2/disbursements/single', ['json' => $payload]);

        \Log::info('Monnify payout response', [
            'status_code'  => $response->status(),
            'body'         => $response->body(),
            'successful'   => $response->successful(),
            'server_error' => $response->serverError(),
            'client_error' => $response->clientError(),
        ]);

        if ($response->clientError()) {
            $errorData    = $response->json();
            $errorMessage = $errorData['responseMessage'] ?? 'Validation error';
            $errorCode    = $errorData['responseCode']    ?? '';
            throw new \Exception("Monnify validation error [{$errorCode}]: {$errorMessage}");
        }

        if ($response->serverError()) {
            throw new \Exception(
                'Monnify server error — call verifyPayout() to confirm status before marking failed: '
                . $response->body()
            );
        }

        if (!$response->successful()) {
            throw new \Exception('Monnify payout failed: ' . $response->body());
        }

        return $response->json('responseBody');
    }

    public function authorizeTransfer(string $reference, string $authorizationCode): array
    {
        $response = $this->request('post', '/api/v2/disbursements/single/validate-otp', [
            'json' => [
                'reference'         => $reference,
                'authorizationCode' => $authorizationCode,
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Monnify OTP authorization failed: ' . $response->body());
        }

        return $response->json('responseBody');
    }

    public function resendTransferOtp(string $reference): array
    {
        $response = $this->request('post', '/api/v2/disbursements/single/resend-otp', [
            'json' => ['reference' => $reference],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Monnify resend OTP failed: ' . $response->body());
        }

        return $response->json('responseBody');
    }

    public function verifyPayout(string $reference): array
    {
        $response = $this->request('get', '/api/v2/disbursements/single/summary', [
            'query' => ['reference' => $reference],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Monnify payout verification failed: ' . $response->body());
        }

        return $response->json('responseBody');
    }

    // -------------------------------------------------------------------------
    // Utility / Misc
    // -------------------------------------------------------------------------

    public function getBanks(): array
    {
        $response = $this->request('get', '/api/v1/banks');

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch Monnify banks: ' . $response->body());
        }

        return $response->json('responseBody');
    }

    public function getWalletBalance(string $accountNumber): array
    {
        $response = $this->request('get', '/api/v2/disbursements/wallet-balance', [
            'query' => ['accountNumber' => $accountNumber],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch Monnify wallet balance: ' . $response->body());
        }

        return $response->json('responseBody');
    }

    public function generateReference(string $prefix = 'ORD'): string
    {
        return $prefix . '-' . strtoupper(Str::random(16));
    }

    public function createTransaction(
        $payable,
        float  $amount,
        string $type,
        string $reference
    ): MonnifyTransaction {
        return MonnifyTransaction::create([
            'reference'    => $reference,
            'payable_type' => get_class($payable),
            'payable_id'   => $payable->id,
            'amount'       => $amount,
            'currency'     => 'NGN',
            'type'         => $type,
            'status'       => 'pending',
        ]);
    }
}