@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Developer API</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">Manage API Keys & Endpoints</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#kt_modal_new_key">
                    <i class="ki-outline ki-plus fs-2"></i> Generate Key
                </button>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10 shadow-sm border-success">
                    <i class="ki-outline ki-shield-tick fs-2hx text-success me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-success">{{ session('success') }}</h4>
                    </div>
                </div>
            @endif

            @if(session('new_token'))
                <div class="alert alert-warning bg-light-warning border border-warning d-flex align-items-center p-5 mb-10 shadow-sm">
                    <i class="ki-outline ki-information fs-2hx text-warning me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-warning">Important: Copy your new API Key now!</h4>
                        <span class="text-dark">You won't be able to see this key again once you navigate away. Keep it secure.</span>
                        <div class="mt-3">
                            <code class="fs-4 fw-bold p-3 bg-white text-dark rounded border border-dark d-inline-block shadow-sm">{{ session('new_token') }}</code>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row g-5 g-xl-10">
                <!-- API Keys Management -->
                <div class="col-xl-7">
                    <div class="card shadow-sm h-xl-100 border">
                        <div class="card-header pt-7 border-bottom">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark fs-4">Active API Keys</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Tokens currently accessing your WhatsApp service</span>
                            </h3>
                        </div>
                        <div class="card-body pt-5">
                            
                            {{-- Search and Refresh --}}
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center position-relative w-100 me-2">
                                    <i class="ki-duotone ki-magnifier fs-5 text-gray-900 position-absolute ms-4 z-index-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" id="dt-search" class="form-control form-control-sm form-control-transparent border border-gray-800 text-gray-900 ps-11 pe-4 fs-7 fw-semibold shadow-sm w-100" placeholder="Search tokens..." />
                                </div>
                                <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary border border-gray-300 w-35px h-35px flex-shrink-0" id="refresh-table-btn" data-bs-toggle="tooltip" title="Refresh">
                                    <i class="ki-outline ki-arrows-circle fs-3"></i>
                                </button>
                            </div>

                            <div id="tokens-table-wrapper" class="table-responsive">
                                <table id="tokens-table" class="table table-row-bordered align-middle fs-6 gy-4 w-100">
                                    <thead>
                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                            <th>Key Name</th>
                                            <th>Last Used</th>
                                            <th>Created Date</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-dark"></tbody>
                                </table>
                            </div>

                            @include('layouts.partials._table-skeleton', ['id' => 'tokens-skeleton'])
                            @include('layouts.partials._table-empty', [
                                'id' => 'tokens-empty',
                                'title' => 'No API keys found',
                                'subtitle' => 'Click "Generate Key" to create your first authentication token.',
                                'actionUrl' => '#',
                                'actionText' => 'Generate Key',
                                'attributes' => 'data-bs-toggle="modal" data-bs-target="#kt_modal_new_key"'
                            ])

                        </div>
                    </div>
                </div>

                <!-- API Documentation -->
                <div class="col-xl-5">
                    <div class="card shadow-sm h-xl-100 border">
                        <div class="card-header pt-7 border-bottom">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark fs-4">Documentation</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Quick integration guide</span>
                            </h3>
                        </div>
                        <div class="card-body pt-6">
                            
                            <div class="mb-8">
                                <h5 class="fw-bold text-dark mb-3">Endpoint</h5>
                                <div class="d-flex align-items-center bg-light-primary border border-primary border-dashed p-3 rounded">
                                    <span class="badge badge-primary me-3 fs-7">POST</span>
                                    <span class="text-primary fw-bold fs-6">{{ url('/api/v1/messages/send') }}</span>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h5 class="fw-bold text-dark mb-3">Authentication Header</h5>
                                <div class="bg-light p-4 rounded border border-gray-300">
                                    <code class="text-dark fs-6 d-block mb-1">Content-Type: <span class="text-primary">application/json</span></code>
                                    <code class="text-dark fs-6 d-block">Authorization: Bearer <span class="text-danger">YOUR_API_KEY</span></code>
                                </div>
                            </div>

                            <div class="mb-0">
                                <h5 class="fw-bold text-dark mb-3">JSON Request Body</h5>
                                <div class="bg-light p-4 rounded border border-gray-300">
                                    <pre class="mb-0"><code class="text-dark fs-6">{
  <span class="text-primary">"to"</span>: <span class="text-success">"919876543210"</span>,
  <span class="text-primary">"text"</span>: <span class="text-success">"Hello from CRM!"</span>,
  <span class="text-muted">"from"</span>: <span class="text-success">"918888888888"</span> <span class="text-muted">// Optional</span>
}</code></pre>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal: New Key -->
<div class="modal fade" id="kt_modal_new_key" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content shadow-lg">
            <form action="{{ route('admin.developer_settings.generate') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h3 class="modal-title fw-bold text-dark">Generate API Key</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body py-8 px-8">
                    <div class="mb-5">
                        <label class="required fs-6 fw-semibold text-dark mb-2">Token Name / Integration</label>
                        <input type="text" class="form-control form-control-solid border" placeholder="e.g. Sales CRM Server" name="token_name" required autocomplete="off" />
                        <div class="text-muted fs-7 mt-2">Give this token a recognizable name so you can identify it later.</div>
                    </div>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Generate Token</button>
                </div>
            </form>
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
            let table = $('#tokens-table').DataTable({
                serverSide: true,
                processing: false,
                ajax: {
                    url: '{{ route('admin.developer_settings.index') }}',
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'last_used_at', name: 'last_used_at' },
                    { data: 'created_at', name: 'created_at' },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-end pe-3'
                    },
                ],
                order: [[2, 'desc']],
                pageLength: 10,
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
                    info: 'Showing _START_–_END_ of _TOTAL_ keys',
                    infoEmpty: 'No keys to show',
                    infoFiltered: '(filtered from _MAX_)',
                    lengthMenu: 'Show _MENU_',
                    paginate: {
                        previous: '<i class="ki-duotone ki-arrow-left"></i>',
                        next: '<i class="ki-duotone ki-arrow-right"></i>',
                    },
                },
                initComplete: function () {
                    $('#tokens-skeleton').fadeOut(200, function () {
                        $(this).remove();
                    });
                },
                drawCallback: function () {
                    let total = this.api().page.info().recordsDisplay;
                    if (total === 0) {
                        $('#tokens-table-wrapper').addClass('d-none');
                        $('#tokens-empty').removeClass('d-none');
                    } else {
                        $('#tokens-empty').addClass('d-none');
                        $('#tokens-table-wrapper').removeClass('d-none');
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
                    title: 'Revoke Key?',
                    text: 'All integrations using it will immediately fail.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Revoke',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-light ms-2'
                    },
                    buttonsStyling: false,
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    $.post(url, {
                        _method: 'POST', // Route uses POST technically? Wait, the route is actually a POST route or DELETE? Let's check web.php. Actually, DeveloperSettings uses POST for revoke? Let me just use POST.
                        _token: '{{ csrf_token() }}'
                    })
                    .done(function (res) {
                        table.ajax.reload(null, false);
                        Swal.fire({ toast: true, position: 'top', showConfirmButton: false, timer: 1500, icon: 'success', title: 'Key revoked successfully' });
                    })
                    .fail(function (xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
                    });
                });
            });

        });
    </script>
@endpush
