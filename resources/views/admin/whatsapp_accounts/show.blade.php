@extends('layouts.app')

@section('title', 'Account Details')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'WhatsApp Accounts', 'url' => route('whatsapp_accounts.index')],
        ['label' => 'Account Details'],
    ]" />

    <div class="row g-5 g-xl-10 mt-5">
        <!-- Left Profile Section -->
        <div class="col-xl-4">
            <div class="card shadow-sm border-0 mb-5 mb-xl-8 hover-elevate-up">
                <div class="card-body pt-15">
                    <div class="d-flex flex-center flex-column mb-5">
                        <div class="symbol symbol-100px symbol-circle mb-7 shadow-sm border border-light">
                            @if($account->status === 'connected')
                                <span class="symbol-label bg-light-success text-success fs-1 fw-bolder">
                                    {{ strtoupper(substr($account->name ?? $account->push_name ?? 'W', 0, 1)) }}
                                </span>
                            @else
                                <span class="symbol-label bg-light-danger text-danger fs-1 fw-bolder">
                                    {{ strtoupper(substr($account->name ?? $account->push_name ?? 'W', 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bolder mb-1">
                            {{ $account->name ?? ($account->push_name ?? 'Unknown Account') }}
                        </a>
                        <div class="fs-5 fw-semibold text-muted mb-6">
                            <i class="ki-outline ki-phone fs-4 me-1"></i> {{ $account->phone_number ?? 'No Phone Number' }}
                        </div>
                        
                        @if($account->status === 'connected')
                            <span class="badge badge-light-success border border-success fw-bold px-4 py-3 text-uppercase">Connected</span>
                        @else
                            <span class="badge badge-light-danger border border-danger fw-bold px-4 py-3 text-uppercase">Disconnected</span>
                        @endif
                    </div>
                    
                    <div class="d-flex flex-stack fs-4 py-3">
                        <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">Details
                            <span class="ms-2 rotate-180">
                                <i class="ki-outline ki-down fs-3"></i>
                            </span>
                        </div>
                        <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Edit account details">
                            <a href="{{ route('whatsapp_accounts.edit', $account->id) }}" class="btn btn-sm btn-light-primary fw-bold">Edit</a>
                        </span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>

                    <div id="kt_user_view_details" class="collapse show">
                        <div class="pb-5 fs-6">
                            <div class="fw-bold mt-5">Session ID</div>
                            <div class="text-gray-600 badge badge-light border border-gray-300 fs-8 fw-semibold mt-1">{{ $account->session_id }}</div>

                            <div class="fw-bold mt-5">WhatsApp Push Name</div>
                            <div class="text-gray-600">{{ $account->push_name ?? 'N/A' }}</div>
                            
                            <div class="fw-bold mt-5">Added On</div>
                            <div class="text-gray-600">{{ $account->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right Stats & Details Section -->
        <div class="col-xl-8">
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-md-6">
                    <div class="card card-flush bg-light-primary border border-primary border-dashed shadow-sm hover-elevate-up">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ number_format($stats['total_messages']) }}</span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Messages Sent</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-send fs-3x text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-flush bg-light-info border border-info border-dashed shadow-sm hover-elevate-up">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ number_format($stats['total_campaigns']) }}</span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Bulk Campaigns</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-rocket fs-3x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="mt-8">
                <x-comments type="App\Models\WhatsAppAccount" :model-id="$account->id" />
            </div>
        </div>
    </div>

@endsection
