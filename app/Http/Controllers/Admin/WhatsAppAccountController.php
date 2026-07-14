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
    private $nodeUrl = 'http://whatsapp-service:3000'; // Docker internal URL

    public function index(WhatsAppAccountDataTable $dataTable)
    {
        return $dataTable->render('admin.whatsapp_accounts.index');
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
            // Network error / timeout — DON'T tell frontend it's an error.
            // Just say "waiting" so it retries on the next poll.
            Log::warning("qrStatus transient error for {$sessionId}: " . $e->getMessage());
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
