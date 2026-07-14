@extends('layouts.app')

@section('title', 'Users')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => 'All Users'],
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
        placeholder="Search by patient name, email or phone..."
    />

</div>
                {{-- Right Controls --}}
                <div class="d-flex align-items-center gap-2">

                    {{-- Refresh Button --}}
                    <button type="button" class="btn btn-icon btn-light btn-active-light-primary border border-gray-300 w-35px h-35px" id="refresh-table-btn" data-bs-toggle="tooltip" title="Refresh">
                        <i class="ki-outline ki-arrows-circle fs-3"></i>
                    </button>

                    {{-- Status Filter --}}
                <div style="width: 145px;">
    <div class="position-relative">
        
        <i class="ki-duotone ki-filter fs-5 text-gray-900 position-absolute top-50 start-0 translate-middle-y ms-4 z-index-3">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>

        <select
            id="filter-status"
            class="form-select form-select-transparent border border-gray-800 text-gray-900 form-select-sm fw-semibold ps-11 shadow-sm"
            data-control="select2"
            data-placeholder="All Status"
            data-allow-clear="true"
            data-hide-search="true"
        >
            <option></option>

            @foreach (\App\Models\User::getStatusList() as $value => $label)
                <option value="{{ $value }}">
                    {{ $label }}
                </option>
            @endforeach
        </select>

    </div>
</div>

                    {{-- Role Filter --}}
                    <div style="width: 145px;">
                        <div class="position-relative">
                            <i class="ki-duotone ki-filter fs-5 text-gray-900 position-absolute top-50 start-0 translate-middle-y ms-4 z-index-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <select
                                id="filter-role"
                                class="form-select form-select-transparent border border-gray-800 text-gray-900 form-select-sm fw-semibold ps-11 shadow-sm"
                                data-control="select2"
                                data-placeholder="All Roles"
                                data-allow-clear="true"
                                data-hide-search="true"
                            >
                                <option></option>
                                @foreach (\App\Models\User::getRoleList() as $value => $label)
                                    <option value="{{ $value }}">
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Add User --}}
                    <a
                        href="{{ route('users.create') }}"
                        class="btn btn-sm btn-primary fw-semibold btn-flex btn-center"
                    >
                        <i class="ki-duotone ki-plus-square fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Add User
                    </a>

                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body py-4">

            <div id="users-table-wrapper" class="table-responsive">

                <table
                    id="users-table"
                    class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 w-100"
                >
                    <thead>
                        <tr class="text-start text-gray-900 fw-medium fs-7 text-uppercase gs-0 border-bottom border-gray-200 border-1">

                            <th class="w-50px">S.No</th>

                            <th class="min-w-250px">
                                User
                            </th>

                            <th class="min-w-120px">
                                Status
                            </th>

                            <th class="min-w-120px">
                                Role
                            </th>

                            <th class="min-w-150px">
                                Last Login
                            </th>

                            <th class="min-w-140px">
                                Joined
                            </th>

                            <th class="text-end min-w-120px pe-3">
                                Actions
                            </th>

                        </tr>
                    </thead>

                    <tbody></tbody>

                </table>
            </div>

            @include('layouts.partials._table-skeleton', [
                'id' => 'users-skeleton'
            ])
            @include('layouts.partials._table-empty', [
                'id' => 'users-empty',
                'title' => 'No users found',
                'subtitle' => 'There are no users matching your criteria. Add a new user to get started.',
                'actionUrl' => route('users.create'),
                'actionText' => 'Add User'
            ])

        </div>
    </div>

@endsection

{{-- DataTables Bundle --}}
@push('datatables_css')
    @include('layouts.partials._datatable-cdn-css')
@endpush

@push('datatables_js')
    @include('layouts.partials._datatable-cdn-js')

    <script>
        $(function () {

            // ── Init ──────────────────────────────────────────────────────────
            let table = $('#users-table').DataTable({
                serverSide: true,
                processing: false,

                ajax: {
                    url: '{{ route('users.index') }}',
                    data: function (d) {
                        d.status = $('#filter-status').val();
                        d.role = $('#filter-role').val();
                    }
                },

                columns: [
                    { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },

                    { data: 'name', name: 'name' },

                    { data: 'status', name: 'status' },

                    { data: 'role_badge', name: 'role' },

                    { data: 'last_login_at', name: 'last_login_at' },

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

                // ──  Bootstrap 5 DOM ────────────────────────────────
                dom:
                    "<'row'<'col-12'tr>>" +
                    "<'row align-items-center mt-3 pt-3 flex-nowrap'" +
                    "<'col-sm-12 col-md-5'i>" +
                    "<'col-sm-12 col-md-7 d-flex justify-content-md-end align-items-center gap-3'lp>>",

                language: {
                    emptyTable: ' ',
                    zeroRecords: ' ',
                    loadingRecords: ' ',

                    info: 'Showing _START_–_END_ of _TOTAL_ patients',

                    infoEmpty: 'No patients to show',

                    infoFiltered: '(filtered from _MAX_)',

                    lengthMenu: 'Show _MENU_',

                    paginate: {
                        previous: '<i class="ki-duotone ki-arrow-left"></i>',
                        next: '<i class="ki-duotone ki-arrow-right"></i>',
                    },
                },



                initComplete: function () {
                    $('#users-skeleton').fadeOut(200, function () {
                        $(this).remove();
                    });
                },

                drawCallback: function () {
                    let total = this.api().page.info().recordsDisplay;

                    if (total === 0) {
                        $('#users-table-wrapper').addClass('d-none');
                        $('#users-empty').removeClass('d-none');
                    } else {
                        $('#users-empty').addClass('d-none');
                        $('#users-table-wrapper').removeClass('d-none');
                    }

                    $('[data-bs-toggle="tooltip"]').tooltip({
                        trigger: 'hover'
                    });
                }
            });

            // ── Search ───────────────────────────────────────────────────────
            let searchTimer;

            $('#dt-search').on('input', function () {
                clearTimeout(searchTimer);
                let query = $(this).val();

                searchTimer = setTimeout(function () {
                    table.search(query).draw();
                }, 400);
            });

            // ── Status Filter ────────────────────────────────────────────────
            $('#filter-status, #filter-role').on('change', function () {
                table.draw();
            });

            // ── Refresh Button ───────────────────────────────────────────────
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



            // ── Delete ───────────────────────────────────────────────────────
            $(document).on('click', '.btn-delete', function () {
                let url = $(this).data('url');

                Swal.fire({
                    title: 'Delete User?',
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
                    if (result.isConfirmed) {
                        $.post(url, {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        })
                        .done(function () {
                            table.ajax.reload(null, false);
                            Swal.fire({ toast: true, position: 'top', showConfirmButton: false, timer: 1500, icon: 'success', title: 'User deleted successfully' });
                        })
                        .fail(function (xhr) {
                            toastr.error('Something went wrong.');
                        });
                    }
                });
            });

        });
    </script>
@endpush



