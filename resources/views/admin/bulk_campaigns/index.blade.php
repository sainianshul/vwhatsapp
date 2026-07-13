@extends('layouts.app')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'Bulk Messaging', 'url' => route('admin.bulk_campaigns.index')],
        ['label' => 'All Campaigns'],
    ]" />

    <div class="card shadow-sm">

        {{-- Toolbar --}}
        <div class="card-header border-0 pt-5 pb-3">
            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-3">

                {{-- Search --}}
                <div class="d-flex align-items-center position-relative">
                    <i class="ki-duotone ki-magnifier fs-5 text-gray-900 position-absolute ms-4 z-index-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input
                        type="text"
                        id="dt-search"
                        class="form-control form-control-transparent border border-gray-800 text-gray-900 w-250px ps-11 pe-4 fs-7 fw-semibold shadow-sm"
                        placeholder="Search campaigns..."
                    />
                </div>

                {{-- Right Controls --}}
                <div class="d-flex align-items-center gap-2">

                    {{-- Refresh Button --}}
                    <button type="button" class="btn btn-icon btn-light btn-active-light-primary border border-gray-300 w-35px h-35px" id="refresh-table-btn" data-bs-toggle="tooltip" title="Refresh">
                        <i class="ki-outline ki-arrows-circle fs-3"></i>
                    </button>

                    {{-- Create Button --}}
                    <a href="{{ route('admin.bulk_campaigns.create') }}" class="btn btn-sm btn-primary fw-semibold btn-flex btn-center">
                        <i class="ki-duotone ki-plus-square fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        New Campaign
                    </a>

                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body py-4">

            <div id="campaigns-table-wrapper" class="table-responsive">
                <table id="campaigns-table" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 w-100">
                    <thead>
                        <tr class="text-start text-gray-900 fw-medium fs-7 text-uppercase gs-0 border-bottom border-gray-200 border-1">
                            <th class="w-50px">S.No</th>
                            <th class="min-w-200px">Campaign Name</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-150px">Progress</th>
                            <th class="min-w-150px">Created At</th>
                            <th class="text-end min-w-100px pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            @include('layouts.partials._table-skeleton', [
                'id' => 'campaigns-skeleton'
            ])
            @include('layouts.partials._table-empty', [
                'id' => 'campaigns-empty',
                'title' => 'No campaigns found',
                'subtitle' => 'You haven\'t created any bulk campaigns yet.',
                'actionUrl' => route('admin.bulk_campaigns.create'),
                'actionText' => 'Create Campaign'
            ])

        </div>
    </div>

@endsection

@push('datatables_css')
    @include('layouts.partials._datatable-cdn-css')
@endpush

@push('datatables_js')
    @include('layouts.partials._datatable-cdn-js')

    <script>
        $(function () {
            let table = $('#campaigns-table').DataTable({
                serverSide: true,
                processing: false,
                ajax: {
                    url: '{{ route('admin.bulk_campaigns.index') }}',
                },
                columns: [
                    { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },
                    { data: 'campaign_name', name: 'campaign_name' },
                    { data: 'status', name: 'status' },
                    { data: 'stats', name: 'stats', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-end pe-3'
                    },
                ],
                order: [[4, 'desc']],
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
                dom:
                    "<'row'<'col-12'tr>>" +
                    "<'row align-items-center mt-3 pt-3 flex-nowrap'" +
                    "<'col-sm-12 col-md-5'i>" +
                    "<'col-sm-12 col-md-7 d-flex justify-content-md-end align-items-center gap-3'lp>>",
                language: {
                    emptyTable: ' ',
                    zeroRecords: ' ',
                    loadingRecords: ' ',
                    info: 'Showing _START_–_END_ of _TOTAL_ campaigns',
                    infoEmpty: 'No campaigns to show',
                    infoFiltered: '(filtered from _MAX_)',
                    lengthMenu: 'Show _MENU_',
                    paginate: {
                        previous: '<i class="ki-duotone ki-arrow-left"></i>',
                        next: '<i class="ki-duotone ki-arrow-right"></i>',
                    },
                },
                initComplete: function () {
                    $('#campaigns-skeleton').fadeOut(200, function () {
                        $(this).remove();
                    });
                },
                drawCallback: function () {
                    let total = this.api().page.info().recordsDisplay;
                    if (total === 0) {
                        $('#campaigns-table-wrapper').addClass('d-none');
                        $('#campaigns-empty').removeClass('d-none');
                    } else {
                        $('#campaigns-empty').addClass('d-none');
                        $('#campaigns-table-wrapper').removeClass('d-none');
                    }
                    $('[data-bs-toggle="tooltip"]').tooltip({ trigger: 'hover' });
                }
            });

            let searchTimer;
            $('#dt-search').on('input', function () {
                clearTimeout(searchTimer);
                let query = $(this).val();
                searchTimer = setTimeout(function () {
                    table.search(query).draw();
                }, 400);
            });

            $('#refresh-table-btn').on('click', function () {
                $(this).tooltip('hide');
                table.ajax.reload(null, false);
                Swal.fire({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 1500,
                    icon: 'success',
                    title: 'Data refreshed successfully'
                });
            });

        });
    </script>
@endpush
