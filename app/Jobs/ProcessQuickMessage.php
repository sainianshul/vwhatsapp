<?php

namespace App\Jobs;

use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessQuickMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $messageId;

    /**
     * Create a new job instance.
     */
    public function __construct($messageId)
    {
        $this->messageId = $messageId;
        $this->onQueue('quick');
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsappService): void
    {
        $messageRecord = WhatsAppMessage::with('whatsappAccount')->find($this->messageId);

        if (!$messageRecord || !in_array($messageRecord->status, ['pending', 'scheduled'])) {
            return;
        }

        try {
            $response = null;
            $maxRetries = 12; // 12 retries × 15 sec = 3 minutes max wait
            $retryDelay = 15; // seconds

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                if ($messageRecord->media_path) {
                    $mediaAbsolutePath = \Illuminate\Support\Facades\Storage::path($messageRecord->media_path);
                    if (file_exists($mediaAbsolutePath)) {
                        $response = $whatsappService->sendMediaMessage(
                            $messageRecord->whatsappAccount->session_id,
                            $messageRecord->receiver_number,
                            $mediaAbsolutePath,
                            $messageRecord->message_text,
                            basename($mediaAbsolutePath)
                        );
                    } else {
                        Log::warning("ProcessQuickMessage Error: Media file not found at {$mediaAbsolutePath}, sending text only.");
                        $response = $whatsappService->sendMessage(
                            $messageRecord->whatsappAccount->session_id,
                            $messageRecord->receiver_number,
                            $messageRecord->message_text
                        );
                    }
                } else {
                    $response = $whatsappService->sendMessage(
                        $messageRecord->whatsappAccount->session_id,
                        $messageRecord->receiver_number,
                        $messageRecord->message_text
                    );
                }

                // Check if the error is a temporary "booting up" or "not connected" error
                $isBootError = !$response['success'] && isset($response['error']) &&
                    (str_contains($response['error'], 'booting up') ||
                     str_contains($response['error'], 'not connected'));

                if (!$isBootError) {
                    break; // Either success or a real error — stop retrying
                }

                // Session is still booting — wait and retry
                Log::warning("QuickMessage {$this->messageId}: Session booting (attempt {$attempt}/{$maxRetries}), waiting {$retryDelay}s before retry.");
                sleep($retryDelay);
            }

            if ($response['success']) {
                $messageRecord->update(['status' => 'sent', 'error_message' => null]);
            } else {
                $messageRecord->update([
                    'status' => 'failed',
                    'error_message' => $response['error']
                ]);
            }
        } catch (\Exception $e) {
            Log::error("ProcessQuickMessage Error: " . $e->getMessage());
            $messageRecord->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
