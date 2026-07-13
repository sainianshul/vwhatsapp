<?php

namespace App\DataTables;

use App\Models\WhatsAppMessage;
use Yajra\DataTables\Services\DataTable;

class WhatsAppMessageDataTable extends DataTable
{
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
                    return '<span class="badge badge-light-success border border-success">Sent</span>';
                } elseif ($msg->status === 'failed') {
                    return '<span class="badge badge-light-danger border border-danger" title="'.e($msg->error_message).'">Failed</span>';
                }
                return '<span class="badge badge-light-warning border border-warning">'.ucfirst($msg->status).'</span>';
            })
            ->addColumn('source', function (WhatsAppMessage $msg) {
                if ($msg->source === 'api') {
                    return '<span class="badge badge-light-primary border border-primary">API</span>';
                }
                return '<span class="badge badge-light-info border border-info">Web</span>';
            })
            ->addColumn('whatsapp_account', function (WhatsAppMessage $msg) {
                return $msg->whatsappAccount->phone_number ?? 'Unknown';
            })
            ->editColumn('created_at', function (WhatsAppMessage $msg) {
                return '<div class="fw-semibold">'.$msg->created_at->format('d M Y, H:i').'</div>';
            })
            ->addColumn('actions', function (WhatsAppMessage $msg) {
                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger border border-danger w-30px h-30px btn-delete" 
                            data-url="'.route('whatsapp_messages.destroy', $msg->id).'" title="Delete">
                            <i class="ki-outline ki-trash fs-5"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['receiver_number', 'message_text', 'status', 'source', 'created_at', 'actions']);
    }

    public function query(WhatsAppMessage $model)
    {
        $query = $model->newQuery()->with('whatsappAccount')->where('user_id', auth()->id())->where('is_bulk', false);

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('account_id')) {
            $query->where('whatsapp_account_id', request('account_id'));
        }

        return $query;
    }
}
