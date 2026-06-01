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

    public function sendBulk(array $numbers, string $message): array
    {
        $numbers = array_values(array_unique(array_filter(array_map('trim', $numbers))));

        // Strip leading + sign to match Termii's required format
        $numbers = array_map(fn($n) => ltrim($n, '+'), $numbers);

        // Log::info('Termii sendBulk: raw numbers received', [
        //     'total'   => count($numbers),
        //     'numbers' => $numbers,
        // ]);

        if (empty($numbers)) {
            Log::warning('Termii sendBulk: no numbers to send to');
            return ['sent' => 0, 'failed' => 0, 'errors' => []];
        }

        $chunks = array_chunk($numbers, 100);

        // Log::info('Termii sendBulk: chunked into batches', [
        //     'total_numbers' => count($numbers),
        //     'total_chunks'  => count($chunks),
        // ]);

        $sent   = 0;
        $failed = 0;
        $errors = [];

        foreach ($chunks as $index => $chunk) {
            Log::info("Termii sendBulk: sending chunk " . ($index + 1) . " of " . count($chunks), [
                'chunk_number' => $index + 1,
                'chunk_size'   => count($chunk),
                'numbers'      => $chunk,
            ]);

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

                // Log::info("Termii sendBulk: response for chunk " . ($index + 1), [
                //     'http_status' => $response->status(),
                //     'body'        => $body,
                //     'numbers'     => $chunk,
                // ]);

                if ($response->successful() && isset($body['code']) && strtolower($body['code']) === 'ok') {
                    $sent += count($chunk);
                    // Log::info("Termii sendBulk: chunk " . ($index + 1) . " sent successfully", [
                    //     'sent_count' => count($chunk),
                    // ]);
                } else {
                    $failed += count($chunk);
                    $errors[] = $body['message'] ?? ('HTTP ' . $response->status());
                    Log::warning("Termii sendBulk: chunk " . ($index + 1) . " failed", [
                        'failed_numbers' => $chunk,
                        'response'       => $body,
                        'http_status'    => $response->status(),
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

        // Log::info('Termii sendBulk: finished', [
        //     'total_numbers' => count($numbers),
        //     'sent'          => $sent,
        //     'failed'        => $failed,
        //     'errors'        => $errors,
        // ]);

        return compact('sent', 'failed', 'errors');
    }
}