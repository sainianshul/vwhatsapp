<?php

namespace App\DataTables;

use App\Models\LoginHistory;
use Yajra\DataTables\Services\DataTable;

class LoginHistoryDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('user', function (LoginHistory $history) {
                $user = $history->user;
                $name = trim((string) ($user?->name ?? 'Unknown User'));
                $email = trim((string) ($user?->email ?? 'No email'));

                return '
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-semibold">'.e($name).'</span>
                    </div>
                ';
            })
            ->editColumn('logged_in_at', function (LoginHistory $history) {
                if (!$history->logged_in_at) return '—';
                return '
                    <div class="fw-semibold">'.$history->logged_in_at->format('d M Y').'</div>
                ';
            })
            ->addColumn('device', function (LoginHistory $history) {
                $platform = $history->platform ?? 'Unknown';
                $type = $history->device_type ?? 'Unknown';
                return '
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-semibold">'.e($platform).'</span>
                        <span class="text-muted fs-7">'.e($type).'</span>
                    </div>
                ';
            })
            ->addColumn('ip', function (LoginHistory $history) {
                return '<span class="badge badge-light-dark">'.e($history->ip_address ?? 'Unknown').'</span>';
            })
            ->addColumn('status_badge', function (LoginHistory $history) {
                return '<span class="badge badge-light-success">Success</span>';
            })
            ->addColumn('actions', function (LoginHistory $history) {
                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger border border-danger w-30px h-30px btn-delete" 
                            data-url="'.route('admin.login_history.destroy', $history->id).'" title="Delete">
                            <i class="ki-outline ki-trash fs-5"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['user', 'logged_in_at', 'device', 'ip', 'status_badge', 'actions']);
    }

    public function query(LoginHistory $model)
    {
        return $model->newQuery()->with('user');
    }
}
