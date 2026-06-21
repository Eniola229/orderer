<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TermiiService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $senderId;
    protected string $channel;

    public function __construct()
    {
        $this->apiKey   = config('services.termii.api_key');
        $this->baseUrl  = rtrim(config('services.termii.base_url', 'https://v3.api.termii.com'), '/');
        $this->senderId = config('services.termii.sender_id', 'YourBrand');
        $this->channel  = config('services.termii.channel', 'generic');
    }

    /**
     * Standardize phone number to Termii format (234XXXXXXXXXX)
     * - Removes '+', ' ', '-', etc.
     * - If starts with '0', replace with '234'
     * - If starts with '234', keep as is
     * - If starts with '+234', remove '+' and keep '234'
     */
    protected function standardizeNumber(string $number): ?string
    {
        // Remove all non-numeric characters except '+'
        $number = preg_replace('/[^0-9+]/', '', $number);
        
        // Remove leading '+'
        $number = ltrim($number, '+');
        
        // If empty after cleaning, return null
        if (empty($number)) {
            return null;
        }
        
        // If starts with '0', replace with '234'
        if (preg_match('/^0/', $number)) {
            $number = '234' . substr($number, 1);
        }
        
        // If starts with '234', keep as is
        if (preg_match('/^234/', $number)) {
            return $number;
        }
        
        // If number is 10 digits (e.g., 8012345678), assume Nigerian and add '234'
        if (strlen($number) === 10 && preg_match('/^[0-9]{10}$/', $number)) {
            return '234' . $number;
        }
        
        // If number is 11 digits starting with 0 (e.g., 08012345678), replace 0 with 234
        if (strlen($number) === 11 && preg_match('/^0[0-9]{10}$/', $number)) {
            return '234' . substr($number, 1);
        }
        
        // If number is 13 digits starting with 234, it's already correct
        if (strlen($number) === 13 && preg_match('/^234[0-9]{10}$/', $number)) {
            return $number;
        }
        
        // Log warning for unusual formats
        Log::warning('TermiiService: unusual phone number format', [
            'original' => $number,
            'cleaned'  => $number,
        ]);
        
        return $number;
    }

    /**
     * Standardize multiple phone numbers
     */
    protected function standardizeNumbers(array $numbers): array
    {
        $standardized = [];
        
        foreach ($numbers as $number) {
            $std = $this->standardizeNumber($number);
            if ($std) {
                $standardized[] = $std;
            } else {
                Log::warning('TermiiService: skipping invalid phone number', [
                    'number' => $number,
                ]);
            }
        }
        
        return array_values(array_unique($standardized));
    }

    public function sendBulk(array $numbers, string $message): array
    {
        // Clean and standardize numbers
        $numbers = array_map('trim', $numbers);
        $numbers = $this->standardizeNumbers($numbers);

        if (empty($numbers)) {
            Log::warning('Termii sendBulk: no valid numbers to send to');
            return ['sent' => 0, 'failed' => 0, 'errors' => ['No valid phone numbers']];
        }

        // Log::info('Termii sendBulk: standardized numbers', [
        //     'total'   => count($numbers),
        //     'numbers' => $numbers,
        // ]);

        $chunks = array_chunk($numbers, 100);

        $sent   = 0;
        $failed = 0;
        $errors = [];

        foreach ($chunks as $index => $chunk) {
            try {
                $payload = [
                    'api_key' => $this->apiKey,
                    'to'      => $chunk,
                    'from'    => $this->senderId,
                    'sms'     => $message,
                    'type'    => 'plain',
                    'channel' => $this->channel,
                ];

                $response = Http::timeout(30)
                    ->post("{$this->baseUrl}/api/sms/send/bulk", $payload);

                $body = $response->json();

                if ($response->successful() && isset($body['code']) && strtolower($body['code']) === 'ok') {
                    $sent += count($chunk);
                } else {
                    $failed += count($chunk);
                    $errors[] = $body['message'] ?? ('HTTP ' . $response->status());
                    Log::warning("Termii sendBulk: chunk " . ($index + 1) . " failed", [
                        'failed_numbers' => $chunk,
                        'response'       => $body,
                    ]);
                }
            } catch (\Throwable $e) {
                $failed += count($chunk);
                $errors[] = $e->getMessage();
                Log::error("Termii sendBulk: exception on chunk " . ($index + 1), [
                    'error'          => $e->getMessage(),
                    'failed_numbers' => $chunk,
                ]);
            }
        }

        return compact('sent', 'failed', 'errors');
    }

    /**
     * Send SMS to a single recipient
     */
    public function send(string $number, string $message): array
    {
        $result = $this->sendBulk([$number], $message);
        
        return [
            'success' => $result['sent'] > 0,
            'message' => $result['errors'][0] ?? ($result['sent'] > 0 ? 'Sent successfully' : 'Failed to send'),
        ];
    }


    public function sendOtp(string $phone): array
    {
        $number = $this->standardizeNumber($phone);
        if (!$number) {
            return ['success' => false, 'message' => 'Invalid phone number.'];
        }

        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/api/sms/otp/send", [
                'api_key'          => $this->apiKey,
                'message_type'     => 'NUMERIC',
                'to'               => $number,
                'from'             => $this->senderId,
                'channel'          => $this->channel,
                'pin_attempts'     => 3,
                'pin_time_to_live' => 10,
                'pin_length'       => 6,
                'pin_placeholder'  => '< 123456 >',
                'message_text'     => 'Your Orderer verification code is < 123456 >. It expires in 10 minutes.',
                'pin_type'         => 'NUMERIC',
            ]);

            $body = $response->json();

            if ($response->successful() && !empty($body['pinId'])) {
                return ['success' => true, 'pin_id' => $body['pinId']];
            }

            Log::warning('Termii sendOtp failed', ['response' => $body]);
            return ['success' => false, 'message' => $body['message'] ?? 'Could not send verification code.'];

        } catch (\Throwable $e) {
            Log::error('Termii sendOtp exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Could not send verification code. Please try again.'];
        }
    }

    public function verifyOtp(string $pinId, string $pin): array
    {
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/api/sms/otp/verify", [
                'api_key' => $this->apiKey,
                'pin_id'  => $pinId,
                'pin'     => $pin,
            ]);

            $body = $response->json();

            $verified = $body['verified'] ?? false;
            if ($response->successful() && ($verified === true || $verified === 'True' || $verified === 'true')) {
                return ['success' => true];
            }

            Log::warning('Termii verifyOtp failed', ['response' => $body]);
            return ['success' => false, 'message' => $body['msg'] ?? 'Invalid or expired code.'];

        } catch (\Throwable $e) {
            Log::error('Termii verifyOtp exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Could not verify code. Please try again.'];
        }
    }
}