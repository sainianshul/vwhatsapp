<?php

namespace App\DataTables;

use Laravel\Sanctum\PersonalAccessToken;
use Yajra\DataTables\Services\DataTable;

class DeveloperTokenDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('name', function (PersonalAccessToken $token) {
                return '
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-bold">'.e($token->name).'</span>
                    </div>
                ';
            })
            ->editColumn('last_used_at', function (PersonalAccessToken $token) {
                if ($token->last_used_at) {
                    return '<span class="badge badge-light-success border border-success">'.$token->last_used_at->diffForHumans().'</span>';
                }
                return '<span class="badge badge-light-secondary border border-secondary">Never</span>';
            })
            ->editColumn('created_at', function (PersonalAccessToken $token) {
                return '<div class="fw-semibold">'.$token->created_at->format('d M Y, H:i').'</div>';
            })
            ->addColumn('actions', function (PersonalAccessToken $token) {
                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger border border-danger w-30px h-30px btn-delete" 
                            data-url="'.route('admin.developer_settings.revoke', $token->id).'" title="Revoke Token">
                            <i class="ki-outline ki-trash fs-5"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['name', 'last_used_at', 'created_at', 'actions']);
    }

    public function query(PersonalAccessToken $model)
    {
        return $model->newQuery()->where('tokenable_id', auth()->id())->where('tokenable_type', get_class(auth()->user()));
    }
}
