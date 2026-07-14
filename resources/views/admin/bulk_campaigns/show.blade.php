@extends('layouts.app')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'Bulk Campaigns', 'url' => route('admin.bulk_campaigns.index')],
        ['label' => $bulkCampaign->campaign_name],
    ]" />

    <div class="row g-5 g-xl-10 mb-8 mb-xl-10">

        <!-- Total Contacts -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light-primary border border-primary border-dashed shadow-sm hover-elevate-up">
                <div class="card-body d-flex align-items-center p-6">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-primary shadow-sm">
                            <i class="ki-outline ki-people fs-2x text-white"></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-2hx fw-bold text-gray-900 lh-1 mb-1">{{ $bulkCampaign->total_contacts }}</span>
                        <span class="text-primary fw-semibold fs-6">Total Contacts</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sent Successfully -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light-success border border-success border-dashed shadow-sm hover-elevate-up">
                <div class="card-body d-flex align-items-center p-6">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-success shadow-sm">
                            <i class="ki-outline ki-check-circle fs-2x text-white"></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-2hx fw-bold text-gray-900 lh-1 mb-1">{{ $bulkCampaign->sent_count }}</span>
                        <span class="text-success fw-semibold fs-6">Sent Successfully</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Failed -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light-danger border border-danger border-dashed shadow-sm hover-elevate-up">
                <div class="card-body d-flex align-items-center p-6">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-danger shadow-sm">
                            <i class="ki-outline ki-cross-circle fs-2x text-white"></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-2hx fw-bold text-gray-900 lh-1 mb-1">{{ $bulkCampaign->failed_count }}</span>
                        <span class="text-danger fw-semibold fs-6">Failed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign Status -->
        <div class="col-sm-6 col-xl-3">
            @php
                $statusColor = match($bulkCampaign->status) {
                    'completed' => 'success',
                    'failed' => 'danger',
                    'processing' => 'warning',
                    default => 'info'
                };
            @endphp
            <div class="card bg-light-{{ $statusColor }} border border-{{ $statusColor }} border-dashed shadow-sm hover-elevate-up">
                <div class="card-body d-flex align-items-center p-6">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-{{ $statusColor }} shadow-sm">
                            <i class="ki-outline ki-information fs-2x text-white"></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-1 fw-bold text-gray-900 lh-1 mb-1">{{ ucfirst($bulkCampaign->status) }}</span>
                        <span class="text-{{ $statusColor }} fw-semibold fs-6">Campaign Status</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">

        {{-- Toolbar --}}
        <div class="card-header border-0 pt-5 pb-3">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">Message Delivery Report</span>
            </h3>
            <div class="card-toolbar">
                <div class="d-flex align-items-center gap-2">
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

                    {{-- Export Dropdown --}}
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light-primary border border-primary dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ki-outline ki-file-down fs-3"></i> Export CSV
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="{{ route('admin.bulk_campaigns.export', ['bulkCampaign' => $bulkCampaign->id, 'status' => 'all']) }}"><i class="ki-outline ki-document text-primary me-2"></i> All Messages</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.bulk_campaigns.export', ['bulkCampaign' => $bulkCampaign->id, 'status' => 'sent']) }}"><i class="ki-outline ki-check-circle text-success me-2"></i> Sent Only</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.bulk_campaigns.export', ['bulkCampaign' => $bulkCampaign->id, 'status' => 'failed']) }}"><i class="ki-outline ki-cross-circle text-danger me-2"></i> Failed Only</a></li>
                        </ul>
                    </div>

                    {{-- Refresh Button --}}
                    <button type="button" class="btn btn-icon btn-light btn-active-light-primary border border-gray-300 w-35px h-35px" id="refresh-table-btn" data-bs-toggle="tooltip" title="Refresh">
                        <i class="ki-outline ki-arrows-circle fs-3"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body py-4">

            <div id="report-table-wrapper" class="table-responsive">
                <table id="report-table" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 w-100">
                    <thead>
                        <tr class="text-start text-gray-900 fw-medium fs-7 text-uppercase gs-0 border-bottom border-gray-200 border-1">
                            <th class="w-50px">S.No</th>
                            <th class="min-w-150px">Phone Number</th>
                            <th class="min-w-200px">Message Sent</th>
                            <th class="min-w-100px">Status</th>
                            <th class="text-end min-w-150px pe-3">Time</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            @include('layouts.partials._table-skeleton', [
                'id' => 'report-skeleton'
            ])
            @include('layouts.partials._table-empty', [
                'id' => 'report-empty',
                'title' => 'No messages processed',
                'subtitle' => 'No messages have been processed yet. Refresh the page to see updates.',
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

    <!-- Comments Section -->
    <div class="mt-8">
        <x-comments type="App\Models\BulkCampaign" :model-id="$bulkCampaign->id" />
    </div>

@endsection

@push('datatables_css')
    @include('layouts.partials._datatable-cdn-css')
@endpush

@push('datatables_js')
    @include('layouts.partials._datatable-cdn-js')

    <script>
        $(function () {
            let table = $('#report-table').DataTable({
                serverSide: true,
                processing: false,
                ajax: {
                    url: '{{ route('admin.bulk_campaigns.show', $bulkCampaign->id) }}',
                    data: function(d) {
                        d.status = $('#filter-status').val();
                    }
                },
                columns: [
                    { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },
                    { data: 'receiver_number', name: 'receiver_number' },
                    { data: 'message_text', name: 'message_text' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at', className: 'text-end pe-3' },
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
                    $('#report-skeleton').fadeOut(200, function () {
                        $(this).remove();
                    });
                },
                drawCallback: function () {
                    let total = this.api().page.info().recordsDisplay;
                    if (total === 0) {
                        $('#report-table-wrapper').addClass('d-none');
                        $('#report-empty').removeClass('d-none');
                    } else {
                        $('#report-empty').addClass('d-none');
                        $('#report-table-wrapper').removeClass('d-none');
                    }
                    $('[data-bs-toggle="tooltip"]').tooltip({ trigger: 'hover' });
                }
            });

            $('#filter-status').on('change', function() {
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
        });
    </script>
@endpush
