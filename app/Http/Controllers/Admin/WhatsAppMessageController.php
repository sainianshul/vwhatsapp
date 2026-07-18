<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppAccount;
use App\Models\WhatsAppMessage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class WhatsAppMessageController extends Controller
{
    protected $nodeUrl;

    public function __construct()
    {
        $this->nodeUrl = env('NODE_MICROSERVICE_URL', 'http://whatsapp-service:3000');
    }

    public function index(\App\DataTables\WhatsAppMessageDataTable $dataTable)
    {
        $accounts = \App\Models\WhatsAppAccount::where('user_id', auth()->id())->get();
        return $dataTable->render('admin.whatsapp_messages.index', compact('accounts'));
    }

    public function create()
    {
        // Only show connected accounts for sending
        $activeAccounts = WhatsAppAccount::where('user_id', auth()->id())
            ->where('status', 'connected')
            ->get();

        return view('admin.whatsapp_messages.create', compact('activeAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'whatsapp_account_id' => 'required|exists:whats_app_accounts,id',
            'receiver_numbers' => 'required|string',
            'message_text' => 'required|string',
            'media_file' => 'nullable|file|max:16384|mimes:jpg,jpeg,png,gif,mp4,mp3,ogg,pdf,doc,docx,xls,xlsx,zip',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $account = WhatsAppAccount::where('user_id', auth()->id())
            ->where('id', $request->whatsapp_account_id)
            ->firstOrFail();

        // Tagify sends data as JSON string e.g. [{"value":"919876543210"},{"value":"919876543211"}]
        $numbersJson = json_decode($request->receiver_numbers, true);
        $finalNumbers = [];

        if (is_array($numbersJson)) {
            // Valid JSON array from Tagify
            foreach ($numbersJson as $numObj) {
                if (isset($numObj['value'])) {
                    $finalNumbers[] = $numObj['value'];
                }
            }
        } else {
            // Fallback: it could be a raw comma-separated string or single number
            $finalNumbers = explode(',', $request->receiver_numbers);
        }

        $queuedCount = 0;

        // Store media file (optional)
        $mediaPath = null;
        $mediaType = null;
        if ($request->hasFile('media_file')) {
            $mediaPath = $request->file('media_file')->store('messages/media', 'local');
            $ext = strtolower($request->file('media_file')->getClientOriginalExtension());
            $typeMap = [
                'jpg' => 'image', 'jpeg' => 'image', 'png' => 'image', 'gif' => 'image',
                'mp4' => 'video',
                'mp3' => 'audio', 'ogg' => 'audio',
                'pdf' => 'document', 'doc' => 'document', 'docx' => 'document',
                'xls' => 'document', 'xlsx' => 'document', 'zip' => 'document',
            ];
            $mediaType = $typeMap[$ext] ?? 'document';
        }

        $isScheduled = $request->filled('scheduled_at');
        $scheduledAt = $isScheduled ? Carbon::parse($request->scheduled_at) : null;

        foreach ($finalNumbers as $numRaw) {
            $number = preg_replace('/[^0-9]/', '', $numRaw);
            if (empty($number)) continue;

            $messageRecord = WhatsAppMessage::create([
                'user_id' => auth()->id(),
                'whatsapp_account_id' => $account->id,
                'receiver_number' => $number,
                'message_text' => $request->message_text,
                'media_path' => $mediaPath,
                'media_type' => $mediaType,
                'status' => $isScheduled ? 'scheduled' : 'pending',
                'scheduled_at' => $scheduledAt,
                'is_bulk' => false,
                'source' => 'web'
            ]);

            // Queue the message
            if ($isScheduled) {
                \App\Jobs\ProcessQuickMessage::dispatch($messageRecord->id)->delay($scheduledAt);
            } else {
                \App\Jobs\ProcessQuickMessage::dispatch($messageRecord->id);
            }
            $queuedCount++;
        }

        if ($request->ajax()) {
            $msg = $isScheduled ? $queuedCount . ' Messages scheduled for ' . $scheduledAt->format('d M Y, h:i A') . '!' : $queuedCount . ' Messages queued for sending!';
            return response()->json([
                'success' => true,
                'message' => $msg
            ]);
        }

        return redirect()->route('whatsapp_messages.index')->with('success', $queuedCount . ' Messages queued for sending!');
    }

    public function destroy($id)
    {
        $message = WhatsAppMessage::where('user_id', auth()->id())->findOrFail($id);
        $message->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Message log deleted.');
    }

    public function resend($id)
    {
        $message = WhatsAppMessage::where('user_id', auth()->id())->findOrFail($id);
        
        $message->update([
            'status' => 'pending',
            'error_message' => null
        ]);

        \App\Jobs\ProcessQuickMessage::dispatch($message->id);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Message queued for resending']);
        }
        return back()->with('success', 'Message queued for resending.');
    }
}
