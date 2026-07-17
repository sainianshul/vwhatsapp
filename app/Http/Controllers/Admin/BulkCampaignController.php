<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BulkCampaign;
use App\Models\WhatsAppAccount;
use App\Jobs\ProcessBulkCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BulkCampaignController extends Controller
{
    public function index(\App\DataTables\BulkCampaignDataTable $dataTable)
    {
        return $dataTable->render('admin.bulk_campaigns.index');
    }

    public function create()
    {
        $accounts = WhatsAppAccount::where('user_id', auth()->id())->where('status', 'connected')->get();
        return view('admin.bulk_campaigns.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'whatsapp_account_id' => 'required|exists:whats_app_accounts,id',
            'message_template' => 'required|string',
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB
            'media_file' => 'nullable|file|max:16384|mimes:jpg,jpeg,png,gif,mp4,mp3,ogg,pdf,doc,docx,xls,xlsx,zip', // 16MB
            'scheduled_at' => 'nullable|date|after:now',
            'delay_min' => 'required|integer|min:1',
            'delay_max' => 'required|integer|gt:delay_min',
        ]);

        $account = WhatsAppAccount::where('user_id', auth()->id())
            ->where('id', $request->whatsapp_account_id)
            ->firstOrFail();

        // Store CSV file
        $path = $request->file('csv_file')->store('campaigns/csv', 'local');

        // Count total contacts (basic count)
        $file = fopen(Storage::path($path), 'r');
        $headers = fgetcsv($file); // Skip header

        // Validate CSV Headers
        if (!$headers) {
            fclose($file);
            Storage::disk('local')->delete($path);
            return back()->withInput()->withErrors(['csv_file' => 'The uploaded CSV file is empty or invalid.']);
        }

        $lowercaseHeaders = array_map('strtolower', array_map('trim', $headers));
        if (!in_array('phone', $lowercaseHeaders)) {
            fclose($file);
            Storage::disk('local')->delete($path);
            return back()->withInput()->withErrors(['csv_file' => 'The CSV file must contain a "phone" column.']);
        }

        $totalContacts = 0;
        while (fgetcsv($file) !== false) {
            $totalContacts++;
        }
        fclose($file);

        // Store media file (optional)
        $mediaPath = null;
        if ($request->hasFile('media_file')) {
            $mediaPath = $request->file('media_file')->store('campaigns/media', 'local');
        }

        // Determine if scheduled or immediate
        $isScheduled = $request->filled('scheduled_at');
        $scheduledAt = $isScheduled ? Carbon::parse($request->scheduled_at) : null;

        // Create campaign
        $campaign = BulkCampaign::create([
            'user_id' => auth()->id(),
            'whatsapp_account_id' => $account->id,
            'campaign_name' => $request->campaign_name,
            'message_template' => $request->message_template,
            'csv_file_path' => $path,
            'media_path' => $mediaPath,
            'total_contacts' => $totalContacts,
            'status' => $isScheduled ? 'scheduled' : 'pending',
            'scheduled_at' => $scheduledAt,
            'delay_min' => $request->delay_min,
            'delay_max' => $request->delay_max,
        ]);

        // Dispatch Job (with delay if scheduled)
        if ($isScheduled) {
            ProcessBulkCampaign::dispatch($campaign)->delay($scheduledAt);
            $successMsg = 'Campaign scheduled for ' . $scheduledAt->format('d M Y, h:i A') . '!';
        } else {
            ProcessBulkCampaign::dispatch($campaign);
            $successMsg = 'Campaign created and queued for sending!';
        }

        return redirect()->route('admin.bulk_campaigns.index')->with('success', $successMsg);
    }

    public function show(BulkCampaign $bulkCampaign, \App\DataTables\BulkCampaignReportDataTable $dataTable)
    {
        if ($bulkCampaign->user_id !== auth()->id()) {
            abort(403);
        }

        return $dataTable->withCampaignId($bulkCampaign->id)->render('admin.bulk_campaigns.show', compact('bulkCampaign'));
    }

    public function stats(BulkCampaign $bulkCampaign)
    {
        if ($bulkCampaign->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $bulkCampaign->status,
            'total_contacts' => $bulkCampaign->total_contacts,
            'sent_count' => $bulkCampaign->sent_count,
            'failed_count' => $bulkCampaign->failed_count,
            'status_label' => ucfirst($bulkCampaign->status)
        ]);
    }

    public function downloadSampleCsv()
    {
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=sample_contacts.csv',
            'Expires' => '0',
            'Pragma' => 'public'
        ];

        $list = [
            ['phone', 'name', 'Offer', 'Month'],
            ['919876543210', 'Rahul Kumar', 'Discount20', 'July'],
            ['919876543211', 'Anjali Singh', 'Offer50', 'August']
        ];

        $callback = function () use ($list) {
            $FH = fopen('php://output', 'w');
            foreach ($list as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCsv(Request $request, BulkCampaign $bulkCampaign)
    {
        if ($bulkCampaign->user_id !== auth()->id()) {
            abort(403);
        }

        $status = $request->query('status', 'all');

        $query = \App\Models\WhatsAppMessage::where('bulk_campaign_id', $bulkCampaign->id);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $fileName = 'campaign_' . $bulkCampaign->id . '_' . $status . '_report.csv';

        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Expires' => '0',
            'Pragma' => 'public'
        ];

        // Fetch first record to get original dynamic headers (clone query to avoid LIMIT 1 bug)
        $firstRecord = (clone $query)->first();
        $originalHeaders = ['phone']; // Default fallback
        if ($firstRecord && is_array($firstRecord->variables) && count($firstRecord->variables) > 0) {
            $originalHeaders = array_keys($firstRecord->variables);
        }

        // Add report-specific columns at the end
        $csvHeaders = array_merge($originalHeaders, ['Status', 'Sent At', 'Error Message']);

        $callback = function () use ($query, $originalHeaders, $csvHeaders) {
            $file = fopen('php://output', 'w');

            // Output exactly the original CSV headers + report headers
            fputcsv($file, $csvHeaders);

            foreach ($query->cursor() as $msg) {
                $row = [];
                $variables = is_array($msg->variables) ? $msg->variables : [];

                // 1. Fill original CSV column values
                foreach ($originalHeaders as $header) {
                    if (array_key_exists($header, $variables)) {
                        $row[] = $variables[$header];
                    } else if (strtolower($header) === 'phone') {
                        $row[] = $msg->receiver_number;
                    } else {
                        $row[] = '';
                    }
                }

                // 2. Fill report data
                $row[] = ucfirst($msg->status);
                $row[] = $msg->updated_at ? $msg->updated_at->format('Y-m-d H:i:s') : '';
                $row[] = $msg->error_message ?? '';

                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
