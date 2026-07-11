<?php

namespace App\Jobs;

use App\Models\Bot;
use App\Services\SocialSearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckSingleBotHealthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $botId;
    public $timeout = 180;

    /**
     * Create a new job instance.
     */
    public function __construct(int $botId)
    {
        $this->botId = $botId;
    }

    /**
     * Execute the job.
     */
    public function handle(SocialSearchService $searchService): void
    {
        $bot = Bot::find($this->botId);
        
        if (!$bot || !$bot->cookie) {
            return;
        }

        Log::info("[HealthCheck] Checking health for Bot ID: {$bot->id}");

        $result = $searchService->checkBotHealth($bot->platform, $bot->cookie);

        if (!$result['success']) {
            Log::warning("[HealthCheck] Failed to check health for Bot ID: {$bot->id} - " . ($result['error'] ?? 'Unknown error'));
            return;
        }

        $statusMap = [
            'active' => Bot::PLATFORM_STATUS_ACTIVE,
            'expired' => Bot::PLATFORM_STATUS_EXPIRED,
            'restricted' => Bot::PLATFORM_STATUS_RESTRICTED,
        ];

        $platformStatus = $statusMap[$result['status']] ?? Bot::PLATFORM_STATUS_UNKNOWN;

        $bot->update([
            'platform_status' => $platformStatus,
            'platform_status_note' => $result['message'] ?? 'Checked via Auto Health Scheduler',
            'platform_status_checked_at' => now(),
        ]);
        
        Log::info("[HealthCheck] Updated health for Bot ID: {$bot->id} -> {$platformStatus}");
    }
}
