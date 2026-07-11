<?php

namespace App\Http\Controllers;

use App\Jobs\BrainJob;
use App\Models\Bot;
use App\Models\Post;
use App\Models\ScheduledOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostEngagementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_ids'               => 'required|array|min:1',
            'post_ids.*'             => 'exists:posts,id',
            'engage_type'            => 'required|in:template,custom',
            'automation_template_id' => 'nullable|required_if:engage_type,template|exists:automation_templates,id',
            'custom_text'            => 'nullable|required_if:engage_type,custom|string|max:2000',
        ]);

        $postIds = $validated['post_ids'];
        
        if ($validated['engage_type'] === 'template') {
            // Bulk engage using AI Template
            // Group post IDs by social account ID because BrainJob processes per account
            $posts = Post::whereIn('id', $postIds)->get();
            $groupedByAccount = $posts->groupBy('social_account_id');

            foreach ($groupedByAccount as $accountId => $accountPosts) {
                // Dispatch BrainJob with the manual override template ID
                BrainJob::dispatch($accountId, $accountPosts->pluck('id')->toArray(), $validated['automation_template_id']);
            }

            return back()->with('success', count($postIds) . ' posts have been sent to the AI Engine for evaluation and scheduling.');
        } 
        
        // Manual Engage using Custom Text
        // Bypass Brain completely and schedule directly.
        $posts = Post::with('socialAccount')->whereIn('id', $postIds)->get();
        $scheduledCount = 0;
        
        foreach ($posts as $index => $post) {
            $account = $post->socialAccount;
            
            // Get any active bot for this platform
            $bot = Bot::where('platform', $account->platform)
                ->where('status', Bot::STATUS_ACTIVE)
                ->inRandomOrder()
                ->first();

            if (!$bot) {
                Log::warning("[Manual Engage] No bot found for platform {$account->platform}");
                continue;
            }

            // Schedule with a slight stagger (e.g., +2 minutes per post in the bulk array)
            $delayMins = ($index * 2) + rand(1, 3);
            
            ScheduledOperation::create([
                'social_account_id'      => $account->id,
                'post_id'                => $post->id,
                'automation_template_id' => null, // Manual
                'assigned_bot_id'        => $bot->id,
                'operation_type'         => ScheduledOperation::TYPE_COMMENT,
                'content_to_post'        => $validated['custom_text'],
                'scheduled_at'           => now()->addMinutes($delayMins),
                'status'                 => ScheduledOperation::STATUS_PENDING,
                'created_by_id'          => auth()->id(),
            ]);

            $scheduledCount++;
        }

        if ($scheduledCount === 0) {
            return back()->with('error', 'Could not queue any comments. Ensure you have an active Bot available for this platform.');
        }

        return back()->with('success', "{$scheduledCount} manual comments have been successfully queued in the Command Center.");
    }
}
