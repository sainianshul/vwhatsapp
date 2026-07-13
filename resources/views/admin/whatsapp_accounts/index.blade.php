@extends('layouts.app')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'WhatsApp Accounts', 'url' => route('whatsapp_accounts.index')],
        ['label' => 'Connected Accounts'],
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
                        placeholder="Search accounts..."
                    />
                </div>

                {{-- Right Controls --}}
                <div class="d-flex align-items-center gap-2">

                    {{-- Refresh Button --}}
                    <button type="button" class="btn btn-icon btn-light btn-active-light-primary border border-gray-300 w-35px h-35px" id="refresh-table-btn" data-bs-toggle="tooltip" title="Refresh">
                        <i class="ki-outline ki-arrows-circle fs-3"></i>
                    </button>

                    {{-- Add Account Button --}}
                    <a href="{{ route('whatsapp_accounts.create') }}" class="btn btn-sm btn-primary fw-semibold btn-flex btn-center">
                        <i class="ki-duotone ki-plus-square fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Add Account
                    </a>

                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body py-4">

            <div id="accounts-table-wrapper" class="table-responsive">
                <table id="accounts-table" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 w-100">
                    <thead>
                        <tr class="text-start text-gray-900 fw-medium fs-7 text-uppercase gs-0 border-bottom border-gray-200 border-1">
                            <th class="w-50px">S.No</th>
                            <th class="min-w-150px">Name</th>
                            <th class="min-w-150px">Phone Number</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-150px">Connected At</th>
                            <th class="text-end min-w-150px pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            @include('layouts.partials._table-skeleton', [
                'id' => 'accounts-skeleton'
            ])
            @include('layouts.partials._table-empty', [
                'id' => 'accounts-empty',
                'title' => 'No accounts found',
                'subtitle' => 'You haven\'t linked any WhatsApp numbers yet.',
                'actionUrl' => route('whatsapp_accounts.create'),
                'actionText' => 'Link Account'
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
            let table = $('#accounts-table').DataTable({
                serverSide: true,
                processing: false,
                ajax: {
                    url: '{{ route('whatsapp_accounts.index') }}',
                },
                columns: [
                    { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'phone_number', name: 'phone_number' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-end pe-3'
                    },
                ],
                order: [[3, 'desc']],
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
                    info: 'Showing _START_–_END_ of _TOTAL_ accounts',
                    infoEmpty: 'No accounts to show',
                    infoFiltered: '(filtered from _MAX_)',
                    lengthMenu: 'Show _MENU_',
                    paginate: {
                        previous: '<i class="ki-duotone ki-arrow-left"></i>',
                        next: '<i class="ki-duotone ki-arrow-right"></i>',
                    },
                },
                initComplete: function () {
                    $('#accounts-skeleton').fadeOut(200, function () {
                        $(this).remove();
                    });
                },
                drawCallback: function () {
                    let total = this.api().page.info().recordsDisplay;
                    if (total === 0) {
                        $('#accounts-table-wrapper').addClass('d-none');
                        $('#accounts-empty').removeClass('d-none');
                    } else {
                        $('#accounts-empty').addClass('d-none');
                        $('#accounts-table-wrapper').removeClass('d-none');
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

            $(document).on('click', '.btn-delete', function () {
                let url = $(this).data('url');
                Swal.fire({
                    title: 'Delete Account?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-light ms-2'
                    },
                    buttonsStyling: false,
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    $.post(url, {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    })
                    .done(function (res) {
                        table.ajax.reload(null, false);
                        Swal.fire({ toast: true, position: 'top', showConfirmButton: false, timer: 1500, icon: 'success', title: 'Account deleted successfully' });
                    })
                    .fail(function (xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
                    });
                });
            });

        });
    </script>
@endpush
