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
                $mediaBadge = '';
                if ($campaign->media_path) {
                    $mediaBadge = '<div class="mt-1"><span class="badge badge-light-primary fw-bold px-2 py-1 fs-8" title="This campaign includes media"><i class="ki-outline ki-file text-primary fs-7 me-1"></i>With Media</span></div>';
                }
                return '
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-bold">'.e($campaign->campaign_name).'</span>
                        ' . $mediaBadge . '
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
                } elseif ($campaign->status === 'paused') {
                    return '<span class="badge badge-light-warning border border-warning fw-bold">Paused</span>';
                }
                return '<span class="badge badge-light-warning border border-warning fw-bold">'.ucfirst($campaign->status ?: 'Pending').'</span>';
            })
            ->editColumn('created_at', function (BulkCampaign $campaign) {
                return '<div class="fw-semibold">'.$campaign->created_at->format('d M Y, H:i').'</div>';
            })
            ->addColumn('actions', function (BulkCampaign $campaign) {
                $pauseResumeBtn = '';
                if ($campaign->status === 'running') {
                    $pauseResumeBtn = '
                        <button type="button" class="btn btn-sm btn-icon btn-light-warning border border-warning w-30px h-30px ms-1 btn-pause" data-url="'.route('admin.bulk_campaigns.pause', $campaign->id).'" data-bs-toggle="tooltip" title="Pause Campaign">
                            <i class="ki-outline ki-pause fs-5 text-warning"></i>
                        </button>
                    ';
                } elseif ($campaign->status === 'paused') {
                    $pauseResumeBtn = '
                        <button type="button" class="btn btn-sm btn-icon btn-light-success border border-success w-30px h-30px ms-1 btn-resume" data-url="'.route('admin.bulk_campaigns.resume', $campaign->id).'" data-bs-toggle="tooltip" title="Resume Campaign">
                            <i class="ki-outline ki-play fs-5 text-success"></i>
                        </button>
                    ';
                }

                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="'.route('admin.bulk_campaigns.show', $campaign->id).'" class="btn btn-sm btn-icon btn-light-primary border border-primary w-30px h-30px" title="View Report">
                            <i class="ki-outline ki-eye fs-5"></i>
                        </a>
                        ' . $pauseResumeBtn . '
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger border border-danger w-30px h-30px ms-1 btn-delete" data-url="'.route('admin.bulk_campaigns.destroy', $campaign->id).'" data-bs-toggle="tooltip" title="Delete Campaign">
                            <i class="ki-outline ki-trash fs-5"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['campaign_name', 'status', 'created_at', 'actions']);
    }

    public function query(BulkCampaign $model)
    {
        $query = $model->newQuery()->where('user_id', auth()->id());
        
        if (request()->filled('account_id')) {
            $query->where('whatsapp_account_id', request('account_id'));
        }

        return $query;
    }
}
