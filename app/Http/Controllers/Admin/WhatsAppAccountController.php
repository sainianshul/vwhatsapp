<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WhatsAppAccount;
use App\DataTables\WhatsAppAccountDataTable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class WhatsAppAccountController extends Controller
{
    private $nodeUrl;

    public function __construct()
    {
        $this->nodeUrl = env('NODE_MICROSERVICE_URL', 'http://whatsapp-service:3000');
    }

    public function index(WhatsAppAccountDataTable $dataTable)
    {
        // Auto-sync: check real session status from Node for all "connected" accounts
        $this->syncSessionStatuses();

        return $dataTable->render('admin.whatsapp_accounts.index');
    }

    /**
     * Sync DB status with actual Node.js session status.
     * Fast — single HTTP call to /api/health, no per-session calls.
     */
    private function syncSessionStatuses()
    {
        try {
            $response = Http::timeout(5)->get("{$this->nodeUrl}/api/health");
            if (!$response->successful()) return;

            $data = $response->json();
            $nodeSessions = collect($data['sessions'] ?? [])
                ->keyBy('id');

            // Get all "connected" accounts from DB
            $connectedAccounts = WhatsAppAccount::where('user_id', auth()->id())
                ->where('status', 'connected')
                ->get();

            foreach ($connectedAccounts as $account) {
                $nodeStatus = $nodeSessions->get($account->session_id);
                $validStates = ['connected', 'syncing_data', 'initializing', 'authenticating', 'qr_ready', 'reconnecting'];

                if (!$nodeStatus || !in_array($nodeStatus['status'], $validStates)) {
                    // Node doesn't have this session or it's dead — trigger reconnect
                    try {
                        Http::timeout(3)->post("{$this->nodeUrl}/api/sessions/{$account->session_id}/reconnect");
                        $account->update(['status' => 'reconnecting']);
                        Log::info("syncSessionStatuses: Triggered reconnect for {$account->session_id}");
                    } catch (\Exception $reconnectErr) {
                        $account->update(['status' => 'disconnected']);
                        Log::info("syncSessionStatuses: Marked {$account->session_id} as disconnected (reconnect failed)");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("syncSessionStatuses: Node unreachable — " . $e->getMessage());
        }
    }

    public function create()
    {
        // 1. Cleanup abandoned/connecting sessions for this user
        $abandoned = WhatsAppAccount::where('user_id', auth()->id())
            ->where('status', 'connecting')
            ->get();

        foreach ($abandoned as $oldSession) {
            // Check real status from Node before deleting to prevent race condition
            try {
                $response = Http::timeout(2)->get("{$this->nodeUrl}/api/sessions/{$oldSession->session_id}/status");
                $data = $response->json();
                if (isset($data['data']['state']) && $data['data']['state'] === 'connected') {
                    // It actually connected! Just update DB and don't delete.
                    $oldSession->status = 'connected';
                    $oldSession->save();
                    continue;
                }
            } catch (\Exception $e) {}

            Log::info("Auto-deleting abandoned session in create(): {$oldSession->session_id}");
            try {
                Http::post("{$this->nodeUrl}/api/sessions/{$oldSession->session_id}/logout");
            } catch (\Exception $e) {}
            // Force delete so they don't bloat the trash
            $oldSession->forceDelete();
        }

        // 2. Generate a new unique session ID
        $sessionId = 'session_' . Str::random(10);
        
        // Save the pending record in database
        $account = WhatsAppAccount::create([
            'user_id' => auth()->id(),
            'session_id' => $sessionId,
            'status' => 'connecting',
        ]);

        return view('admin.whatsapp_accounts.create_qr', compact('account', 'sessionId'));
    }

    public function startSession(Request $request)
    {
        $request->validate(['session_id' => 'required|string']);
        $account = WhatsAppAccount::where('session_id', $request->session_id)->firstOrFail();

        // Tell Node microservice to start generating QR code
        try {
            Http::post("{$this->nodeUrl}/api/sessions/start", [
                'sessionId' => $request->session_id
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            $account->update(['status' => 'error']);
            return response()->json(['success' => false, 'message' => 'Microservice is offline.']);
        }
    }

    public function show($id)
    {
        $account = WhatsAppAccount::findOrFail($id);
        
        $stats = [
            'total_messages' => \App\Models\WhatsAppMessage::where('whatsapp_account_id', $account->id)->count(),
            'total_campaigns' => \App\Models\BulkCampaign::where('whatsapp_account_id', $account->id)->count(),
        ];

        return view('admin.whatsapp_accounts.show', compact('account', 'stats'));
    }

    public function edit($id)
    {
        $account = WhatsAppAccount::findOrFail($id);
        return view('admin.whatsapp_accounts.edit', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $account = WhatsAppAccount::findOrFail($id);
        $request->validate([
            'name' => 'nullable|string|max:255',
        ]);
        
        $account->update(['name' => $request->name]);
        return redirect()->route('whatsapp_accounts.index')->with('success', 'Account updated successfully.');
    }

    public function qrStatus($sessionId)
    {
        try {
            $response = Http::timeout(5)->get("{$this->nodeUrl}/api/sessions/{$sessionId}/qr");
            $data = $response->json();

            // If Node returned a non-200 or malformed response, just tell frontend to wait
            if (!$response->successful() || !is_array($data)) {
                return response()->json(['status' => 'waiting']);
            }

            // Check if status changed to connected
            if (isset($data['data']['state']) && $data['data']['state'] === 'connected') {
                $phone = $data['data']['phone'] ?? null;
                $name = $data['data']['name'] ?? null;
                $profilePic = $data['data']['profile_pic_url'] ?? null;

                $account = WhatsAppAccount::where('session_id', $sessionId)->first();
                if ($account) {
                    $account->status = 'connected';
                    $account->phone_number = $phone;
                    $account->push_name = $name;
                    $account->profile_pic_url = $profilePic;
                    $account->save();
                }
                return response()->json(['status' => 'connected']);
            }

            if (isset($data['data']['qr'])) {
                return response()->json(['status' => 'pending', 'qr' => $data['data']['qr']]);
            }

            if (isset($data['status']) && $data['status'] === 'syncing') {
                return response()->json(['status' => 'syncing']);
            }

            // Node returned 'failed' (session truly dead) 
            if (isset($data['status']) && $data['status'] === 'failed') {
                return response()->json(['status' => 'failed']);
            }

            return response()->json(['status' => 'waiting']);

        } catch (\Exception $e) {
            Log::warning("qrStatus transient error for {$sessionId}: " . $e->getMessage());
            return response()->json(['status' => 'waiting']);
        }
    }

    /**
     * Reconnect a disconnected session without re-scanning QR.
     */
    public function reconnect($id)
    {
        $account = WhatsAppAccount::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            $response = Http::timeout(10)->post("{$this->nodeUrl}/api/sessions/{$account->session_id}/reconnect");
            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'success') {
                $account->update(['status' => 'reconnecting']);
                return response()->json([
                    'success' => true,
                    'message' => 'Reconnection initiated. Please wait...'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Failed to reconnect. Please try scanning QR again.'
            ], 400);
        } catch (\Exception $e) {
            Log::error("Reconnect failed for account {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'WhatsApp service is offline. Please try again later.'
            ], 503);
        }
    }

    /**
     * Check the reconnection status of a session (polled by AJAX).
     */
    public function reconnectStatus($id)
    {
        $account = WhatsAppAccount::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            $response = Http::timeout(5)->get("{$this->nodeUrl}/api/sessions/{$account->session_id}/status");
            $data = $response->json();
            $nodeState = $data['data']['state'] ?? 'unknown';

            // Update DB if connected
            if ($nodeState === 'connected') {
                // Fetch user info from QR endpoint
                $qrResponse = Http::timeout(5)->get("{$this->nodeUrl}/api/sessions/{$account->session_id}/qr");
                $qrData = $qrResponse->json();

                $account->status = 'connected';
                $account->phone_number = $qrData['data']['phone'] ?? $account->phone_number;
                $account->push_name = $qrData['data']['name'] ?? $account->push_name;
                $account->profile_pic_url = $qrData['data']['profile_pic_url'] ?? $account->profile_pic_url;
                $account->save();

                return response()->json(['status' => 'connected']);
            }

            if (in_array($nodeState, ['initializing', 'authenticating', 'syncing_data', 'reconnecting'])) {
                return response()->json(['status' => 'reconnecting', 'state' => $nodeState]);
            }

            if ($nodeState === 'error' || $nodeState === 'auth_failed') {
                $account->update(['status' => 'disconnected']);
                return response()->json(['status' => 'failed', 'message' => 'Reconnection failed. Please scan QR again.']);
            }

            return response()->json(['status' => 'waiting']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'waiting']);
        }
    }

    public function destroy($id)
    {
        $account = WhatsAppAccount::findOrFail($id);
        
        try {
            Log::info("Explicitly deleting session in destroy(): {$account->session_id}");
            Http::post("{$this->nodeUrl}/api/sessions/{$account->session_id}/logout");
        } catch (\Exception $e) {
            // Ignore error if microservice is offline, just delete from DB
        }

        $account->delete();

        if (request()->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Account deleted successfully.']);
        }

        return redirect()->route('whatsapp_accounts.index')->with('success', 'Account deleted successfully.');
    }

    public function trash()
    {
        if (request()->ajax()) {
            $query = WhatsAppAccount::onlyTrashed()->where('user_id', auth()->id());
            return datatables()->of($query)
                ->editColumn('created_at', function($row) {
                    return $row->created_at->format('Y-m-d H:i');
                })
                ->make(true);
        }

        return view('admin.whatsapp_accounts.trash');
    }

    public function forceDelete($id)
    {
        $account = WhatsAppAccount::onlyTrashed()->findOrFail($id);
        $account->forceDelete();

        if (request()->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Account permanently deleted.']);
        }

        return redirect()->route('whatsapp_accounts.trash')->with('success', 'Account permanently deleted.');
    }
}
