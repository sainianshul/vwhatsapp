<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\TargetProfile;
use App\Models\SocialAccount;

class TargetUserController extends Controller
{
    /**
     * Index - List all saved target users
     */
    public function index()
    {
        $targets = TargetProfile::with('socialAccounts')->latest()->get();
        return view('targets.index', compact('targets'));
    }

    /**
     * Show the search page
     */
    public function searchPage()
    {
        return view('targets.search');
    }

    /**
     * AJAX: Perform search via Scraper microservice (returns JSON)
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|min:2|max:100',
        ]);

        $query = $request->input('query');

        try {
            set_time_limit(300);
            ini_set('max_execution_time', 300);

            $response = Http::timeout(240)
                ->connectTimeout(30)
                ->post('http://scraper:3000/api/search', [
                    'query' => $query,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'query' => $query,
                    'results' => $data['results'] ?? [],
                ]);
            }

            $errorData = $response->json();
            return response()->json([
                'success' => false,
                'error' => $errorData['error'] ?? 'Scraper error.',
            ], 500);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Could not connect to Scraper. Make sure Docker is running.',
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX: Add a target user from search results
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create the target profile
        $profile = TargetProfile::create([
            'name' => $request->input('name'),
            'designation' => $request->input('designation'),
            'party' => $request->input('party'),
            'city' => $request->input('city'),
            'photo_url' => $request->input('photo_url'),
            'notes' => $request->input('notes'),
            'status' => 'active',
        ]);

        // Save all associated social accounts (pages found from search)
        $accounts = $request->input('accounts', []);
        foreach ($accounts as $account) {
            SocialAccount::create([
                'target_profile_id' => $profile->id,
                'platform' => $account['platform'] ?? 'facebook',
                'account_url' => $account['url'] ?? '',
                'account_name' => $account['name'] ?? $request->input('name'),
                'account_username' => $account['username'] ?? null,
                'followers_count' => $account['followers'] ?? null,
                'account_type' => $account['accountType'] ?? null,
                'profile_pic_url' => $account['profilePic'] ?? null,
                'description' => $account['description'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Target user added successfully!',
            'profile_id' => $profile->id,
        ]);
    }

    /**
     * Show profile of a target user
     */
    public function show($id)
    {
        $target = TargetProfile::with(['socialAccounts.posts.comments'])->findOrFail($id);
        return view('targets.show', compact('target'));
    }

    /**
     * Deep Scrape a specific social account
     */
    public function deepScrape(Request $request, $id, $accountId)
    {
        $target = TargetProfile::findOrFail($id);
        $account = SocialAccount::where('target_profile_id', $id)->findOrFail($accountId);

        try {
            set_time_limit(300);
            ini_set('max_execution_time', 300);

            // Get cookies from a bot account for authenticated scraping
            $botAccount = \App\Models\FbBotAccount::where('status', 'active')->first();
            $cookies = $botAccount ? $botAccount->fb_cookies_decrypted : null;

            $response = Http::timeout(240)
                ->connectTimeout(30)
                ->post('http://scraper:3000/api/deep-scrape', [
                    'url' => $account->account_url,
                    'fb_cookies' => $cookies,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['data']['posts']) && is_array($data['data']['posts'])) {
                    foreach ($data['data']['posts'] as $postData) {
                        // Create or update post
                        $post = \App\Models\ScrapedPost::updateOrCreate(
                            [
                                'social_account_id' => $account->id,
                                'fb_post_id' => $postData['fb_post_id'] ?? null,
                            ],
                            [
                                'post_url' => $postData['post_url'] ?? '',
                                'post_type' => $postData['post_type'] ?? 'text',
                                'content' => $postData['content'] ?? null,
                                'media_url' => $postData['media_url'] ?? null,
                                'posted_at' => isset($postData['posted_at']) ? \Carbon\Carbon::parse($postData['posted_at']) : now(),
                                'reactions_count' => $postData['reactions_count'] ?? 0,
                                'comments_count' => $postData['comments_count'] ?? 0,
                                'shares_count' => $postData['shares_count'] ?? 0,
                                'scraped_at' => now(),
                            ]
                        );

                        // Save comments for this post
                        if (isset($postData['comments']) && is_array($postData['comments'])) {
                            foreach ($postData['comments'] as $commentData) {
                                \App\Models\ScrapedComment::create([
                                    'scraped_post_id' => $post->id,
                                    'commenter_name' => $commentData['commenter_name'] ?? 'Unknown',
                                    'comment_text' => $commentData['comment_text'] ?? '',
                                    'reactions_count' => $commentData['reactions_count'] ?? 0,
                                    'commented_at' => now(),
                                    'scraped_at' => now(),
                                ]);
                            }
                        }
                    }
                }

                $account->update(['last_synced_at' => now()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Deep scrape completed successfully!'
                ]);
            }

            $errorData = $response->json();
            return response()->json([
                'success' => false,
                'error' => 'Scraper error: ' . ($errorData['error'] ?? 'Unknown')
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error during deep scrape: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a target user
     */
    public function destroy($id)
    {
        $target = TargetProfile::findOrFail($id);
        $target->delete();
        return redirect()->route('target.index')->with('success', 'Target user deleted.');
    }
}
