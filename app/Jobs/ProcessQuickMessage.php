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
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsappService): void
    {
        $messageRecord = WhatsAppMessage::with('whatsappAccount')->find($this->messageId);

        if (!$messageRecord || $messageRecord->status !== 'pending') {
            return;
        }

        try {
            $response = $whatsappService->sendMessage(
                $messageRecord->whatsappAccount->session_id,
                $messageRecord->receiver_number,
                $messageRecord->message_text
            );

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
