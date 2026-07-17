<?php

namespace App\Jobs;

use App\Models\BulkCampaign;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessBulkCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaign;
    public $timeout = 0; // Prevent timeout for long campaigns

    /**
     * Create a new job instance.
     */
    public function __construct(BulkCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsappService): void
    {
        $this->campaign->update(['status' => 'running']);

        // Log if this was a scheduled campaign
        if ($this->campaign->scheduled_at) {
            Log::info("Scheduled Campaign {$this->campaign->id} is now running (was scheduled for {$this->campaign->scheduled_at}).");
        }

        try {
            $filePath = Storage::path($this->campaign->csv_file_path);
            $file = fopen($filePath, 'r');
            
            // Read Headers
            $headers = fgetcsv($file);
            if (!$headers) {
                throw new \Exception("CSV file is empty or invalid.");
            }

            // Lowercase headers for consistent matching
            $headers = array_map('strtolower', array_map('trim', $headers));
            
            // Find Phone column index
            $phoneIndex = array_search('phone', $headers);
            if ($phoneIndex === false) {
                throw new \Exception("CSV file must contain a 'phone' column.");
            }

            // Resolve media path once (shared between all messages in this campaign)
            $mediaAbsolutePath = null;
            $mediaType = null;
            if ($this->campaign->media_path) {
                $mediaAbsolutePath = Storage::path($this->campaign->media_path);
                if (!file_exists($mediaAbsolutePath)) {
                    Log::warning("Campaign {$this->campaign->id}: Media file not found at {$mediaAbsolutePath}, sending text only.");
                    $mediaAbsolutePath = null;
                } else {
                    // Determine media type from file extension
                    $ext = strtolower(pathinfo($mediaAbsolutePath, PATHINFO_EXTENSION));
                    $typeMap = [
                        'jpg' => 'image', 'jpeg' => 'image', 'png' => 'image', 'gif' => 'image',
                        'mp4' => 'video',
                        'mp3' => 'audio', 'ogg' => 'audio',
                        'pdf' => 'document', 'doc' => 'document', 'docx' => 'document',
                        'xls' => 'document', 'xlsx' => 'document', 'zip' => 'document',
                    ];
                    $mediaType = $typeMap[$ext] ?? 'document';
                }
            }

            while (($row = fgetcsv($file)) !== false) {
                // Check if campaign was paused, failed, or soft-deleted
                $this->campaign->refresh();
                if ($this->campaign->trashed() || $this->campaign->status !== 'running') {
                    break;
                }

                $phone = $row[$phoneIndex] ?? null;
                if (!$phone) {
                    continue; // Skip empty rows
                }

                // Process Variables in Message Template
                $messageText = $this->campaign->message_template;
                $rowVariables = [];
                foreach ($headers as $index => $header) {
                    $val = $row[$index] ?? '';
                    // Replace {{header}} with actual value
                    $messageText = str_ireplace('{{' . $header . '}}', $val, $messageText);
                    $rowVariables[$header] = $val;
                }

                // Warn if unreplaced variables remain (CSV was missing that column)
                if (preg_match('/\{\{\w+\}\}/', $messageText)) {
                    Log::warning("Campaign {$this->campaign->id}: Message for {$phone} has unreplaced variables: {$messageText}");
                }

                // Sanitize phone number (strip spaces, dashes, plus signs)
                $phone = preg_replace('/[^0-9]/', '', $phone);

                // Create Message Record
                $messageRecord = WhatsAppMessage::create([
                    'user_id' => $this->campaign->user_id,
                    'whatsapp_account_id' => $this->campaign->whatsapp_account_id,
                    'receiver_number' => $phone,
                    'message_text' => $messageText,
                    'media_path' => $this->campaign->media_path,
                    'media_type' => $mediaType,
                    'status' => 'pending',
                    'bulk_campaign_id' => $this->campaign->id,
                    'is_bulk' => true,
                    'variables' => $rowVariables
                ]);

                // Send Message via Service (media or text)
                $sessionId = $this->campaign->whatsappAccount->session_id;
                if ($mediaAbsolutePath) {
                    $response = $whatsappService->sendMediaMessage(
                        $sessionId,
                        $phone,
                        $mediaAbsolutePath,
                        $messageText,
                        $this->campaign->media_filename ?? basename($mediaAbsolutePath)
                    );
                } else {
                    $response = $whatsappService->sendMessage(
                        $sessionId,
                        $phone,
                        $messageText
                    );
                }

                if ($response['success']) {
                    $messageRecord->update(['status' => 'sent']);
                    $this->campaign->increment('sent_count');
                } else {
                    $messageRecord->update([
                        'status' => 'failed',
                        'error_message' => $response['error']
                    ]);
                    $this->campaign->increment('failed_count');
                }

                // Sleep to avoid Ban (Anti-Ban feature)
                $delay = rand($this->campaign->delay_min, $this->campaign->delay_max);
                sleep($delay);
            }
            
            fclose($file);

            if ($this->campaign->status === 'running') {
                $this->campaign->update(['status' => 'completed']);
            }

        } catch (\Exception $e) {
            Log::error("Bulk Campaign Error (ID: {$this->campaign->id}): " . $e->getMessage());
            $this->campaign->update(['status' => 'failed']);
        }
    }
}
