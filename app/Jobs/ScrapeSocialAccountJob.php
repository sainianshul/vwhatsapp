<?php

namespace App\Jobs;

use App\Contracts\SocialSearchInterface;
use App\Models\Bot;
use App\Models\Post;
use App\Models\ScrapeLog;
use App\Models\SocialAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScrapeSocialAccountJob implements ShouldQueue
{
    use Queueable;

    protected $account;

    /**
     * Create a new job instance.
     */
    public function __construct(SocialAccount $account)
    {
        $this->account = $account;
    }

    /**
     * Execute the job.
     */
    public function handle(SocialSearchInterface $searchService): void
    {
        \Illuminate\Support\Facades\Log::info("[Scraper] Starting scrape job for Account ID: {$this->account->id} ({$this->account->platform})");

        // Mark as scraping (in case not already marked by controller)
        $this->account->update(['scrape_status' => 'scraping']);

        // Fetch an active bot cookie
        $bot = Bot::where('platform', $this->account->platform)
            ->where('status', 'active')
            ->whereNotNull('cookie')
            ->inRandomOrder()
            ->first();

        if (!$bot) {
            $this->markFailed('No active bots with cookies found for ' . $this->account->platform);
            return;
        }

        $results = $searchService->scrapeAccount($this->account->account_url, $bot->cookie);

        \Illuminate\Support\Facades\Log::info("[Scraper] Response received", [
            'success' => $results['success'] ?? false,
            'posts_count' => count($results['posts'] ?? []),
            'stats' => $results['stats'] ?? 'N/A',
            'error' => $results['error'] ?? null,
        ]);

        if (!$results || !isset($results['success']) || !$results['success']) {
            $errorMsg = $results['error'] ?? 'Scraper returned failure';
            
            // Auto-update bot health if expired or restricted
            if ($errorMsg && (stripos($errorMsg, 'expired') !== false || stripos($errorMsg, 'invalid') !== false)) {
                $bot->update(['platform_status' => \App\Models\Bot::PLATFORM_STATUS_EXPIRED, 'platform_status_note' => 'Auto-detected during scraping.', 'platform_status_checked_at' => now()]);
            } elseif ($errorMsg && (stripos($errorMsg, 'restricted') !== false || stripos($errorMsg, 'checkpoint') !== false)) {
                $bot->update(['platform_status' => \App\Models\Bot::PLATFORM_STATUS_RESTRICTED, 'platform_status_note' => 'Auto-detected during scraping.', 'platform_status_checked_at' => now()]);
            }

            $this->markFailed($errorMsg);
            return;
        }

        // Process successful scrape
        $postsData = $results['posts'] ?? [];
        $newPostsCount = 0;
        $newPostIds = [];

        \Illuminate\Support\Facades\Log::info("[Scraper] Processing " . count($postsData) . " posts from scraper response");

        foreach ($postsData as $postItem) {
            // Scraper returns: post_id, message, created_time, post_url,
            //   post_type, media_url, author_name, reactions_count,
            //   comments_count, shares_count
            $platformPostId = $postItem['post_id'] ?? null;

            if (empty($platformPostId)) {
                \Illuminate\Support\Facades\Log::warning("[Scraper] Skipping post with no post_id", ['post' => array_keys($postItem)]);
                continue;
            }

            $postType = $postItem['post_type'] ?? 'text';
            $mediaUrl = $postItem['media_url'] ?? null;

            // firstOrCreate to prevent duplicates based on platform_post_id
            $post = Post::firstOrCreate(
                [
                    'social_account_id' => $this->account->id,
                    'platform_post_id' => $platformPostId,
                ],
                [
                    'created_by_id' => $this->account->created_by_id,
                    'post_type' => $postType,
                    'content' => $postItem['message'] ?? '',
                    'media_url' => $mediaUrl,
                    'likes_count' => $postItem['reactions_count'] ?? 0,
                    'comments_count' => $postItem['comments_count'] ?? 0,
                    'shares_count' => $postItem['shares_count'] ?? 0,
                    'posted_at' => !empty($postItem['created_time']) ? \Carbon\Carbon::parse($postItem['created_time']) : now(),
                    'platform_specific_data' => $postItem,
                    'status' => 'active',
                ]
            );

            if ($post->wasRecentlyCreated) {
                $newPostsCount++;
                $newPostIds[] = $post->id;
            }
        }

        // Trigger the Auto-Engage Brain if we have new posts
        if (!empty($newPostIds)) {
            \App\Jobs\BrainJob::dispatch($this->account->id, $newPostIds);
        }

        // Update Account
        $this->account->update([
            'scrape_status' => 'idle',
            'last_scraped_at' => now()
        ]);

        // Log Scrape
        ScrapeLog::create([
            'social_account_id' => $this->account->id,
            'status' => 'success',
            'message' => "Scraped {$newPostsCount} new posts (Total from API: " . count($postsData) . ").",
            'posts_found' => count($postsData),
            'raw_response' => $results['stats'] ?? null,
        ]);

        // Notify user
        if ($this->account->creator) {
            $this->account->creator->notify(new \App\Notifications\ScrapeCompletedNotification($this->account, $newPostsCount, 'success'));
        }
    }

    protected function markFailed(string $errorMsg)
    {
        $this->account->update([
            'scrape_status' => 'failed',
            'last_scraped_at' => now()
        ]);

        ScrapeLog::create([
            'social_account_id' => $this->account->id,
            'status' => 'error',
            'message' => $errorMsg,
        ]);

        if ($this->account->creator) {
            $this->account->creator->notify(new \App\Notifications\ScrapeCompletedNotification($this->account, 0, 'error'));
        }
    }
}
