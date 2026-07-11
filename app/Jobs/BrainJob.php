<?php

namespace App\Jobs;

use App\Models\AutomationRule;
use App\Models\Bot;
use App\Models\Post;
use App\Models\ScheduledOperation;
use App\Models\SocialAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BrainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $socialAccountId;
    protected $postIds;
    protected $manualTemplateId;

    /**
     * Create a new job instance.
     */
    public function __construct($socialAccountId, array $postIds, $manualTemplateId = null)
    {
        $this->socialAccountId = $socialAccountId;
        $this->postIds = $postIds;
        $this->manualTemplateId = $manualTemplateId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $account = SocialAccount::with(['automationRule.template'])->find($this->socialAccountId);
        
        if (!$account) return;

        $template = null;
        $creatorId = null;

        if ($this->manualTemplateId) {
            // Manual Override Mode
            $template = \App\Models\AutomationTemplate::find($this->manualTemplateId);
            $creatorId = $template->created_by_id ?? $account->created_by_id;
            Log::info("[Brain] Manual bulk engage triggered for Account ID: {$this->socialAccountId} using Template: {$template->name}");
        } else {
            // Auto-Engage Mode
            if (!$account->automationRule || !$account->automationRule->is_active) {
                Log::info("[Brain] Auto-Engage is OFF or missing for Account ID: {$this->socialAccountId}. Skipping.");
                return;
            }
            $template = $account->automationRule->template;
            $creatorId = $account->automationRule->created_by_id;
        }

        if (!$template) return;

        $posts = Post::whereIn('id', $this->postIds)->get();

        Log::info("[Brain] Evaluating {$posts->count()} posts for Account ID: {$account->id} using Template: {$template->name}");

        // Get any available bot for this platform
        $availableBots = Bot::where('platform', $account->platform)
            ->where('status', Bot::STATUS_ACTIVE)
            ->get();

        if ($availableBots->isEmpty()) {
            Log::warning("[Brain] No active bots found for platform {$account->platform} (User ID: {$rule->created_by_id}). Cannot schedule operations.");
            return;
        }

        $scheduledCount = 0;

        foreach ($posts as $post) {
            // 1. Check if an operation already exists for this post
            $existing = ScheduledOperation::where('post_id', $post->id)
                ->where('social_account_id', $account->id)
                ->exists();

            if ($existing) {
                continue;
            }

            // 2. Evaluate Likes
            if ($post->likes_count < $template->min_likes_required) {
                Log::debug("[Brain] Post {$post->id} failed Likes check (Has {$post->likes_count}, Needs {$template->min_likes_required})");
                continue;
            }

            // 3. Evaluate Keywords Include
            $contentLower = strtolower($post->post_content ?? '');
            if (!empty($template->keywords_include)) {
                $hasInclude = false;
                foreach ($template->keywords_include as $kw) {
                    if (str_contains($contentLower, strtolower($kw))) {
                        $hasInclude = true;
                        break;
                    }
                }
                if (!$hasInclude) {
                    Log::debug("[Brain] Post {$post->id} failed Keywords Include check.");
                    continue;
                }
            }

            // 4. Evaluate Keywords Exclude
            if (!empty($template->keywords_exclude)) {
                $hasExclude = false;
                foreach ($template->keywords_exclude as $kw) {
                    if (str_contains($contentLower, strtolower($kw))) {
                        $hasExclude = true;
                        break;
                    }
                }
                if ($hasExclude) {
                    Log::debug("[Brain] Post {$post->id} failed Keywords Exclude check.");
                    continue;
                }
            }

            // 5. Select Bot first (so we can use its persona)
            $assignedBot = $availableBots->random();

            // 6. Generate Content (now aware of the bot)
            $commentText = $this->generateComment($template, $post, $assignedBot);

            if (!$commentText) {
                Log::warning("[Brain] Failed to generate comment text for Post {$post->id}");
                continue;
            }

            // 7. Schedule Operation
            $delayMins = rand($template->min_delay_mins ?? 5, $template->max_delay_mins ?? 15);
            $scheduledAt = now()->addMinutes($delayMins);

            ScheduledOperation::create([
                'social_account_id'      => $account->id,
                'post_id'                => $post->id,
                'automation_template_id' => $template->id,
                'assigned_bot_id'        => $assignedBot->id,
                'operation_type'         => ScheduledOperation::TYPE_COMMENT,
                'content_to_post'        => $commentText,
                'scheduled_at'           => $scheduledAt,
                'status'                 => ScheduledOperation::STATUS_PENDING,
                'created_by_id'          => $creatorId,
            ]);

            $scheduledCount++;
            Log::info("[Brain] Scheduled operation for Post {$post->id} at {$scheduledAt} using Bot {$assignedBot->name}");
        }

        // Update the rule's sync tracking if not manual
        if (!$this->manualTemplateId && isset($account->automationRule)) {
            $account->automationRule->update([
                'last_sync_at' => now(),
                'next_sync_at' => now()->addHours($account->automationRule->sync_interval_hours ?? 6)
            ]);
        }

        Log::info("[Brain] Finished. Scheduled {$scheduledCount} operations.");
    }

    /**
     * Generate the comment text based on template settings.
     * Uses the LlmServiceInterface to interact with the configured LLM (e.g., Gemini, OpenAI).
     */
    private function generateComment($template, $post, ?\App\Models\Bot $bot = null): ?string
    {
        if ($template->engine_type === \App\Models\AutomationTemplate::ENGINE_BANK) {
            // Just return a static bank comment (in future, pick randomly from a table)
            return "This is a pre-approved comment from the bank.";
        }

        // Use the LLM Service
        $llmService = app(\App\Contracts\LlmServiceInterface::class);
        
        $tone = $template->ai_tone ?? 'neutral';
        $customPrompt = $template->ai_prompt ?? null;
        
        $postContent = $post->post_content ?? '';

        Log::info("[Brain] Generating comment for Post {$post->id} using tone: {$tone}");

        $generatedComment = $llmService->generateComment($postContent, $tone, $customPrompt, $bot);

        if (!$generatedComment) {
            Log::error("[Brain] LLM returned null or failed for Post {$post->id}");
            return null;
        }

        return $generatedComment;
    }
}
