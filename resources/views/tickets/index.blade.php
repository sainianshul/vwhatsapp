@extends('layouts.app')

@section('title', 'Help & Support')

@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Help & Support</h1>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @if(auth()->user()->role !== 'admin')
            <a href="{{ route('tickets.create') }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-outline ki-plus fs-2"></i>Create Ticket
            </a>
            @endif
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card">
            <div class="card-body py-4">
                {{ $dataTable->table(['class' => 'table align-middle table-row-dashed fs-6 gy-5']) }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush
