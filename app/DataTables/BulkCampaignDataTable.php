<?php

namespace App\DataTables;

use App\Models\BulkCampaign;
use Yajra\DataTables\Services\DataTable;

class BulkCampaignDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('campaign_name', function (BulkCampaign $campaign) {
                return '
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-bold">'.e($campaign->campaign_name).'</span>
                    </div>
                ';
            })
            ->editColumn('status', function (BulkCampaign $campaign) {
                if ($campaign->status === 'completed') {
                    return '<span class="badge badge-light-success border border-success fw-bold">Completed</span>';
                } elseif ($campaign->status === 'running' || $campaign->status === 'processing') {
                    return '<span class="badge badge-light-primary border border-primary fw-bold">Running</span>';
                } elseif ($campaign->status === 'failed') {
                    return '<span class="badge badge-light-danger border border-danger fw-bold">Failed</span>';
                } elseif ($campaign->status === 'scheduled') {
                    $scheduledTime = $campaign->scheduled_at ? $campaign->scheduled_at->format('d M, h:i A') : '';
                    return '<span class="badge badge-light-info border border-info fw-bold">Scheduled</span>'
                         . ($scheduledTime ? '<div class="text-muted fs-8 mt-1">' . $scheduledTime . '</div>' : '');
                }
                return '<span class="badge badge-light-warning border border-warning fw-bold">'.ucfirst($campaign->status ?: 'Pending').'</span>';
            })
            ->editColumn('created_at', function (BulkCampaign $campaign) {
                return '<div class="fw-semibold">'.$campaign->created_at->format('d M Y, H:i').'</div>';
            })
            ->addColumn('actions', function (BulkCampaign $campaign) {
                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="'.route('admin.bulk_campaigns.show', $campaign->id).'" class="btn btn-sm btn-icon btn-light-primary border border-primary w-30px h-30px" title="View Report">
                            <i class="ki-outline ki-eye fs-5"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['campaign_name', 'status', 'created_at', 'actions']);
    }

    public function query(BulkCampaign $model)
    {
        return $model->newQuery()->where('user_id', auth()->id());
    }
}
