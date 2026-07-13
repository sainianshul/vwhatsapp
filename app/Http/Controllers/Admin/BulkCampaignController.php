<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BulkCampaign;
use App\Models\WhatsAppAccount;
use App\Jobs\ProcessBulkCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $totalContacts = 0;
        while (fgetcsv($file) !== false) {
            $totalContacts++;
        }
        fclose($file);

        // Create campaign
        $campaign = BulkCampaign::create([
            'user_id' => auth()->id(),
            'whatsapp_account_id' => $account->id,
            'campaign_name' => $request->campaign_name,
            'message_template' => $request->message_template,
            'csv_file_path' => $path,
            'total_contacts' => $totalContacts,
            'status' => 'pending',
            'delay_min' => $request->delay_min,
            'delay_max' => $request->delay_max,
        ]);

        // Dispatch Job
        ProcessBulkCampaign::dispatch($campaign);

        return redirect()->route('admin.bulk_campaigns.index')->with('success', 'Campaign created and queued for sending!');
    }

    public function show(BulkCampaign $bulkCampaign, \App\DataTables\BulkCampaignReportDataTable $dataTable)
    {
        if ($bulkCampaign->user_id !== auth()->id()) {
            abort(403);
        }

        return $dataTable->withCampaignId($bulkCampaign->id)->render('admin.bulk_campaigns.show', compact('bulkCampaign'));
    }

    public function downloadSampleCsv()
    {
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=sample_contacts.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $list = [
            ['phone', 'name', 'var1', 'var2'],
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
}
