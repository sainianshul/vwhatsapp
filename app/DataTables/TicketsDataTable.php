<?php

namespace App\DataTables;

use App\Models\Ticket;
use Yajra\DataTables\Services\DataTable;

class TicketsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('created_at', function(Ticket $ticket) {
                return $ticket->created_at ? $ticket->created_at->format('d M Y, h:i A') : '-';
            })
            ->addColumn('user', function(Ticket $ticket) {
                return $ticket->user ? $ticket->user->name : '-';
            })
            ->addColumn('status', function (Ticket $ticket) {
                $color = $ticket->status === 'open' ? 'success' : 'danger';
                return '<span class="badge badge-light-'.$color.' border border-'.$color.' fw-bold">'.ucfirst($ticket->status).'</span>';
            })
            ->addColumn('actions', function (Ticket $ticket) {
                return '
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="'.route('tickets.show', $ticket->id).'" class="btn btn-sm btn-light btn-active-light-primary border" title="View">
                            View
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['status', 'actions']);
    }

    public function query(Ticket $model)
    {
        $user = auth()->user();
        $query = $model->newQuery()->with('user');
        
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
                    ->setTableId('tickets-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(0)
                    ->parameters([
                        'dom' => 'Bfrtip',
                        'buttons' => ['excel', 'csv', 'pdf', 'print', 'reset', 'reload'],
                    ]);
    }

    protected function getColumns()
    {
        $columns = [
            ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
        ];

        if (auth()->check() && auth()->user()->role === 'admin') {
            $columns[] = ['data' => 'user', 'name' => 'user.name', 'title' => 'User', 'orderable' => false];
        }

        $columns = array_merge($columns, [
            ['data' => 'subject', 'name' => 'subject', 'title' => 'Subject'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'class' => 'text-end'],
        ]);

        return $columns;
    }
}
