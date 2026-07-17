@extends('layouts.app')

@section('title', 'Account Details')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'WhatsApp Accounts', 'url' => route('whatsapp_accounts.index')],
        ['label' => 'Account Details'],
    ]" />

    <div class="row g-5 g-xl-10 mt-5">
        <div class="col-12">
            <!-- Navbar -->
            <div class="card mb-5 mb-xl-10 shadow-sm border-0">
                <div class="card-body pt-9 pb-0">
                    <!-- Details -->
                    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                        <!-- Pic -->
                        <div class="me-7 mb-4">
                            <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative border border-light shadow-sm">
                                @if($account->status === 'connected')
                                    <span class="symbol-label bg-light-success text-success fs-2tx fw-bolder">
                                        {{ strtoupper(substr($account->name ?? $account->push_name ?? 'W', 0, 1)) }}
                                    </span>
                                    <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px" title="Connected"></div>
                                @else
                                    <span class="symbol-label bg-light-danger text-danger fs-2tx fw-bolder">
                                        {{ strtoupper(substr($account->name ?? $account->push_name ?? 'W', 0, 1)) }}
                                    </span>
                                    <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-danger rounded-circle border border-4 border-white h-20px w-20px" title="Disconnected"></div>
                                @endif
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <div class="d-flex flex-column">
                                    <!-- Name -->
                                    <div class="d-flex align-items-center mb-2">
                                        <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bolder me-1">
                                            {{ $account->name ?? ($account->push_name ?? 'Unknown Account') }}
                                        </a>
                                        @if($account->status === 'connected')
                                            <span class="badge badge-light-success ms-2">Connected</span>
                                        @else
                                            <span class="badge badge-light-danger ms-2">Disconnected</span>
                                        @endif
                                    </div>
                                    <!-- Info -->
                                    <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                        <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                            <i class="ki-outline ki-phone fs-4 me-1"></i> {{ $account->phone_number ?? 'No Phone Number' }}
                                        </a>
                                        <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                            <i class="ki-outline ki-fingerprint-scan fs-4 me-1"></i> Session ID: {{ $account->session_id }}
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="d-flex my-4">
                                    <a href="{{ route('whatsapp_accounts.edit', $account->id) }}" class="btn btn-sm btn-light-primary fw-bold">Edit Account</a>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="d-flex flex-wrap flex-stack mt-4">
                                <div class="d-flex flex-column flex-grow-1 pe-8">
                                    <div class="d-flex flex-wrap">
                                        <div class="border border-gray-300 rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ki-outline ki-send fs-3 text-primary me-2"></i>
                                                <div class="fs-2 fw-bold text-gray-900">{{ number_format($stats['total_messages']) }}</div>
                                            </div>
                                            <div class="fw-semibold fs-7 text-gray-500 text-uppercase">Messages Sent</div>
                                        </div>

                                        <div class="border border-gray-300 rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ki-outline ki-rocket fs-3 text-info me-2"></i>
                                                <div class="fs-2 fw-bold text-gray-900">{{ number_format($stats['total_campaigns']) }}</div>
                                            </div>
                                            <div class="fw-semibold fs-7 text-gray-500 text-uppercase">Bulk Campaigns</div>
                                        </div>

                                        <div class="border border-gray-300 rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ki-outline ki-calendar-8 fs-3 text-muted me-2"></i>
                                                <div class="fs-3 fw-bold text-gray-900">{{ $account->created_at->format('d M, Y') }}</div>
                                            </div>
                                            <div class="fw-semibold fs-7 text-gray-500 text-uppercase">Added On</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold mt-5" role="tablist">
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4 active" data-bs-toggle="tab" href="#tab_overview" role="tab">
                                <i class="ki-outline ki-setting-2 fs-5 me-2"></i>Overview
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#tab_message_logs" role="tab">
                                <i class="ki-outline ki-message-text-2 fs-5 me-2"></i>Message Logs
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#tab_campaigns" role="tab">
                                <i class="ki-outline ki-rocket fs-5 me-2"></i>Bulk Campaigns
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="tab_overview" role="tabpanel">
                    <div class="row g-5 g-xl-8">
                        <div class="col-xl-6">
                            <div class="card shadow-sm border-0">
                                <div class="card-header border-0 pt-6">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-4 mb-1 text-gray-900">Account Details</span>
                                    </h3>
                                </div>
                                <div class="card-body pt-4 pb-8 fs-6">
                                    <div class="fw-bold mt-5">WhatsApp Push Name</div>
                                    <div class="text-gray-600">{{ $account->push_name ?? 'N/A' }}</div>
                                    
                                    <div class="fw-bold mt-5">Session ID</div>
                                    <div class="text-gray-600 font-monospace">{{ $account->session_id }}</div>
                                    
                                    <div class="fw-bold mt-5">System Added Date</div>
                                    <div class="text-gray-600">{{ $account->created_at->format('d M Y, h:i A') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <x-comments type="App\Models\WhatsAppAccount" :model-id="$account->id" />
                        </div>
                    </div>
                </div>

                <!-- Message Logs Tab -->
                <div class="tab-pane fade" id="tab_message_logs" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-4 mb-1 text-gray-900">Message Logs</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-8">All individual messages sent from this account</span>
                            </h3>
                        </div>
                        <div class="card-body pt-4">
                            <div class="table-responsive">
                                <table id="messages-table" class="table align-middle table-row-dashed fs-6 gy-5 w-100">
                                    <thead>
                                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                            <th>#</th>
                                            <th>Receiver Number</th>
                                            <th>Message</th>
                                            <th>Status</th>
                                            <th>Source</th>
                                            <th>Sent At</th>
                                            <th class="text-end min-w-100px">Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaigns Tab -->
                <div class="tab-pane fade" id="tab_campaigns" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-4 mb-1 text-gray-900">Bulk Campaigns</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-8">Campaigns executed by this account</span>
                            </h3>
                        </div>
                        <div class="card-body pt-4">
                            <div class="table-responsive">
                                <table id="campaigns-table" class="table align-middle table-row-dashed fs-6 gy-5 w-100">
                                    <thead>
                                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                            <th>#</th>
                                            <th>Campaign Name</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th class="text-end min-w-100px">Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal for Messages -->
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

    <!-- Message Text Modal -->
    <div class="modal fade" tabindex="-1" id="message-modal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-sm">
                <div class="modal-header border-bottom border-gray-200">
                    <h3 class="modal-title fw-bold">Message Content</h3>
                    <div class="btn btn-icon btn-sm btn-active-light ms-2" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body p-6">
                    <div id="modal-message-text" class="fs-6 fw-semibold text-gray-800" style="white-space: pre-wrap; word-break: break-word;"></div>
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
            // -- Message Logs DataTable Initialization --
            let messagesTable = $('#messages-table').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: '{{ route('whatsapp_messages.index') }}',
                    data: function(d) {
                        d.account_id = '{{ $account->id }}';
                    }
                },
                columns: [
                    { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },
                    { data: 'receiver_number', name: 'receiver_number' },
                    { data: 'message_text', name: 'message_text' },
                    { data: 'status', name: 'status' },
                    { data: 'source', name: 'source' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ],
                order: [[5, 'desc']],
                pageLength: 10,
                dom:
                    "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    paginate: {
                        previous: '<i class="ki-duotone ki-arrow-left"></i>',
                        next: '<i class="ki-duotone ki-arrow-right"></i>',
                    },
                },
                drawCallback: function () {
                    $('[data-bs-toggle="tooltip"]').tooltip({ trigger: 'hover' });
                }
            });

            // Handle Read More Message
            $(document).on('click', '.view-message-btn', function() {
                $('#modal-message-text').text($(this).data('message'));
                $('#message-modal').modal('show');
            });

            // Handle Error Log View
            $(document).on('click', '.view-error-btn', function() {
                $('#modal-error-text').text($(this).data('error'));
                $('#error-modal').modal('show');
            });


            // -- Bulk Campaigns DataTable Initialization --
            let campaignsTable = $('#campaigns-table').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: '{{ route('admin.bulk_campaigns.index') }}',
                    data: function(d) {
                        d.account_id = '{{ $account->id }}';
                    }
                },
                columns: [
                    { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },
                    { data: 'campaign_name', name: 'campaign_name' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ],
                order: [[3, 'desc']],
                pageLength: 10,
                dom:
                    "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    paginate: {
                        previous: '<i class="ki-duotone ki-arrow-left"></i>',
                        next: '<i class="ki-duotone ki-arrow-right"></i>',
                    },
                }
            });
            
            // Re-adjust columns when tabs are shown (fixes DataTables header alignment bug in hidden tabs)
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            });
        });
    </script>
@endpush
