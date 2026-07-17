@extends('layouts.app')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'Message Logs', 'url' => route('whatsapp_messages.index')],
        ['label' => 'Quick Send Logs'],
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
                        placeholder="Search receiver or message..."
                    />
                </div>

                {{-- Right Controls --}}
                <div class="d-flex align-items-center gap-2">

                    {{-- Date Filters --}}
                    <div style="width: 130px;">
                        <input type="date" id="filter-from-date" class="form-control form-control-transparent border border-gray-800 text-gray-900 form-control-sm fw-semibold shadow-sm" title="From Date" />
                    </div>
                    <div style="width: 130px;">
                        <input type="date" id="filter-to-date" class="form-control form-control-transparent border border-gray-800 text-gray-900 form-control-sm fw-semibold shadow-sm" title="To Date" />
                    </div>

                    {{-- Account Filter --}}
                    <div style="width: 145px;">
                        <div class="position-relative">
                            <i class="ki-duotone ki-filter fs-5 text-gray-900 position-absolute top-50 start-0 translate-middle-y ms-4 z-index-3">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            <select id="filter-account" class="form-select form-select-transparent border border-gray-800 text-gray-900 form-select-sm fw-semibold ps-11 shadow-sm" data-control="select2" data-placeholder="All Accounts" data-allow-clear="true" data-hide-search="true">
                                <option></option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->phone_number }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <div style="width: 145px;">
                        <div class="position-relative">
                            <i class="ki-duotone ki-filter fs-5 text-gray-900 position-absolute top-50 start-0 translate-middle-y ms-4 z-index-3">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            <select id="filter-status" class="form-select form-select-transparent border border-gray-800 text-gray-900 form-select-sm fw-semibold ps-11 shadow-sm" data-control="select2" data-placeholder="All Status" data-allow-clear="true" data-hide-search="true">
                                <option></option>
                                <option value="sent">Sent</option>
                                <option value="failed">Failed</option>
                                <option value="queued">Queued</option>
                            </select>
                        </div>
                    </div>

                    {{-- Refresh Button --}}
                    <button type="button" class="btn btn-icon btn-light btn-active-light-primary border border-gray-300 w-35px h-35px" id="refresh-table-btn" data-bs-toggle="tooltip" title="Refresh">
                        <i class="ki-outline ki-arrows-circle fs-3"></i>
                    </button>

                    {{-- Quick Send Button --}}
                    <a href="{{ route('whatsapp_messages.create') }}" class="btn btn-sm btn-primary fw-semibold btn-flex btn-center">
                        <i class="ki-duotone ki-plus-square fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Quick Send
                    </a>

                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body py-4">

            <div id="messages-table-wrapper" class="table-responsive">
                <table id="messages-table" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 w-100">
                    <thead>
                        <tr class="text-start text-gray-900 fw-medium fs-7 text-uppercase gs-0 border-bottom border-gray-200 border-1">
                            <th class="w-50px">S.No</th>
                            <th class="min-w-150px">Receiver</th>
                            <th class="min-w-200px">Message</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-100px">Source</th>
                            <th class="min-w-150px">Account</th>
                            <th class="min-w-150px">Date</th>
                            <th class="text-end min-w-100px pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            @include('layouts.partials._table-skeleton', [
                'id' => 'messages-skeleton'
            ])
            @include('layouts.partials._table-empty', [
                'id' => 'messages-empty',
                'title' => 'No messages found',
                'subtitle' => 'You have not sent any quick messages yet.',
                'actionUrl' => route('whatsapp_messages.create'),
                'actionText' => 'Send Message'
            ])

        </div>
    </div>

    <!-- Read More Modal -->
    <div class="modal fade" tabindex="-1" id="message-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-sm border border-gray-300">
                <div class="modal-header border-bottom border-gray-200">
                    <h3 class="modal-title text-gray-900 fw-bold">Message Content</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body p-6">
                    <div id="modal-message-text" class="text-gray-800 fs-6 fw-medium" style="white-space: pre-wrap; word-break: break-word;"></div>
                </div>
                <div class="modal-footer border-top border-gray-200 p-4">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Log Modal -->
    <div class="modal fade" tabindex="-1" id="error-modal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-sm border border-danger">
                <div class="modal-header border-bottom border-gray-200 bg-light-danger">
                    <h3 class="modal-title text-danger fw-bold"><i class="ki-outline ki-information-5 text-danger fs-2 me-2"></i>Error Details</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-danger ms-2" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body p-6">
                    <div class="border border-danger border-dashed rounded p-4 bg-light-danger">
                        <div id="modal-error-text" class="text-danger fs-6 fw-semibold font-monospace" style="white-space: pre-wrap; word-break: break-word;"></div>
                    </div>
                </div>
                <div class="modal-footer border-top border-gray-200 p-4">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Close</button>
                </div>
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
        $(function () {
            let table = $('#messages-table').DataTable({
                serverSide: true,
                processing: false,
                ajax: {
                    url: '{{ route('whatsapp_messages.index') }}',
                    data: function(d) {
                        d.status = $('#filter-status').val();
                        d.account_id = $('#filter-account').val();
                        d.from_date = $('#filter-from-date').val();
                        d.to_date = $('#filter-to-date').val();
                    }
                },
                columns: [
                    { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },
                    { data: 'receiver_number', name: 'receiver_number' },
                    { data: 'message_text', name: 'message_text' },
                    { data: 'status', name: 'status' },
                    { data: 'source', name: 'source' },
                    { data: 'whatsapp_account', name: 'whatsappAccount.phone_number', orderable: false },
                    { data: 'created_at', name: 'created_at' },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-end pe-3'
                    },
                ],
                order: [[6, 'desc']],
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
                    info: 'Showing _START_–_END_ of _TOTAL_ messages',
                    infoEmpty: 'No messages to show',
                    infoFiltered: '(filtered from _MAX_)',
                    lengthMenu: 'Show _MENU_',
                    paginate: {
                        previous: '<i class="ki-duotone ki-arrow-left"></i>',
                        next: '<i class="ki-duotone ki-arrow-right"></i>',
                    },
                },
                initComplete: function () {
                    $('#messages-skeleton').fadeOut(200, function () {
                        $(this).remove();
                    });
                },
                drawCallback: function () {
                    let total = this.api().page.info().recordsDisplay;
                    if (total === 0) {
                        $('#messages-table-wrapper').addClass('d-none');
                        $('#messages-empty').removeClass('d-none');
                    } else {
                        $('#messages-empty').addClass('d-none');
                        $('#messages-table-wrapper').removeClass('d-none');
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

            $('#filter-status, #filter-account, #filter-from-date, #filter-to-date').on('change', function() {
                table.draw();
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

            $(document).on('click', '.view-message-btn', function() {
                $('#modal-message-text').text($(this).data('message'));
                $('#message-modal').modal('show');
            });

            $(document).on('click', '.view-error-btn', function() {
                let errorData = $(this).data('error');
                $('#modal-error-text').text(errorData);
                $('#error-modal').modal('show');
            });

            $(document).on('click', '.resend-message-btn', function () {
                let url = $(this).data('url');
                
                $.post(url, {
                    _token: '{{ csrf_token() }}'
                })
                .done(function (res) {
                    table.ajax.reload(null, false);
                    Swal.fire({ toast: true, position: 'top', showConfirmButton: false, timer: 1500, icon: 'success', title: res.message || 'Message queued for resending' });
                })
                .fail(function (xhr) {
                    Swal.fire({ toast: true, position: 'top', showConfirmButton: false, timer: 3000, icon: 'error', title: xhr.responseJSON?.message || 'Something went wrong.' });
                });
            });

            $(document).on('click', '.btn-delete', function () {
                let url = $(this).data('url');
                Swal.fire({
                    title: 'Delete Log?',
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
                        Swal.fire({ toast: true, position: 'top', showConfirmButton: false, timer: 1500, icon: 'success', title: 'Log deleted successfully' });
                    })
                    .fail(function (xhr) {
                        Swal.fire({ toast: true, position: 'top', showConfirmButton: false, timer: 3000, icon: 'error', title: xhr.responseJSON?.message || 'Something went wrong.' });
                    });
                });
            });

        });
    </script>
@endpush
