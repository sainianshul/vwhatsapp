<?php

namespace App\Http\Controllers;

use App\Contracts\SocialSearchInterface;
use App\Models\SocialAccount;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SocialAccountController extends Controller
{
    /**
     * Show the form to add a new social account.
     */
    public function create(Subject $subject)
    {
        // Ensure user has access
        if (auth()->user()->role !== \App\Models\User::ROLE_ADMIN && $subject->user_id !== auth()->id()) {
            abort(403);
        }

        return view('subjects.add-account', compact('subject'));
    }

    /**
     * Search for a social media profile via the Node.js scraper service.
     */
    public function search(Request $request, SocialSearchInterface $searchService)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        // Fetch a cookie from an active Facebook bot
        $bot = \App\Models\Bot::where('platform', 'facebook')
            ->where('status', 'active')
            ->whereNotNull('cookie')
            ->inRandomOrder()
            ->first();

        $cookie = $bot ? $bot->cookie : null;

        $results = $searchService->search($request->input('query'), $cookie);

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }

    /**
     * Store a new social account link to a subject.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id'          => 'required|exists:subjects,id',
            'platform'            => 'required|string',
            'platform_account_id' => 'nullable|string',
            'account_name'        => 'required|string|max:255',
            'account_url'         => [
                'required',
                'string',
                Rule::unique('social_accounts')->where(function ($query) use ($request) {
                    return $query->where('subject_id', $request->subject_id);
                }),
            ],
            'account_type'        => 'nullable|string',
            'profile_pic_url'     => 'nullable|string',
        ], [
            'account_url.unique' => 'This social account is already linked to this profile.',
        ]);

        // Ensure user has access to this subject
        $subject = Subject::when(auth()->user()->role !== \App\Models\User::ROLE_ADMIN, function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($request->subject_id);

        $validated['created_by_id'] = auth()->id();
        $validated['status']        = SocialAccount::STATUS_ACTIVE;

        $subject->socialAccounts()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Account linked successfully.',
        ]);
    }

    /**
     * Remove the specified social account link.
     */
    public function destroy(string $id)
    {
        $account = SocialAccount::whereHas('subject', function ($q) {
            if (auth()->user()->role !== \App\Models\User::ROLE_ADMIN) {
                $q->where('user_id', auth()->id());
            }
        })->findOrFail($id);

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account link removed successfully.',
        ]);
    }

    /**
     * Dispatch a background job to scrape the social account.
     */
    public function scrape(string $id)
    {
        $account = SocialAccount::whereHas('subject', function ($q) {
            if (auth()->user()->role !== \App\Models\User::ROLE_ADMIN) {
                $q->where('user_id', auth()->id());
            }
        })->findOrFail($id);

        if ($account->scrape_status === 'scraping') {
            return response()->json([
                'success' => false,
                'message' => 'Scraping is already in progress.',
            ], 422);
        }

        $account->update(['scrape_status' => 'scraping']);
        \App\Jobs\ScrapeSocialAccountJob::dispatch($account);

        return response()->json([
            'success' => true,
            'message' => 'Scraping started.',
            'status' => 'scraping'
        ]);
    }

    /**
     * Check the current scrape status of the account.
     */
    public function checkStatus(string $id)
    {
        $account = SocialAccount::whereHas('subject', function ($q) {
            if (auth()->user()->role !== \App\Models\User::ROLE_ADMIN) {
                $q->where('user_id', auth()->id());
            }
        })->findOrFail($id);

        return response()->json([
            'success' => true,
            'status' => $account->scrape_status,
            'last_scraped_at' => $account->last_scraped_at ? $account->last_scraped_at->diffForHumans() : 'Never'
        ]);
    }

    /**
     * Dispatch a synchronous job to scrape the social account immediately.
     */
    public function syncScrape(string $id)
    {
        $account = SocialAccount::whereHas('subject', function ($q) {
            if (auth()->user()->role !== \App\Models\User::ROLE_ADMIN) {
                $q->where('user_id', auth()->id());
            }
        })->findOrFail($id);

        if ($account->scrape_status === 'scraping') {
            return response()->json([
                'success' => false,
                'message' => 'Scraping is already in progress.',
            ], 422);
        }

        $account->update(['scrape_status' => 'scraping']);
        
        // Run it synchronously right now
        \App\Jobs\ScrapeSocialAccountJob::dispatchSync($account);
        
        // After dispatchSync, the job is completed.
        $account->refresh();

        $logs = \App\Models\ScrapeLog::where('social_account_id', $account->id)->latest()->first();

        return response()->json([
            'success' => $account->scrape_status !== 'failed',
            'message' => 'Synchronous scraping finished.',
            'status' => $account->scrape_status,
            'log' => $logs
        ]);
    }
}
