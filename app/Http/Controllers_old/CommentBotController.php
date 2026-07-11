<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\CommentBank;
use App\Models\FbBotAccount;
use App\Models\CommentTask;
use App\Models\ScrapedPost;

class CommentBotController extends Controller
{
    /**
     * Comment Bank — manage good/bad comment templates
     */
    public function commentBank()
    {
        $comments = CommentBank::latest()->get();
        $goodCount = $comments->where('type', 'good')->count();
        $badCount = $comments->where('type', 'bad')->count();
        return view('comments.bank', compact('comments', 'goodCount', 'badCount'));
    }

    /**
     * Store a new comment template
     */
    public function storeComment(Request $request)
    {
        $request->validate([
            'comment_text' => 'required|string|min:2|max:2000',
            'type' => 'required|in:good,bad',
            'category' => 'nullable|string|max:100',
        ]);

        CommentBank::create([
            'comment_text' => $request->input('comment_text'),
            'type' => $request->input('type'),
            'category' => $request->input('category'),
        ]);

        return response()->json(['success' => true, 'message' => 'Comment added!']);
    }

    /**
     * Delete a comment template
     */
    public function deleteComment($id)
    {
        CommentBank::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Bot Accounts — manage Facebook login credentials
     */
    public function botAccounts()
    {
        $accounts = FbBotAccount::latest()->get();
        return view('comments.accounts', compact('accounts'));
    }

    /**
     * Store a new bot account
     */
    public function storeBotAccount(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'fb_email' => 'required|string|max:255',
            'fb_cookies' => 'required|string',
        ]);

        FbBotAccount::create([
            'account_name' => $request->input('account_name'),
            'fb_email' => $request->input('fb_email'),
            'fb_cookies' => $request->input('fb_cookies'),
        ]);

