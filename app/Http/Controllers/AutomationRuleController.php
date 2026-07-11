<?php

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use App\Models\SocialAccount;
use Illuminate\Http\Request;

class AutomationRuleController extends Controller
{
    /**
     * Store or update an automation rule for a social account.
     */
    public function store(Request $request, SocialAccount $account)
    {
        $this->authorizeAccount($account);

        $validated = $request->validate([
            'automation_template_id' => 'required|exists:automation_templates,id',
            'sync_interval_hours'    => 'required|integer|min:1|max:72',
            'is_active'              => 'boolean',
        ]);

        $validated['created_by_id'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');
        $validated['next_sync_at'] = now()->addMinutes(5); // Start checking soon

        AutomationRule::updateOrCreate(
            ['social_account_id' => $account->id],
            $validated
        );

        return redirect()->back()->with('success', 'Auto-Engage settings saved successfully.');
    }

    /**
     * Remove the automation rule (Turn off Auto-Engage).
     */
    public function destroy(SocialAccount $account)
    {
        $this->authorizeAccount($account);

        if ($account->automationRule) {
            $account->automationRule->delete();
        }

        return redirect()->back()->with('success', 'Auto-Engage turned off.');
    }

    private function authorizeAccount(SocialAccount $account): void
    {
        if (auth()->user()->role !== \App\Models\User::ROLE_ADMIN && $account->created_by_id !== auth()->id()) {
            abort(403);
        }
    }
}
