<?php

namespace App\Services;

use App\Contracts\SocialSearchInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialSearchService implements SocialSearchInterface
{
    protected string $scraperBaseUrl;

    public function __construct()
    {
        // Expecting the Node.js scraper to be running at this URL.
        // E.g., http://social_scraper:3000 in Docker.
        $this->scraperBaseUrl = config('services.scraper.url', 'http://social_scraper:3000');
    }

    /**
     * Search for a social profile by query (e.g. name or url).
     *
     * @param  string  $query
     * @param  string|null  $cookies Optional FB cookies for deep searching
     * @return array
     */
    public function search(string $query, ?string $cookies = null): array
    {
        try {
            // The scraper only has /api/graphql-search.
            // When no cookies are provided, it auto-falls back to Bing/Google search.
            $payload = ['query' => $query];

            if ($cookies) {
                $payload['fb_cookies'] = $cookies;
            }

            $response = Http::timeout(120)->post("{$this->scraperBaseUrl}/api/graphql-search", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data['results'] ?? [];
            }

            Log::warning('SocialSearchService: Non-success response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('SocialSearchService: Search failed', [
                'query'   => $query,
                'error'   => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Deep scrape a specific social media account.
     */
    public function scrapeAccount(string $url, string $cookies): array
    {
        try {
            $response = Http::timeout(180)->post("{$this->scraperBaseUrl}/api/graphql-scrape", [
                'url' => $url,
                'fb_cookies' => $cookies,
                'max_scrolls' => 5
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Scraper graphql-scrape failed: ' . $response->body());
            return ['success' => false, 'error' => 'API returned error code'];
        } catch (\Exception $e) {
            Log::error('Scraper graphql-scrape exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Post a comment using the bot network.
     */
    public function postComment(string $postUrl, string $commentText, string $cookies): array
    {
        try {
            $response = Http::timeout(180)->post("{$this->scraperBaseUrl}/api/post-comment", [
                'post_url'     => $postUrl,
                'comment_text' => $commentText,
                'fb_cookies'   => $cookies
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Scraper post-comment failed: ' . $response->body());
            return ['success' => false, 'error' => 'API returned error code: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('Scraper post-comment exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check the health of a bot's cookies.
     */
    public function checkBotHealth(string $platform, string $cookies): array
    {
        try {
            $response = Http::timeout(60)->post("{$this->scraperBaseUrl}/api/health-check", [
                'platform'   => $platform,
                'fb_cookies' => $cookies
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'error' => 'API returned error code: ' . $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
