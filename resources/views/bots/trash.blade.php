@extends('layouts.app')

@section('title', 'Trashed Bots')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'Bots', 'url' => route('bots.index')],
        ['label' => 'Trash'],
    ]" />

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-5 pb-3">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">Deleted Bots</span>
                <span class="text-muted mt-1 fw-semibold fs-7">Bots that have been removed from the active list.</span>
            </h3>
        </div>

        <div class="card-body py-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="bots-trash-table">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th>S.No</th>
                            <th class="min-w-200px">Bot Info</th>
                            <th>Status</th>
                            <th>Deleted At</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('datatables_css')
    @include('layouts.partials._datatable-cdn-css')
@endpush

@push('datatables_js')
    @include('layouts.partials._datatable-cdn-js')

<script>
    $(document).ready(function() {
        $('#bots-trash-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('bots.trash') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'bot_info', name: 'name' },
                { data: 'status_badge', name: 'status' },
                { data: 'deleted_at_formatted', name: 'deleted_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
            ],
            language: {
                emptyTable: `<div class="d-flex flex-column flex-center p-10">
                                <i class="ki-outline ki-trash fs-5x text-gray-400 mb-5"></i>
                                <div class="fs-4 fw-bold text-gray-900 mb-2">Trash is Empty</div>
                                <div class="fs-6 text-gray-500">There are no deleted bots.</div>
                             </div>`
            }
        });
    });
</script>
@endpush
