<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppAccount;
use App\Models\WhatsAppMessage;
use App\Jobs\ProcessQuickMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'text' => 'required|string',
            'from' => 'nullable|string'
        ]);

        $user = $request->user();

        // Determine which account to use
        $accountQuery = WhatsAppAccount::where('user_id', $user->id)
            ->where('status', 'connected');
            
        if ($request->from) {
            $accountQuery->where('phone_number', $request->from);
        }

        $account = $accountQuery->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'No active WhatsApp account found' . ($request->from ? " for number {$request->from}" : '.')
            ], 400);
        }

        // Create pending message
        $messageRecord = WhatsAppMessage::create([
            'user_id' => $user->id,
            'whatsapp_account_id' => $account->id,
            'receiver_number' => $request->to,
            'message_text' => $request->text,
            'status' => 'pending',
            'is_bulk' => false,
            'source' => 'api'
        ]);

        // Dispatch Job
        ProcessQuickMessage::dispatch($messageRecord->id);

        return response()->json([
            'success' => true,
            'message' => 'Message queued successfully',
            'data' => [
                'message_id' => $messageRecord->id,
                'status' => 'pending'
            ]
        ], 202);
    }
}
