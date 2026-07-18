<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $nodeUrl;

    public function __construct()
    {
        $this->nodeUrl = env('NODE_MICROSERVICE_URL', 'http://whatsapp-service:3000');
    }

    /**
     * Send a text message using the microservice.
     * Includes automatic retry on transient failures.
     *
     * @param string $sessionId
     * @param string $receiverNumber
     * @param string $messageText
     * @return array ['success' => bool, 'message_id' => string|null, 'error' => string|null]
     */
    public function sendMessage(string $sessionId, string $receiverNumber, string $messageText)
    {
        $payload = [
            'session_id' => $sessionId,
            'receiver' => $receiverNumber,
            'text' => $messageText,
        ];

        return $this->sendWithRetry(
            "{$this->nodeUrl}/api/messages/send",
            $payload,
            'sendMessage',
            120
        );
    }

    /**
     * Send a media message (image, video, document, audio) using the microservice.
     * Media is read from disk by Node.js — no base64 overhead.
     *
     * @param string $sessionId
     * @param string $receiverNumber
     * @param string $mediaAbsolutePath Absolute path to media file on shared disk
     * @param string $caption Optional text caption
     * @param string|null $filename Optional filename override for documents
     * @return array ['success' => bool, 'message_id' => string|null, 'error' => string|null]
     */
    public function sendMediaMessage(string $sessionId, string $receiverNumber, string $mediaAbsolutePath, string $caption = '', ?string $filename = null)
    {
        $payload = [
            'session_id' => $sessionId,
            'receiver' => $receiverNumber,
            'media_path' => $mediaAbsolutePath,
            'caption' => $caption,
        ];

        if ($filename) {
            $payload['filename'] = $filename;
        }

        return $this->sendWithRetry(
            "{$this->nodeUrl}/api/messages/send-media",
            $payload,
            'sendMediaMessage',
            180
        );
    }

    /**
     * Send a request with automatic retry on transient failures.
     * Retries up to 2 times (3 attempts total) with a 3-second delay.
     *
     * @param string $url
     * @param array $payload
     * @param string $methodName for logging
     * @param int $timeout seconds
     * @return array
     */
    private function sendWithRetry(string $url, array $payload, string $methodName, int $timeout): array
    {
        $maxRetries = 2;
        $retryDelay = 3; // seconds

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::timeout($timeout)->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'message_id' => $data['data']['messageId'] ?? null,
                        'error' => null
                    ];
                }

                $errorMsg = $response->json('message') ?? 'Unknown error from microservice.';

                // Don't retry on user-level errors (invalid number, not registered, etc.)
                if ($response->status() === 400) {
                    return [
                        'success' => false,
                        'message_id' => null,
                        'error' => $errorMsg
                    ];
                }

                // On 500 (session crash, etc.), retry if we have attempts left
                if ($attempt < $maxRetries) {
                    Log::warning("WhatsAppService {$methodName}: Attempt {$attempt} failed ({$errorMsg}), retrying in {$retryDelay}s...");
                    sleep($retryDelay);
                    continue;
                }

                return [
                    'success' => false,
                    'message_id' => null,
                    'error' => $errorMsg
                ];

            } catch (\Exception $e) {
                // Connection errors (timeout, refused, etc.) — retry
                if ($attempt < $maxRetries && $this->isTransientError($e)) {
                    Log::warning("WhatsAppService {$methodName}: Transient error on attempt {$attempt} ({$e->getMessage()}), retrying in {$retryDelay}s...");
                    sleep($retryDelay);
                    continue;
                }

                Log::error("WhatsAppService {$methodName} Error: " . $e->getMessage());
                return [
                    'success' => false,
                    'message_id' => null,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'success' => false,
            'message_id' => null,
            'error' => 'Failed after all retry attempts.'
        ];
    }

    /**
     * Determine if an exception represents a transient (retryable) error.
     */
    private function isTransientError(\Exception $e): bool
    {
        $msg = strtolower($e->getMessage());
        return str_contains($msg, 'connection refused') ||
               str_contains($msg, 'timed out') ||
               str_contains($msg, 'timeout') ||
               str_contains($msg, 'could not resolve') ||
               str_contains($msg, 'connection reset');
    }
}