        return response()->json(['success' => true, 'message' => 'Bot account added!']);
    }

    /**
     * Delete a bot account
     */
    public function deleteBotAccount($id)
    {
        FbBotAccount::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Execute auto-comment on selected posts
     */
    public function executeComment(Request $request)
    {
        $request->validate([
            'post_ids' => 'required|array|min:1',
            'post_ids.*' => 'exists:scraped_posts,id',
            'type' => 'required|in:good,bad',
        ]);

        $type = $request->input('type');
        $postIds = $request->input('post_ids');

        // Get available bot account
        $botAccount = FbBotAccount::where('status', 'active')
            ->orderBy('last_used_at', 'asc')
            ->first();

        if (!$botAccount) {
            return response()->json([
                'success' => false,
                'error' => 'No active bot account found. Please add a Facebook account first.'
            ], 400);
        }

        // Get available comments of the requested type
        $availableComments = CommentBank::where('type', $type)
            ->where('is_active', true)
            ->get();

        if ($availableComments->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => "No {$type} comments found in the bank. Please add some first."
            ], 400);
        }

        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($postIds as $postId) {
            $post = ScrapedPost::find($postId);
            if (!$post || !$post->post_url) {
                $failCount++;
                continue;
            }

            // Pick a random comment from the bank
            $commentTemplate = $availableComments->random();

            // Create task record
            $task = CommentTask::create([
                'scraped_post_id' => $post->id,
                'comment_bank_id' => $commentTemplate->id,
                'fb_bot_account_id' => $botAccount->id,
                'type' => $type,
                'status' => 'processing',
            ]);

            try {
                set_time_limit(300);
                ini_set('max_execution_time', 300);

                // Call scraper microservice to post comment
                $response = Http::timeout(240)
                    ->connectTimeout(30)
                    ->post('http://scraper:3000/api/post-comment', [
                        'post_url' => $post->post_url,
                        'comment_text' => $commentTemplate->comment_text,
                        'fb_cookies' => $botAccount->fb_cookies_decrypted,
                    ]);

                if ($response->successful() && $response->json('success')) {
                    $task->update([
                        'status' => 'posted',
                        'executed_at' => now(),
                    ]);

                    $botAccount->increment('total_comments_posted');
                    $botAccount->update(['last_used_at' => now()]);

                    $successCount++;
                    $results[] = [
                        'post_id' => $post->id,
                        'status' => 'posted',
                        'comment' => $commentTemplate->comment_text,
                    ];
                } else {
                    $errorMsg = $response->json('error') ?? 'Unknown scraper error';
                    $task->update([
                        'status' => 'failed',
                        'error_message' => $errorMsg,
                        'executed_at' => now(),
                    ]);
                    $failCount++;
                    $results[] = [
                        'post_id' => $post->id,
                        'status' => 'failed',
                        'error' => $errorMsg,
                    ];
                }
            } catch (\Exception $e) {
                $task->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'executed_at' => now(),
                ]);
                $failCount++;
                $results[] = [
                    'post_id' => $post->id,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
            }

            // Anti-detection delay between comments (30-60 seconds)
            if (count($postIds) > 1) {
                sleep(rand(30, 60));
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Commenting complete! {$successCount} posted, {$failCount} failed.",
            'results' => $results,
            'success_count' => $successCount,
            'fail_count' => $failCount,
        ]);
    }

    /**
     * Comment History — show all past comment tasks
     */
    public function commentHistory()
    {
        $tasks = CommentTask::with(['post.socialAccount', 'comment', 'botAccount'])
            ->latest()
            ->paginate(50);
        return view('comments.history', compact('tasks'));
    }

    /**
     * Post a single manual comment on a specific post URL
     */
    public function postSingleComment(Request $request)
    {
        $request->validate([
            'post_url' => 'required|string',
            'comment_text' => 'required|string|min:1|max:2000',
            'target_name' => 'nullable|string|max:255',
        ]);

        $botAccount = FbBotAccount::where('status', 'active')->first();
        if (!$botAccount) {
            return response()->json(['success' => false, 'error' => 'No active bot account found. Please add one first.'], 400);
        }

        $postUrl = $request->input('post_url');
        $commentText = $request->input('comment_text');
        $targetName = $request->input('target_name', '');

        // Create task record
        $task = CommentTask::create([
            'scraped_post_id' => null,
            'post_url' => $postUrl,
            'comment_bank_id' => null,
            'fb_bot_account_id' => $botAccount->id,
            'type' => 'good',
            'comment_text_used' => $commentText,
            'target_name' => $targetName,
            'status' => 'processing',
        ]);

        try {
            $response = Http::timeout(240)
                ->connectTimeout(30)
                ->post('http://scraper:3000/api/post-comment', [
                    'post_url' => $postUrl,
                    'comment_text' => $commentText,
                    'fb_cookies' => $botAccount->fb_cookies_decrypted,
                ]);

            if ($response->successful() && $response->json('success')) {
                $task->update([
                    'status' => 'posted',
                    'executed_at' => now(),
                ]);
                $botAccount->increment('total_comments_posted');
                $botAccount->update(['last_used_at' => now()]);

                return response()->json(['success' => true, 'message' => 'Comment posted successfully! ✅']);
            } else {
                $errorMsg = $response->json('error') ?? 'Unknown scraper error';
                $task->update([
                    'status' => 'failed',
                    'error_message' => $errorMsg,
                    'executed_at' => now(),
                ]);
                return response()->json(['success' => false, 'error' => $errorMsg]);
            }
        } catch (\Exception $e) {
            $task->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'executed_at' => now(),
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Like a single post
     */
    public function likeSinglePost(Request $request)
    {
        $request->validate([
            'post_url' => 'required|string',
        ]);

        $botAccount = FbBotAccount::where('status', 'active')->first();
        if (!$botAccount) {
            return response()->json(['success' => false, 'error' => 'No active bot account found.'], 400);
        }

        $postUrl = $request->input('post_url');

        try {
            $response = Http::timeout(240)
                ->connectTimeout(30)
                ->post('http://scraper:3000/api/like-post', [
                    'post_url' => $postUrl,
                    'fb_cookies' => $botAccount->fb_cookies_decrypted,
                ]);

            if ($response->successful() && $response->json('success')) {
                return response()->json(['success' => true, 'message' => 'Post liked successfully! 👍']);
            } else {
                $errorMsg = $response->json('error') ?? 'Unknown scraper error';
                return response()->json(['success' => false, 'error' => $errorMsg]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
