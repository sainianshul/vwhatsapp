<?php

namespace App\Jobs;

use App\Models\BulkCampaign;
use App\Models\WhatsAppMessage;
use App\Models\MediaAsset;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        $this->onQueue('bulk');
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

            // Resolve Single Media path once (if media_type == single)
            $singleMediaAbsolutePath = null;
            $singleMediaType = null;
            if ($this->campaign->media_path && is_null($this->campaign->media_group_id)) {
                $singleMediaAbsolutePath = Storage::path($this->campaign->media_path);
                if (!file_exists($singleMediaAbsolutePath)) {
                    Log::warning("Campaign {$this->campaign->id}: Media file not found at {$singleMediaAbsolutePath}, sending text only.");
                    $singleMediaAbsolutePath = null;
                } else {
                    // Determine media type from file extension
                    $ext = strtolower(pathinfo($singleMediaAbsolutePath, PATHINFO_EXTENSION));
                    $typeMap = [
                        'jpg' => 'image', 'jpeg' => 'image', 'png' => 'image', 'gif' => 'image',
                        'mp4' => 'video',
                        'mp3' => 'audio', 'ogg' => 'audio',
                        'pdf' => 'document', 'doc' => 'document', 'docx' => 'document',
                        'xls' => 'document', 'xlsx' => 'document', 'zip' => 'document',
                    ];
                    $singleMediaType = $typeMap[$ext] ?? 'document';
                }
            }
            
            // Check if Dynamic Media Group is configured
            $hasDynamicMedia = !is_null($this->campaign->media_group_id);
            $mediaCodeIndex = array_search('media_code', $headers);
            
            if ($hasDynamicMedia && $mediaCodeIndex === false) {
                throw new \Exception("Campaign uses Dynamic Media, but CSV is missing 'media_code' column.");
            }

            while (($row = fgetcsv($file)) !== false) {
                // Reconnect DB in case MySQL dropped idle connection during sleep
                DB::reconnect();

                // Check if campaign was paused, failed, or soft-deleted
                $this->campaign->refresh();
                if ($this->campaign->trashed() || $this->campaign->status !== 'running') {
                    break;
                }

                $phone = $row[$phoneIndex] ?? null;
                if (!$phone) {
                    continue; // Skip empty rows
                }

                // Sanitize phone number (strip spaces, dashes, plus signs)
                $phone = preg_replace('/[^0-9]/', '', $phone);

                // Check if this contact has already been processed for this campaign
                $alreadyProcessed = WhatsAppMessage::where('bulk_campaign_id', $this->campaign->id)
                                                    ->where('receiver_number', $phone)
                                                    ->exists();
                if ($alreadyProcessed) {
                    continue; // Skip, already processed in previous run
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

                // Resolve dynamic media for this row (if any)
                $currentRowMediaAbsolutePath = $singleMediaAbsolutePath;
                $currentRowMediaType = $singleMediaType;
                $currentRowMediaFilename = $this->campaign->media_filename;
                $currentRowMediaPath = $this->campaign->media_path;

                if ($hasDynamicMedia && $mediaCodeIndex !== false) {
                    $mediaCode = trim($row[$mediaCodeIndex] ?? '');
                    if (!empty($mediaCode)) {
                        $asset = MediaAsset::where('media_group_id', $this->campaign->media_group_id)
                                           ->where('asset_code', $mediaCode)
                                           ->where('status', 'active')
                                           ->first();
                        
                        if ($asset) {
                            $currentRowMediaPath = $asset->file_path;
                            $currentRowMediaAbsolutePath = Storage::disk('public')->path($asset->file_path);
                            $currentRowMediaFilename = $asset->file_name;
                            
                            $ext = strtolower(pathinfo($currentRowMediaAbsolutePath, PATHINFO_EXTENSION));
                            $typeMap = [
                                'jpg' => 'image', 'jpeg' => 'image', 'png' => 'image', 'gif' => 'image',
                                'mp4' => 'video',
                                'mp3' => 'audio', 'ogg' => 'audio',
                                'pdf' => 'document', 'doc' => 'document', 'docx' => 'document',
                                'xls' => 'document', 'xlsx' => 'document', 'zip' => 'document',
                            ];
                            $currentRowMediaType = $typeMap[$ext] ?? 'document';

                            if (!file_exists($currentRowMediaAbsolutePath)) {
                                Log::warning("Campaign {$this->campaign->id}: Dynamic Media file not found for code {$mediaCode}, sending text only.");
                                $currentRowMediaAbsolutePath = null;
                            }
                        } else {
                            Log::warning("Campaign {$this->campaign->id}: Dynamic Media code '{$mediaCode}' not found or inactive.");
                        }
                    }
                }

                // Create Message Record
                $messageRecord = WhatsAppMessage::create([
                    'user_id' => $this->campaign->user_id,
                    'whatsapp_account_id' => $this->campaign->whatsapp_account_id,
                    'receiver_number' => $phone,
                    'message_text' => $messageText,
                    'media_path' => $currentRowMediaPath,
                    'media_type' => $currentRowMediaType,
                    'status' => 'pending',
                    'bulk_campaign_id' => $this->campaign->id,
                    'is_bulk' => true,
                    'variables' => $rowVariables
                ]);

                // Send Message via Service (media or text) — with Auto-Retry for Boot Errors
                $sessionId = $this->campaign->whatsappAccount->session_id;
                $response = null;
                $maxRetries = 12; // 12 retries × 15 sec = 3 minutes max wait
                $retryDelay = 15; // seconds

                for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                    if ($currentRowMediaAbsolutePath) {
                        $response = $whatsappService->sendMediaMessage(
                            $sessionId,
                            $phone,
                            $currentRowMediaAbsolutePath,
                            $messageText,
                            $currentRowMediaFilename ?? basename($currentRowMediaAbsolutePath)
                        );
                    } else {
                        $response = $whatsappService->sendMessage(
                            $sessionId,
                            $phone,
                            $messageText
                        );
                    }

                    // If success or a permanent error (invalid number, file too large, etc.) — stop retrying
                    if ($response['success']) {
                        break;
                    }

                    $errorMsg = $response['error'] ?? '';

                    // Session permanently dead — stop entire campaign, no point retrying
                    if (str_contains($errorMsg, 'failed to connect') || str_contains($errorMsg, 'reconnect the account')) {
                        Log::error("Campaign {$this->campaign->id}: Session is permanently dead. Stopping campaign.");
                        $messageRecord->update(['status' => 'failed', 'error_message' => $errorMsg]);
                        $this->campaign->increment('failed_count');
                        $this->campaign->update(['status' => 'failed']);
                        fclose($file);
                        return; // Exit the entire job
                    }

                    // Temporary boot error — retry after delay
                    $isBootError = str_contains($errorMsg, 'booting up') || str_contains($errorMsg, 'not connected');

                    if (!$isBootError) {
                        break; // Real error (e.g., invalid number) — don't retry
                    }

                    // Session is still booting — wait and retry
                    Log::warning("Campaign {$this->campaign->id}: Session booting (attempt {$attempt}/{$maxRetries}), waiting {$retryDelay}s before retry for {$phone}.");
                    sleep($retryDelay);

                    // Re-check if campaign was cancelled during the wait
                    DB::reconnect();
                    $this->campaign->refresh();
                    if ($this->campaign->trashed() || $this->campaign->status !== 'running') {
                        break 2; // Exit both the retry loop and the main CSV loop
                    }
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
