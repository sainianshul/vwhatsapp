@extends('layouts.app')

@section('title', 'Trashed Profiles')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <x-page-header title="Trash" description="Deleted intelligence profiles" />
                <x-breadcrumb :items="[
                    ['label' => 'Profiles', 'url' => route('subjects.index')],
                    ['label' => 'Trash'],
                ]" />
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('subjects.index') }}" class="btn btn-sm btn-light fw-semibold">
                    <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <x-alert-success />

            <div class="card shadow-sm">
                <div class="card-header border-0 pt-5 pb-3">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Deleted Profiles</span>
                    </h3>
                </div>

                <div class="card-body py-0">
                    <div class="table-responsive">
                        
                        <div id="table-skeleton">
                            <x-table-skeleton :columns="5" :rows="3" />
                        </div>

                        <table class="table align-middle table-row-dashed fs-6 gy-5 d-none" id="trash-table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="w-10px pe-2">#</th>
                                    <th class="min-w-250px">Profile Info</th>
                                    <th class="min-w-150px">Tracking Stats</th>
                                    <th>Status</th>
                                    <th>Deleted On</th>
                                    <th class="text-end min-w-100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                            </tbody>
                        </table>
                    </div>

                    @include('layouts.partials._table-empty', [
                        'id' => 'trash-empty',
                        'title' => 'Trash is Empty',
                        'subtitle' => 'No deleted profiles found.',
                    ])
                    
                </div>
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
    $(document).ready(function() {
        var dt = $('#trash-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('subjects.trash') }}",
                error: function() {
                    $('#table-skeleton').addClass('d-none');
                    $('#trash-table').removeClass('d-none');
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'profile_info', name: 'name' },
                { data: 'stats', name: 'stats', orderable: false, searchable: false },
                { data: 'status_badge', name: 'status' },
                { data: 'deleted_at_formatted', name: 'deleted_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
            ],
            order: [[4, 'desc']],
            language: {
                emptyTable: ' ',
                zeroRecords: ' ',
                loadingRecords: ' '
            },
            drawCallback: function() {
                let total = this.api().page.info().recordsDisplay;
                if (total === 0) {
                    $('#trash-table').addClass('d-none');
                    $('#trash-empty').removeClass('d-none');
                } else {
                    $('#trash-empty').addClass('d-none');
                    $('#trash-table').removeClass('d-none');
                }
            },
            initComplete: function(settings, json) {
                $('#table-skeleton').addClass('d-none');
                if (this.api().page.info().recordsDisplay > 0) {
                    $('#trash-table').removeClass('d-none').hide().fadeIn(300);
                }
            }
        });
    });
</script>
@endpush
