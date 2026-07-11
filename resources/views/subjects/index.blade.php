@extends('layouts.app')

@section('title', 'Profiles')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'Profiles'],
    ]" />

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-5 pb-3">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">Intelligence Profiles</span>
                <span class="text-muted mt-1 fw-semibold fs-7">Manage tracked people, competitors, and watchlists.</span>
            </h3>
            
            <div class="card-toolbar">
                <div class="d-flex align-items-center gap-3">
                    
                    {{-- Search --}}
                    <div class="position-relative w-md-250px">
                        <i class="ki-outline ki-magnifier fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-4"></i>
                        <input type="text" id="dt-search" class="form-control form-control-sm form-control-solid ps-12 shadow-sm" placeholder="Search profiles..." />
                    </div>

                    {{-- Status Filter --}}
                    <div style="width: 150px;">
                        <div class="position-relative">
                            <i class="ki-duotone ki-filter fs-5 text-gray-900 position-absolute top-50 start-0 translate-middle-y ms-4 z-index-3">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            <select id="filter-status" class="form-select form-select-transparent border border-gray-800 text-gray-900 form-select-sm fw-semibold ps-11 shadow-sm" data-control="select2" data-placeholder="Status" data-allow-clear="true" data-hide-search="true">
                                <option></option>
                                @foreach (\App\Models\Subject::getStatusList() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Add Button --}}
                    <a href="{{ route('subjects.create') }}" class="btn btn-sm btn-primary fw-semibold btn-flex btn-center">
                        <i class="ki-outline ki-plus fs-4"></i> Add Profile
                    </a>

                </div>
            </div>
        </div>

        <div class="card-body py-0">
            <div class="table-responsive">
                
                {{-- Skeleton Loader --}}
                <div id="table-skeleton">
                    <x-table-skeleton :columns="5" :rows="5" />
                </div>

                {{-- Actual DataTable --}}
                <table class="table align-middle table-row-dashed fs-6 gy-5 d-none" id="subjects-table">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">#</th>
                            <th class="min-w-250px">Profile Info</th>
                            <th class="min-w-150px">Tracking Stats</th>
                            <th>Status</th>
                            <th>Added On</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
            </div>

            @include('layouts.partials._table-empty', [
                'id' => 'subjects-empty',
                'title' => 'No Profiles Yet',
                'subtitle' => 'Start by adding a person or entity to track.',
                'actionUrl' => route('subjects.create'),
                'actionText' => 'Add Profile'
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
        var dt = $('#subjects-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('subjects.index') }}",
                data: function(d) {
                    d.status = $('#filter-status').val();
                },
                error: function(xhr, error, thrown) {
                    $('#table-skeleton').addClass('d-none');
                    $('#subjects-table').removeClass('d-none');
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'profile_info', name: 'name' },
                { data: 'stats', name: 'stats', orderable: false, searchable: false },
                { data: 'status_badge', name: 'status' },
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
            ],
            order: [[4, 'desc']], // Order by created_at desc
            language: {
                emptyTable: ' ',
                zeroRecords: ' ',
                loadingRecords: ' '
            },
            drawCallback: function() {
                let total = this.api().page.info().recordsDisplay;
                if (total === 0) {
                    $('#subjects-table').addClass('d-none');
                    $('#subjects-empty').removeClass('d-none');
                } else {
                    $('#subjects-empty').addClass('d-none');
                    $('#subjects-table').removeClass('d-none');
                }
            },
            initComplete: function(settings, json) {
                $('#table-skeleton').addClass('d-none');
                if (this.api().page.info().recordsDisplay > 0) {
                    $('#subjects-table').removeClass('d-none').hide().fadeIn(300);
                }
            }
        });

        // Search
        $('#dt-search').on('keyup', function() {
            dt.search(this.value).draw();
        });

        // Filters
        $('#filter-status').on('change', function() {
            dt.draw();
        });
    });
</script>
@endpush
