@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Dashboard Overview
                </h1>
                <x-breadcrumb :items="[
                    ['label' => 'Dashboards'],
                    ['label' => 'Overview'],
                ]" />
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                
                {{-- Active Users --}}
                <div class="col-md-3">
                    <div class="card card-flush h-md-100 border border-gray-300">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ \App\Models\User::count() }}</span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Active Users</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-profile-user fs-3x text-primary"></i>
                        </div>
                    </div>
                </div>

                {{-- Total Logins --}}
                <div class="col-md-3">
                    <div class="card card-flush h-md-100 border border-gray-300">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ \App\Models\LoginHistory::count() }}</span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Logins</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-entrance-left fs-3x text-info"></i>
                        </div>
                    </div>
                </div>

                {{-- System Status --}}
                <div class="col-md-3">
                    <div class="card card-flush h-md-100 border border-gray-300">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">Online</span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">System Status</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-chart-simple-3 fs-3x text-success"></i>
                        </div>
                    </div>
                </div>

                {{-- Trashed Users --}}
                <div class="col-md-3">
                    <div class="card card-flush h-md-100 border border-gray-300">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ \App\Models\User::onlyTrashed()->count() }}</span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Trashed Users</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-trash fs-3x text-danger"></i>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <!--end::Content-->

@endsection
