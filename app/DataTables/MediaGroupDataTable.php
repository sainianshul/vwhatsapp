<?php

namespace App\DataTables;

use App\Models\MediaGroup;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class MediaGroupDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('assets_count', function (MediaGroup $group) {
                return '<span class="badge badge-light-primary fw-bold px-2 py-1 fs-8">' . $group->assets()->count() . ' Assets</span>';
            })
            ->editColumn('created_at', function (MediaGroup $group) {
                return '<div class="fw-semibold">'.$group->created_at->format('d M Y, H:i').'</div>';
            })
            ->addColumn('actions', function (MediaGroup $group) {
                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="'.route('admin.media_library.groups.show', $group->id).'" class="btn btn-sm btn-light-primary fw-bold" title="Manage Assets">
                            Manage Assets
                        </a>
                        <button type="button" class="btn btn-sm btn-icon btn-light-info border border-info w-30px h-30px ms-2" title="Edit Group" onclick="editGroup('.$group->id.', \''.htmlspecialchars($group->name, ENT_QUOTES).'\')">
                            <i class="ki-outline ki-pencil fs-5"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-active-light-danger w-30px h-30px btn-delete" data-url="'.route('admin.media_library.groups.destroy', $group->id).'" data-bs-toggle="tooltip" title="Delete Group">
                            <i class="ki-outline ki-trash fs-3"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['assets_count', 'created_at', 'actions'])
            ->setRowId('id');
    }

    public function query(MediaGroup $model): QueryBuilder
    {
        return $model->newQuery()->where('user_id', auth()->id());
    }
}
