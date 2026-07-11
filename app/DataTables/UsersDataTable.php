<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('created_at', function(User $user) {
                return $user->created_at ? $user->created_at->format('d M Y, h:i A') : '-';
            })
            ->addColumn('avatar', function (User $user) {
                if ($user->avatar) {
                    return '<div class="avatar avatar-sm"><img src="'.\Storage::url($user->avatar).'" alt="..." class="avatar-img rounded-circle"></div>';
                }
                $initial = strtoupper(substr($user->name, 0, 1));
                return '<span class="badge badge-light-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-size: 14px;">'.$initial.'</span>';
            })
            ->addColumn('status', function (User $user) {
                $color = $user->status_color;
                return '<span class="badge badge-light-'.$color.' fw-bold">'.ucfirst($user->status).'</span>';
            })
            ->addColumn('role_badge', function (User $user) {
                $role = $user->role ?? \App\Models\User::ROLE_USER;
                $color = match($role) {
                    \App\Models\User::ROLE_ADMIN => 'danger',
                    \App\Models\User::ROLE_MANAGER => 'warning',
                    default => 'primary'
                };
                $label = ucfirst($role);
                return '<span class="badge badge-light-'.$color.' fw-bold">'.$label.'</span>';
            })
            ->addColumn('actions', function (User $user) {
                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="'.route('users.show', $user->id).'" class="btn btn-sm btn-icon btn-light-primary border border-primary w-30px h-30px me-1" title="View">
                            <i class="ki-outline ki-eye fs-5"></i>
                        </a>
                        <a href="'.route('users.edit', $user->id).'" class="btn btn-sm btn-icon btn-light-warning border border-warning w-30px h-30px me-1" title="Edit">
                            <i class="ki-outline ki-pencil fs-5"></i>
                        </a>
                        <form action="'.route('users.destroy', $user->id).'" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm(\'Are you sure you want to delete this user?\');">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-icon btn-light-danger border border-danger w-30px h-30px" title="Delete">
                                <i class="ki-outline ki-trash fs-5"></i>
                            </button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['avatar', 'status', 'role_badge', 'actions']);
    }

    public function query(User $model)
    {
        $query = $model->newQuery()->where('role', '!=', \App\Models\User::ROLE_ADMIN);
        
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        if (request('role')) {
            $query->where('role', request('role'));
        }
        
        return $query;
    }
}
