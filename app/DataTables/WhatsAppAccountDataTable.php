<?php

namespace App\DataTables;

use App\Models\WhatsAppAccount;
use Yajra\DataTables\Services\DataTable;

class WhatsAppAccountDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('name', function (WhatsAppAccount $account) {
                $name = $account->name ?? '-';
                return '<div class="fw-bold text-gray-800">'.e($name).'</div>';
            })
            ->addColumn('phone_number', function (WhatsAppAccount $account) {
                $avatar = $account->profile_pic_url ?? asset('assets/media/avatars/blank.png');
                $pushName = $account->push_name ?? 'WhatsApp User';
                return '
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-45px me-3">
                            <img src="'.e($avatar).'" alt="DP" class="rounded-circle" />
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fw-bold">'.e($account->phone_number).'</span>
                            <span class="text-muted fs-7">'.e($pushName).'</span>
                        </div>
                    </div>
                ';
            })
            ->editColumn('status', function (WhatsAppAccount $account) {
                if ($account->status === 'connected') {
                    return '<span class="badge badge-light-success border border-success">Connected</span>';
                }
                return '<span class="badge badge-light-warning border border-warning">'.ucfirst($account->status).'</span>';
            })
            ->editColumn('created_at', function (WhatsAppAccount $account) {
                return '<div class="fw-semibold">'.$account->created_at->format('d M Y, H:i').'</div>';
            })
            ->addColumn('actions', function (WhatsAppAccount $account) {
                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="'.route('whatsapp_accounts.show', $account->id).'" class="btn btn-sm btn-icon btn-light-primary border border-primary w-30px h-30px" title="View">
                            <i class="ki-outline ki-eye fs-5"></i>
                        </a>
                        <a href="'.route('whatsapp_accounts.edit', $account->id).'" class="btn btn-sm btn-icon btn-light-info border border-info w-30px h-30px" title="Edit">
                            <i class="ki-outline ki-pencil fs-5"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger border border-danger w-30px h-30px btn-delete" 
                            data-url="'.route('whatsapp_accounts.destroy', $account->id).'" title="Delete">
                            <i class="ki-outline ki-trash fs-5"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['name', 'phone_number', 'status', 'created_at', 'actions']);
    }

    public function query(WhatsAppAccount $model)
    {
        return $model->newQuery()->where('user_id', auth()->id());
    }
}
