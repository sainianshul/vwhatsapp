@extends('layouts.app')

@section('title', 'Bots')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'Bots', 'url' => route('bots.index')],
        ['label' => 'All Bots'],
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
                        placeholder="Search bots by name or platform..."
                    />
                </div>

                {{-- Right Controls --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">

                    {{-- Refresh Button --}}
                    <button type="button" class="btn btn-icon btn-light btn-active-light-primary border border-gray-300 w-35px h-35px" id="refresh-table-btn" data-bs-toggle="tooltip" title="Refresh">
                        <i class="ki-outline ki-arrows-circle fs-3"></i>
                    </button>

                    {{-- Platform Filter --}}
                    <div style="width: 155px;">
                        <div class="position-relative">
                            <i class="ki-duotone ki-filter fs-5 text-gray-900 position-absolute top-50 start-0 translate-middle-y ms-4 z-index-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <select
                                id="filter-platform"
                                class="form-select form-select-transparent border border-gray-800 text-gray-900 form-select-sm fw-semibold ps-11 shadow-sm"
                                data-control="select2"
                                data-placeholder="All Platforms"
                                data-allow-clear="true"
                                data-hide-search="true"
                            >
                                <option></option>
                                @foreach (\App\Models\Bot::getPlatformList() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Type Filter --}}
                    <div style="width: 135px;">
                        <div class="position-relative">
                            <i class="ki-duotone ki-filter fs-5 text-gray-900 position-absolute top-50 start-0 translate-middle-y ms-4 z-index-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <select
                                id="filter-type"
                                class="form-select form-select-transparent border border-gray-800 text-gray-900 form-select-sm fw-semibold ps-11 shadow-sm"
                                data-control="select2"
                                data-placeholder="All Types"
                                data-allow-clear="true"
                                data-hide-search="true"
                            >
                                <option></option>
                                @foreach (\App\Models\Bot::getTypeList() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <div style="width: 135px;">
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
                                @foreach (\App\Models\Bot::getStatusList() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Platform Status Filter --}}
                    <div style="width: 185px;">
                        <div class="position-relative">
                            <i class="ki-duotone ki-filter fs-5 text-gray-900 position-absolute top-50 start-0 translate-middle-y ms-4 z-index-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <select
                                id="filter-platform-status"
                                class="form-select form-select-transparent border border-gray-800 text-gray-900 form-select-sm fw-semibold ps-11 shadow-sm"
                                data-control="select2"
                                data-placeholder="All Platform Status"
                                data-allow-clear="true"
                                data-hide-search="true"
                            >
                                <option></option>
                                @foreach (\App\Models\Bot::getPlatformStatusList() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Add Bot --}}
                    <a href="{{ route('bots.create') }}" class="btn btn-sm btn-primary fw-semibold btn-flex btn-center">
                        <i class="ki-outline ki-plus fs-4"></i> Add Bot
                    </a>

                </div>
            </div>
        </div>

        {{-- Table Wrapper --}}
        <div class="card-body py-0">
            <div class="table-responsive">
                
                {{-- Skeleton Loader (Shown initially) --}}
                <div id="table-skeleton">
                    <x-table-skeleton :columns="6" :rows="5" />
                </div>

                {{-- Actual DataTable --}}
                <table class="table align-middle table-row-dashed fs-6 gy-5 d-none" id="bots-table">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">#</th>
                            <th class="min-w-200px">Bot Info</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Platform Status</th>
                            <th>Cookie</th>
                            <th>Last Action</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
            </div>

            @include('layouts.partials._table-empty', [
                'id' => 'bots-empty',
                'title' => 'No Bot Yet',
                'subtitle' => 'Please add a bot to get started.',
                'actionUrl' => route('bots.create'),
                'actionText' => 'Add Bot'
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
    $(document).ready(function() {
        var dt = $('#bots-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('bots.index') }}",
                data: function(d) {
                    d.platform = $('#filter-platform').val();
                    d.type = $('#filter-type').val();
                    d.status = $('#filter-status').val();
                    d.platform_status = $('#filter-platform-status').val();
                },
                error: function(xhr, error, thrown) {
                    console.error("DataTables Error: ", error, thrown);
                    $('#table-skeleton').addClass('d-none');
                    $('#bots-table').removeClass('d-none');
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'bot_info', name: 'name' }, // Also searchable by platform
                { data: 'type_badge', name: 'type' },
                { data: 'status_badge', name: 'status' },
                { data: 'platform_status_badge', name: 'platform_status' },
                { data: 'cookie_status', name: 'cookie_updated_at' },
                { data: 'last_action_at', name: 'last_action_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
            ],
            order: [[1, 'asc']], // Order by name initially
            language: {
                emptyTable: ' ',
                zeroRecords: ' ',
                loadingRecords: ' '
            },
            drawCallback: function() {
                let total = this.api().page.info().recordsDisplay;
                if (total === 0) {
                    $('#bots-table').addClass('d-none');
                    $('#bots-empty').removeClass('d-none');
                } else {
                    $('#bots-empty').addClass('d-none');
                    $('#bots-table').removeClass('d-none');
                }
            },
            initComplete: function(settings, json) {
                $('#table-skeleton').addClass('d-none');
                if (this.api().page.info().recordsDisplay > 0) {
                    $('#bots-table').removeClass('d-none').hide().fadeIn(300);
                }
            }
        });

        // Search
        $('#dt-search').on('keyup', function() {
            dt.search(this.value).draw();
        });

        // Filters
        $('#filter-platform, #filter-type, #filter-status, #filter-platform-status').on('change', function() {
            dt.draw();
        });

        // Refresh
        $('#refresh-table-btn').on('click', function() {
            dt.ajax.reload(null, false); 
        });
    });
</script>
@endpush
