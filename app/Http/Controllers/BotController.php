<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\DataTables\BotsDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BotController extends Controller
{
    public function index(BotsDataTable $dataTable)
    {
        return $dataTable->render('bots.index');
    }

    public function create()
    {
        return view('bots.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'platform'          => 'required|string|in:' . implode(',', array_keys(Bot::getPlatformList())),
            'type'              => 'required|string|in:' . implode(',', array_keys(Bot::getTypeList())),
            'status'            => 'required|string|in:' . implode(',', array_keys(Bot::getStatusList())),
            'platform_status'   => 'required|string|in:' . implode(',', array_keys(Bot::getPlatformStatusList())),
            'platform_username' => 'nullable|string|max:255',
            'platform_user_id'  => 'nullable|string|max:255',
            'gender'            => 'nullable|string|max:50',
            'language'          => 'nullable|string|max:50',
            'slang_level'       => 'nullable|string|max:50',
            'ai_persona'        => 'nullable|string',
            'system_prompt_override' => 'nullable|string',
            'cookie'            => 'nullable|string',
            'user_agent'        => 'nullable|string|max:2000',
            'proxy'             => 'nullable|string|max:255',
            'notes'             => 'nullable|string|max:5000',
        ]);

        // Handle cookie file upload (Puppeteer extension JSON)
        if ($request->hasFile('cookie_file')) {
            $request->validate(['cookie_file' => 'file|mimes:json,txt|max:2048']);
            $validated['cookie'] = file_get_contents($request->file('cookie_file')->getRealPath());
            $validated['cookie_updated_at'] = now();
        } elseif (!empty($validated['cookie'])) {
            $validated['cookie_updated_at'] = now();
        }

        $validated['created_by'] = Auth::id();

        Bot::create($validated);

        return redirect()->route('bots.index')->with('success', 'Bot created successfully.');
    }

    public function show(Bot $bot)
    {
        $bot->load('creator', 'comments.creator');
        return view('bots.show', compact('bot'));
    }

    public function edit(Bot $bot)
    {
        return view('bots.edit', compact('bot'));
    }

    public function update(Request $request, Bot $bot)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'platform'          => 'required|string|in:' . implode(',', array_keys(Bot::getPlatformList())),
            'type'              => 'required|string|in:' . implode(',', array_keys(Bot::getTypeList())),
            'status'            => 'required|string|in:' . implode(',', array_keys(Bot::getStatusList())),
            'platform_status'   => 'required|string|in:' . implode(',', array_keys(Bot::getPlatformStatusList())),
            'platform_username' => 'nullable|string|max:255',
            'platform_user_id'  => 'nullable|string|max:255',
            'gender'            => 'nullable|string|max:50',
            'language'          => 'nullable|string|max:50',
            'slang_level'       => 'nullable|string|max:50',
            'ai_persona'        => 'nullable|string',
            'system_prompt_override' => 'nullable|string',
            'cookie'            => 'nullable|string',
            'user_agent'        => 'nullable|string|max:2000',
            'proxy'             => 'nullable|string|max:255',
            'notes'             => 'nullable|string|max:5000',
        ]);

        // Handle cookie file upload
        if ($request->hasFile('cookie_file')) {
            $request->validate(['cookie_file' => 'file|mimes:json,txt|max:2048']);
            $validated['cookie'] = file_get_contents($request->file('cookie_file')->getRealPath());
            $validated['cookie_updated_at'] = now();
        } elseif (!empty($validated['cookie']) && $validated['cookie'] !== $bot->cookie) {
            $validated['cookie_updated_at'] = now();
        }

        $bot->update($validated);

        return redirect()->route('bots.show', $bot)->with('success', 'Bot updated successfully.');
    }

    public function destroy(Bot $bot)
    {
        $bot->delete();
        return redirect()->route('bots.index')->with('success', 'Bot deleted successfully.');
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = Bot::onlyTrashed()->with('creator');
            return datatables()->eloquent($query)
                ->addIndexColumn()
                ->addColumn('bot_info', function (Bot $bot) {
                    $platformColor = $bot->platform_color;
                    $initial = strtoupper(substr($bot->name, 0, 1));
                    return '
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-circle symbol-45px me-3">
                                <span class="symbol-label bg-light-'.$platformColor.' text-'.$platformColor.' fs-4 fw-bold">'.$initial.'</span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-6">'.$bot->name.'</span>
                                <span class="badge badge-light-'.$platformColor.' fs-8 fw-bold w-auto align-self-start mt-1">'.ucfirst($bot->platform).'</span>
                            </div>
                        </div>
                    ';
                })
                ->addColumn('deleted_at_formatted', function (Bot $bot) {
                    return $bot->deleted_at ? $bot->deleted_at->format('d M Y, h:i A') : '-';
                })
                ->addColumn('status_badge', function (Bot $bot) {
                    $color = $bot->status_color;
                    return '<span class="badge badge-light-'.$color.' fw-bold">'.ucfirst($bot->status).'</span>';
                })
                ->addColumn('actions', function (Bot $bot) {
                    return '
                        <div class="d-flex gap-1 justify-content-end">
                            <form action="'.route('bots.restore', $bot->id).'" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm(\'Restore this bot?\');">
                                '.csrf_field().'
                                <button type="submit" class="btn btn-sm btn-icon btn-light-success border border-success w-30px h-30px" title="Restore">
                                    <i class="ki-outline ki-arrows-circle fs-5"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['bot_info', 'status_badge', 'actions'])
                ->make(true);
        }
        return view('bots.trash');
    }

    public function restore($id)
    {
        $bot = Bot::onlyTrashed()->findOrFail($id);
        $bot->restore();
        return redirect()->route('bots.trash')->with('success', 'Bot restored successfully.');
    }

    public function updateCookie(Request $request, Bot $bot)
    {
        if ($request->hasFile('cookie_file')) {
            $request->validate(['cookie_file' => 'file|mimes:json,txt|max:2048']);
            $bot->update([
                'cookie'            => file_get_contents($request->file('cookie_file')->getRealPath()),
                'cookie_updated_at' => now(),
            ]);
        } elseif ($request->filled('cookie')) {
            $bot->update([
                'cookie'            => $request->input('cookie'),
                'cookie_updated_at' => now(),
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Cookie updated successfully.']);
        }

        return back()->with('success', 'Cookie updated successfully.');
    }

    public function healthCheck(Bot $bot, \App\Services\SocialSearchService $searchService)
    {
        if (!$bot->cookie) {
            return response()->json(['success' => false, 'message' => 'Bot has no cookie to check.']);
        }

        $result = $searchService->checkBotHealth($bot->platform, $bot->cookie);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['error'] ?? 'Health check failed.']);
        }

        $statusMap = [
            'active' => Bot::PLATFORM_STATUS_ACTIVE,
            'expired' => Bot::PLATFORM_STATUS_EXPIRED,
            'restricted' => Bot::PLATFORM_STATUS_RESTRICTED,
        ];

        $platformStatus = $statusMap[$result['status']] ?? Bot::PLATFORM_STATUS_UNKNOWN;

        $bot->update([
            'platform_status' => $platformStatus,
            'platform_status_note' => $result['message'] ?? 'Checked via Health API',
            'platform_status_checked_at' => now(),
        ]);

        return response()->json([
            'success' => true, 
            'status' => $platformStatus,
            'status_color' => $bot->platform_status_color,
            'status_label' => \App\Models\Bot::getPlatformStatusList()[$platformStatus] ?? ucfirst($platformStatus),
            'message' => $result['message'] ?? 'Health check completed.',
            'checked_at' => $bot->platform_status_checked_at->format('d M Y, h:i A')
        ]);
    }
}
