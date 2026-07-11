<?php

namespace App\DataTables;

use App\Models\Bot;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;

class BotsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('bot_info', function (Bot $bot) {
                $platformColor = $bot->platform_color;
                $platformLabel = ucfirst($bot->platform);
                $initial = strtoupper(substr($bot->name, 0, 1));
                $username = $bot->platform_username ? '<span class="text-gray-500 fs-7">@'.$bot->platform_username.'</span>' : '';

                return '
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-45px me-3">
                            <span class="symbol-label bg-light-'.$platformColor.' border border-'.$platformColor.' border-dashed text-'.$platformColor.' fs-4 fw-bold">'.$initial.'</span>
                        </div>
                        <div class="d-flex flex-column">
                            <a href="'.route('bots.show', $bot->id).'" class="text-gray-900 text-hover-primary fw-bold fs-6">'.$bot->name.'</a>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-light-'.$platformColor.' fs-8 fw-bold">'.$platformLabel.'</span>
                                '.$username.'
                            </div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('type_badge', function (Bot $bot) {
                $color = match($bot->type) {
                    Bot::TYPE_SCRAPER => 'info',
                    Bot::TYPE_ACTION  => 'warning',
                    Bot::TYPE_BOTH    => 'primary',
                    default           => 'secondary',
                };
                return '<span class="badge badge-light-'.$color.' fw-bold">'.ucfirst($bot->type).'</span>';
            })
            ->addColumn('status_badge', function (Bot $bot) {
                $color = $bot->status_color;
                return '<span class="badge badge-light-'.$color.' fw-bold">'.ucfirst($bot->status).'</span>';
            })
            ->addColumn('platform_status_badge', function (Bot $bot) {
                $color = $bot->platform_status_color;
                $label = Bot::getPlatformStatusList()[$bot->platform_status] ?? ucfirst($bot->platform_status);
                return '<span class="badge badge-light-'.$color.' fw-bold">'.$label.'</span>';
            })
            ->addColumn('cookie_status', function (Bot $bot) {
                if ($bot->has_cookie) {
                    $updated = $bot->cookie_updated_at ? $bot->cookie_updated_at->diffForHumans() : 'N/A';
                    return '<span class="badge badge-light-success fw-bold">Active</span>
                            <div class="text-gray-500 fs-8 mt-1">'.$updated.'</div>';
                }
                return '<span class="badge badge-light-danger fw-bold">Missing</span>';
            })
            ->editColumn('last_action_at', function (Bot $bot) {
                return $bot->last_action_at ? $bot->last_action_at->format('d M Y, h:i A') : '-';
            })
            ->addColumn('actions', function (Bot $bot) {
                return '
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="'.route('bots.show', $bot->id).'" class="btn btn-sm btn-icon btn-light-primary border border-primary rounded-circle w-30px h-30px" title="View">
                            <i class="ki-outline ki-eye fs-5"></i>
                        </a>
                        <a href="'.route('bots.edit', $bot->id).'" class="btn btn-sm btn-icon btn-light-warning border border-warning rounded-circle w-30px h-30px" title="Edit">
                            <i class="ki-outline ki-pencil fs-5"></i>
                        </a>
                        <form action="'.route('bots.destroy', $bot->id).'" method="POST" class="d-inline m-0 p-0" onsubmit="return confirm(\'Delete this bot? This action cannot be undone.\');">
                            '.csrf_field().method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-icon btn-light-danger border border-danger rounded-circle w-30px h-30px" title="Delete">
                                <i class="ki-outline ki-trash fs-5"></i>
                            </button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['bot_info', 'type_badge', 'status_badge', 'platform_status_badge', 'cookie_status', 'actions']);
    }

    public function query(Bot $model): Builder
    {
        $query = $model->newQuery()->with('creator');

        if (request('platform')) {
            $query->where('platform', request('platform'));
        }

        if (request('type')) {
            $query->where('type', request('type'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('platform_status')) {
            $query->where('platform_status', request('platform_status'));
        }

        return $query;
    }
}
