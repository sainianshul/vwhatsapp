<?php

namespace App\DataTables;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Services\DataTable;

class SubjectsDataTable extends DataTable
{
    protected bool $trash = false;

    public function with(array|string $key, mixed $value = null): static
    {
        if ($key === 'trash') {
            $this->trash = $value;
        }
        return parent::with($key, $value);
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('profile_info', function (Subject $subject) {
                $initial = strtoupper(substr($subject->name, 0, 1));
                $designation = $subject->designation ? '<div class="fs-7 text-muted">'.$subject->designation.'</div>' : '';

                $avatarHtml = '';
                if ($subject->photo_url) {
                    $avatarHtml = '<img src="'.\Storage::url($subject->photo_url).'" alt="'.$subject->name.'" class="w-100 h-100" style="object-fit:cover;" />';
                } else {
                    $avatarHtml = '<span class="symbol-label bg-light-primary text-primary fs-3 fw-bold">'.$initial.'</span>';
                }

                $route = $this->trash ? '#' : route('subjects.show', $subject->id);
                $titleClass = $this->trash ? 'text-gray-600 fw-bold fs-6' : 'text-gray-900 text-hover-primary fw-bold fs-6';

                return '
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-45px me-3 overflow-hidden border border-gray-300">
                            '.$avatarHtml.'
                        </div>
                        <div class="d-flex flex-column">
                            <a href="'.$route.'" class="'.$titleClass.'">'.$subject->name.'</a>
                            '.$designation.'
                        </div>
                    </div>
                ';
            })
            ->addColumn('stats', function (Subject $subject) {
                $accounts = $subject->accounts_count;
                $posts = $subject->total_posts_count;
                return '
                    <div class="d-flex flex-column">
                        <span class="fs-7 fw-semibold text-gray-700"><i class="ki-outline ki-profile-user text-primary me-1"></i> '.$accounts.' Accounts</span>
                        <span class="fs-8 text-muted"><i class="ki-outline ki-message-text-2 text-success me-1"></i> '.$posts.' Posts</span>
                    </div>
                ';
            })
            ->addColumn('status_badge', function (Subject $subject) {
                $color = $subject->status_color;
                return '<span class="badge badge-light-'.$color.' fw-bold">'.ucfirst($subject->status).'</span>';
            })
            ->addColumn('created_at_formatted', function (Subject $subject) {
                return $subject->created_at ? $subject->created_at->format('d M Y') : '-';
            })
            ->addColumn('deleted_at_formatted', function (Subject $subject) {
                return $subject->deleted_at ? $subject->deleted_at->format('d M Y, h:i A') : '-';
            })
            ->addColumn('actions', function (Subject $subject) {
                if ($this->trash) {
                    return '
                        <form action="'.route('subjects.restore', $subject->id).'" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm(\'Restore this profile?\');">
                            '.csrf_field().'
                            <button type="submit" class="btn btn-sm btn-icon btn-light-success rounded-circle w-30px h-30px" title="Restore">
                                <i class="ki-outline ki-arrows-loop fs-5"></i>
                            </button>
                        </form>
                    ';
                }

                return '
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="'.route('subjects.show', $subject->id).'" class="btn btn-sm btn-icon btn-light-primary border border-primary rounded-circle w-30px h-30px" title="View">
                            <i class="ki-outline ki-eye fs-5"></i>
                        </a>
                        <a href="'.route('subjects.edit', $subject->id).'" class="btn btn-sm btn-icon btn-light-warning border border-warning rounded-circle w-30px h-30px" title="Edit">
                            <i class="ki-outline ki-pencil fs-5"></i>
                        </a>
                        <form action="'.route('subjects.destroy', $subject->id).'" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm(\'Delete this profile? This action cannot be undone.\');">
                            '.csrf_field().method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-icon btn-light-danger border border-danger rounded-circle w-30px h-30px" title="Delete">
                                <i class="ki-outline ki-trash fs-5"></i>
                            </button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['profile_info', 'stats', 'status_badge', 'actions']);
    }

    public function query(Subject $model): Builder
    {
        $query = $model->newQuery();

        if (auth()->user()->role !== \App\Models\User::ROLE_ADMIN) {
            $query->where('user_id', auth()->id());
        }

        if ($this->trash) {
            $query->onlyTrashed();
        } else {
            if (request('status')) {
                $query->where('status', request('status'));
            }
        }

        return $query;
    }
}
