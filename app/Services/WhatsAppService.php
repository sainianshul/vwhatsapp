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
}
