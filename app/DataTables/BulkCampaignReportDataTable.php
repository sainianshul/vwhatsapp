<?php

namespace App\DataTables;

use App\Models\WhatsAppMessage;
use Yajra\DataTables\Services\DataTable;

class BulkCampaignReportDataTable extends DataTable
{
    protected $campaignId;

    public function withCampaignId($campaignId)
    {
        $this->campaignId = $campaignId;
        return $this;
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('receiver_number', function (WhatsAppMessage $msg) {
                return '<div class="fw-bold text-gray-800">'.e($msg->receiver_number).'</div>';
            })
            ->editColumn('message_text', function (WhatsAppMessage $msg) {
                $text = e($msg->message_text);
                if (strlen($text) > 40) {
                    $truncated = substr($text, 0, 40) . '...';
                    return '<div class="text-gray-800">' . $truncated . ' <a href="javascript:void(0);" class="text-primary fw-bold view-message-btn" data-message="' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '">Read more</a></div>';
                }
                return '<div class="text-gray-800">' . $text . '</div>';
            })
            ->editColumn('status', function (WhatsAppMessage $msg) {
                if ($msg->status === 'sent') {
                    return '<span class="badge badge-light-success border border-success fw-bold">Sent</span>';
                } elseif ($msg->status === 'failed') {
                    $errorMsg = e($msg->error_message ?? 'No error details available');
                    return '<div class="d-flex align-items-center gap-2">
                                <span class="badge badge-light-danger border border-danger fw-bold">Failed</span>
                                <a href="javascript:void(0);" class="text-danger view-error-btn" data-error="' . htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') . '" title="View Logs">
                                    <i class="ki-outline ki-information-5 fs-4 text-danger"></i>
                                </a>
                                <a href="javascript:void(0);" class="text-primary resend-message-btn" data-url="'.route('whatsapp_messages.resend', $msg->id).'" title="Resend Message">
                                    <i class="ki-outline ki-arrows-circle fs-4 text-primary hover-elevate-up"></i>
                                </a>
                            </div>';
                }
                return '<span class="badge badge-light-warning border border-warning fw-bold">Queued</span>';
            })
            ->editColumn('created_at', function (WhatsAppMessage $msg) {
                return '<div class="fw-semibold">'.$msg->created_at->format('d M Y, H:i').'</div>';
            })
            ->rawColumns(['receiver_number', 'message_text', 'status', 'created_at']);
    }

    public function query(WhatsAppMessage $model)
    {
        $query = $model->newQuery()->where('bulk_campaign_id', $this->campaignId);

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        return $query;
    }
}
