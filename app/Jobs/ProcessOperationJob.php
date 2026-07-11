<?php

namespace App\Jobs;

use App\Models\ScheduledOperation;
use App\Services\SocialSearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOperationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $operationId;
    public $timeout = 180; // Allow 3 minutes for this job to complete since node scraping can take time

    /**
     * Create a new job instance.
     */
    public function __construct(int $operationId)
    {
        $this->operationId = $operationId;
    }

    /**
     * Execute the job.
     */
    public function handle(SocialSearchService $searchService): void
    {
        $op = ScheduledOperation::with(['assignedBot', 'post.socialAccount'])->find($this->operationId);

        if (!$op || $op->status !== ScheduledOperation::STATUS_PROCESSING) {
            // Already processed, cancelled, or deleted
            return;
        }

        Log::info("[ProcessOperation] Processing Operation ID: {$op->id} for Post ID: {$op->post_id}");

        try {
            if (!$op->assignedBot || !$op->assignedBot->cookie) {
                throw new \Exception("Assigned bot has no valid cookies.");
            }

            // Determine the correct post URL (from platform_specific_data or fallback)
            $postUrl = $op->post->platform_specific_data['post_url'] ?? $op->post->socialAccount->account_url;

            // Call the actual Node JS Bot Script via the Service
            $result = $searchService->postComment($postUrl, $op->content_to_post, $op->assignedBot->cookie);
            
            $success = $result['success'] ?? false;
            $errorMsg = $result['error'] ?? null;

            if ($success) {
                $op->update([
                    'status' => ScheduledOperation::STATUS_COMPLETED,
                    'completed_at' => now(),
                ]);
                
                // Increment bot's action count
                $op->assignedBot->increment('total_actions_count');
                $op->assignedBot->update(['last_action_at' => now()]);
                
                Log::info("[ProcessOperation] Successfully completed Operation ID: {$op->id}");
            } else {
                $op->update([
                    'status' => ScheduledOperation::STATUS_FAILED,
                    'error_log' => $errorMsg ?? 'Unknown bot error',
                ]);
                Log::error("[ProcessOperation] Failed Operation ID: {$op->id} - Error: {$errorMsg}");
                
                // Auto-update bot health if expired or restricted
                if ($errorMsg && (stripos($errorMsg, 'expired') !== false || stripos($errorMsg, 'invalid') !== false)) {
                    $op->assignedBot->update(['platform_status' => \App\Models\Bot::PLATFORM_STATUS_EXPIRED, 'platform_status_note' => 'Auto-detected during comment execution.', 'platform_status_checked_at' => now()]);
                } elseif ($errorMsg && (stripos($errorMsg, 'restricted') !== false || stripos($errorMsg, 'checkpoint') !== false)) {
                    $op->assignedBot->update(['platform_status' => \App\Models\Bot::PLATFORM_STATUS_RESTRICTED, 'platform_status_note' => 'Auto-detected during comment execution.', 'platform_status_checked_at' => now()]);
                }
            }
        } catch (\Exception $e) {
            $op->update([
                'status' => ScheduledOperation::STATUS_FAILED,
                'error_log' => $e->getMessage(),
            ]);
            Log::error("[ProcessOperation] Exception on Operation ID: {$op->id} - " . $e->getMessage());
        }
    }
}
