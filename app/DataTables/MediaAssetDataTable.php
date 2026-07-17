<?php

namespace App\DataTables;

use App\Models\MediaAsset;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class MediaAssetDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public $groupId;

    public function withGroupId($id)
    {
        $this->groupId = $id;
        return $this;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('preview', function (MediaAsset $asset) {
                $icon = 'ki-document';
                if (str_contains($asset->mime_type, 'image')) $icon = 'ki-picture';
                elseif (str_contains($asset->mime_type, 'video')) $icon = 'ki-video';

                $url = \Illuminate\Support\Facades\Storage::url($asset->file_path);
                
                return '
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-40px me-3">
                            <span class="symbol-label bg-light-primary text-primary">
                                <i class="ki-outline '.$icon.' fs-1"></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <a href="'.$url.'" target="_blank" class="text-gray-800 text-hover-primary mb-1 fw-bold">'.e($asset->name).'</a>
                            <span class="text-muted fs-7">'.e($asset->file_name).'</span>
                        </div>
                    </div>
                ';
            })
            ->addColumn('asset_code_html', function (MediaAsset $asset) {
                return '
                    <div class="d-flex align-items-center gap-2">
                        <code class="fs-6 px-2 py-1 bg-light text-dark rounded border border-gray-300">'.e($asset->asset_code).'</code>
                        <button type="button" class="btn btn-icon btn-sm btn-light-primary w-30px h-30px" onclick="copyToClipboard(\''.htmlspecialchars($asset->asset_code, ENT_QUOTES).'\')" title="Copy Code">
                            <i class="ki-outline ki-copy fs-5"></i>
                        </button>
                    </div>
                ';
            })
            ->editColumn('size', function (MediaAsset $asset) {
                return '<span class="text-gray-700 fw-semibold">'.round($asset->size / 1024 / 1024, 2).' MB</span>';
            })
            ->editColumn('status', function (MediaAsset $asset) {
                $checked = $asset->status === 'active' ? 'checked' : '';
                return '
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-20px w-30px" type="checkbox" value="" '.$checked.' onchange="toggleStatus('.$asset->id.', this.checked)">
                    </div>
                ';
            })
            ->editColumn('created_at', function (MediaAsset $asset) {
                return '<div class="fw-semibold text-gray-700">'.$asset->created_at->format('d M Y, H:i').'</div>';
            })
            ->addColumn('actions', function (MediaAsset $asset) {
                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <button type="button" class="btn btn-sm btn-icon btn-light-info border border-info w-30px h-30px" title="Edit Asset" onclick="editAsset('.$asset->id.', \''.htmlspecialchars($asset->name, ENT_QUOTES).'\')">
                            <i class="ki-outline ki-pencil fs-5"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger border border-danger w-30px h-30px ms-1 btn-delete" data-url="'.route('admin.media_library.assets.destroy', $asset->id).'" data-bs-toggle="tooltip" title="Delete Asset">
                            <i class="ki-outline ki-trash fs-5"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['preview', 'asset_code_html', 'size', 'status', 'created_at', 'actions'])
            ->setRowId('id');
    }

    public function query(MediaAsset $model): QueryBuilder
    {
        return $model->newQuery()
            ->whereHas('group', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->where('media_group_id', $this->groupId);
    }
}
