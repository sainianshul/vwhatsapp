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
     * Send a text message using the microservice
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

        try {
            // High timeout for safety
            $response = Http::timeout(120)->post("{$this->nodeUrl}/api/messages/send", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['data']['messageId'] ?? null,
                    'error' => null
                ];
            } else {
                return [
                    'success' => false,
                    'message_id' => null,
                    'error' => $response->json('message') ?? 'Unknown error from microservice.'
                ];
            }
        } catch (\Exception $e) {
            Log::error("WhatsAppService sendMessage Error: " . $e->getMessage());
            return [
                'success' => false,
                'message_id' => null,
                'error' => $e->getMessage()
            ];
        }
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

        try {
            // Higher timeout for media (files can be large)
            $response = Http::timeout(180)->post("{$this->nodeUrl}/api/messages/send-media", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['data']['messageId'] ?? null,
                    'error' => null
                ];
            } else {
                return [
                    'success' => false,
                    'message_id' => null,
                    'error' => $response->json('message') ?? 'Unknown error from microservice.'
                ];
            }
        } catch (\Exception $e) {
            Log::error("WhatsAppService sendMediaMessage Error: " . $e->getMessage());
            return [
                'success' => false,
                'message_id' => null,
                'error' => $e->getMessage()
            ];
        }
    }
}
