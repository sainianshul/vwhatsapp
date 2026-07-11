<?php

namespace App\Http\Controllers;

use App\Models\AutomationTemplate;
use Illuminate\Http\Request;

class AutomationTemplateController extends Controller
{
    public function index()
    {
        $templates = AutomationTemplate::where('created_by_id', auth()->id())
            ->withCount('automationRules')
            ->latest()
            ->get();

        return view('automation.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('automation.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'platform'           => 'required|in:facebook,instagram,twitter,youtube',
            'engine_type'        => 'required|in:ai,bank',
            'ai_tone'            => 'required|in:positive,negative,neutral,custom',
            'ai_prompt'          => 'nullable|string|max:2000',
            'keywords_include'   => 'nullable|string|max:500',
            'keywords_exclude'   => 'nullable|string|max:500',
            'min_likes_required' => 'nullable|integer|min:0',
            'min_delay_mins'     => 'nullable|integer|min:1|max:120',
            'max_delay_mins'     => 'nullable|integer|min:1|max:240',
            'max_daily_comments' => 'nullable|integer|min:1|max:500',
        ]);

        // Convert comma-separated keywords to arrays
        $validated['keywords_include'] = $this->parseKeywords($request->keywords_include);
        $validated['keywords_exclude'] = $this->parseKeywords($request->keywords_exclude);
        $validated['created_by_id'] = auth()->id();

        AutomationTemplate::create($validated);

        return redirect()->route('automation-templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(AutomationTemplate $template)
    {
        $this->authorizeTemplate($template);
        return view('automation.templates.edit', compact('template'));
    }

    public function update(Request $request, AutomationTemplate $template)
    {
        $this->authorizeTemplate($template);

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'platform'           => 'required|in:facebook,instagram,twitter,youtube',
            'engine_type'        => 'required|in:ai,bank',
            'ai_tone'            => 'required|in:positive,negative,neutral,custom',
            'ai_prompt'          => 'nullable|string|max:2000',
            'keywords_include'   => 'nullable|string|max:500',
            'keywords_exclude'   => 'nullable|string|max:500',
            'min_likes_required' => 'nullable|integer|min:0',
            'min_delay_mins'     => 'nullable|integer|min:1|max:120',
            'max_delay_mins'     => 'nullable|integer|min:1|max:240',
            'max_daily_comments' => 'nullable|integer|min:1|max:500',
        ]);

        $validated['keywords_include'] = $this->parseKeywords($request->keywords_include);
        $validated['keywords_exclude'] = $this->parseKeywords($request->keywords_exclude);

        $template->update($validated);

        return redirect()->route('automation-templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(AutomationTemplate $template)
    {
        $this->authorizeTemplate($template);

        $template->delete();

        return redirect()->route('automation-templates.index')
            ->with('success', 'Template deleted.');
    }

    // ── Private Helpers ─────────────────────────────────────────

    private function authorizeTemplate(AutomationTemplate $template): void
    {
        if (auth()->user()->role !== \App\Models\User::ROLE_ADMIN && $template->created_by_id !== auth()->id()) {
            abort(403);
        }
    }

    private function parseKeywords(?string $input): ?array
    {
        if (empty($input)) {
            return null;
        }
        return array_values(array_filter(array_map('trim', explode(',', $input))));
    }
}
