@extends('layouts.app')

@section('title', 'Profile Details')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <x-page-header title="Profile Details" description="View intelligence and tracking data" />
                <x-breadcrumb :items="[
                    ['label' => 'Profiles', 'url' => route('subjects.index')],
                    ['label' => $subject->name],
                ]" />
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('subjects.index') }}" class="btn btn-sm btn-light fw-semibold">
                    <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Back
                </a>
                <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-sm btn-light-primary border border-primary fw-bold shadow-sm">
                    <i class="ki-outline ki-pencil fs-4 me-1"></i>Edit Profile
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            <x-alert-success />
            <x-form-errors />

            <!--begin::Navbar-->
            <div class="card card-bordered border-gray-300 mb-5 mb-xl-10 shadow-sm">
                <div class="card-body pt-9 pb-0">
                    <!--begin::Details-->
                    <div class="d-flex flex-wrap flex-sm-nowrap">
                        <!--begin: Pic-->
                        <div class="me-7 mb-4">
                            <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative border border-gray-300 rounded overflow-hidden">
                                @if($subject->photo_url)
                                    <img src="{{ Storage::url($subject->photo_url) }}" alt="image" style="object-fit:cover;" />
                                @else
                                    <span class="symbol-label bg-light-primary text-primary fs-2x fw-bold">
                                        {{ strtoupper(substr($subject->name, 0, 1)) }}
                                    </span>
                                @endif
                                <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-{{ $subject->status_color }} rounded-circle border border-4 border-body h-20px w-20px"></div>
                            </div>
                        </div>
                        <!--end::Pic-->

                        <!--begin::Info-->
                        <div class="flex-grow-1">
                            <!--begin::Title-->
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <!--begin::User-->
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-2">
                                        <h1 class="text-gray-900 fs-1 fw-bold me-2">{{ $subject->name }}</h1>
                                        <span class="badge badge-light-{{ $subject->status_color }} border border-{{ $subject->status_color }} fw-semibold px-3 py-1 me-2">
                                            {{ ucfirst($subject->status) }}
                                        </span>
                                    </div>

                                    <div class="d-flex flex-wrap fw-medium fs-6 mb-4 pe-2 gap-5">
                                        @if($subject->designation)
                                            <span class="d-flex align-items-center text-gray-600">
                                                <i class="ki-outline ki-briefcase fs-4 me-2 text-gray-500"></i>
                                                {{ $subject->designation }}
                                            </span>
                                        @endif
                                        <span class="d-flex align-items-center text-gray-600">
                                            <i class="ki-outline ki-user fs-4 me-2 text-gray-500"></i>
                                            Created by {{ $subject->creator->name ?? 'System' }}
                                        </span>
                                    </div>
                                </div>
                                <!--end::User-->

                            </div>
                            <!--end::Title-->

                            <!--begin::Stats-->
                            <div class="d-flex flex-wrap flex-stack mt-4">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-column flex-grow-1 pe-8">
                                    <div class="d-flex flex-wrap">
                                        
                                        <div class="border border-gray-300 rounded min-w-125px py-2 px-4 me-4 mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="ki-outline ki-profile-user fs-3 text-primary me-2"></i>
                                                <div class="fs-4 fw-bold text-gray-900">{{ $subject->accounts_count }}</div>
                                            </div>
                                            <div class="fw-medium fs-8 text-gray-600 text-uppercase">Social Accounts</div>
                                        </div>

                                        <div class="border border-gray-300 rounded min-w-125px py-2 px-4 me-4 mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="ki-outline ki-message-text-2 fs-3 text-success me-2"></i>
                                                <div class="fs-4 fw-bold text-gray-900">{{ $subject->total_posts_count }}</div>
                                            </div>
                                            <div class="fw-medium fs-8 text-gray-600 text-uppercase">Total Posts Scraped</div>
                                        </div>

                                        <div class="border border-gray-300 rounded min-w-125px py-2 px-4 me-4 mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="ki-outline ki-time fs-3 text-warning me-2"></i>
                                                <div class="fs-5 fw-bold text-gray-900">
                                                    {{ $subject->created_at->format('d M, Y') }}
                                                </div>
                                            </div>
                                            <div class="fw-medium fs-8 text-gray-600 text-uppercase">Tracking Since</div>
                                        </div>

                                    </div>
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Stats-->
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Details-->

                    <!--begin::Navs-->
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-6 fw-semibold mt-6" role="tablist">
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4 active" data-bs-toggle="tab" href="#tab_home" role="tab">
                                <i class="ki-outline ki-home fs-5 me-2"></i>Overview
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#tab_accounts" role="tab">
                                <i class="ki-outline ki-profile-user fs-5 me-2"></i>Linked Accounts ({{ $subject->accounts_count }})
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#tab_posts" role="tab">
                                <i class="ki-outline ki-message-text-2 fs-5 me-2"></i>Posts Data
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#tab_notes" role="tab">
                                <i class="ki-outline ki-notepad fs-5 me-2"></i>Internal Notes
                            </a>
                        </li>
                    </ul>
                    <!--begin::Navs-->
                </div>
            </div>
            <!--end::Navbar-->

            <!--begin::Tab Content-->
            <div class="tab-content" id="subjectTabContent">
                
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="tab_home" role="tabpanel">
                    @include('subjects.tabs.home')
                </div>

                <!-- Accounts Tab -->
                <div class="tab-pane fade" id="tab_accounts" role="tabpanel">
                    @include('subjects.tabs.accounts')
                </div>

                <!-- Posts Tab -->
                <div class="tab-pane fade" id="tab_posts" role="tabpanel">
                    @include('subjects.tabs.posts')
                </div>

                <!-- Notes Tab -->
                <div class="tab-pane fade" id="tab_notes" role="tabpanel">
                    @include('subjects.tabs.notes')
                </div>

            </div>
            <!--end::Tab Content-->

        </div>
    </div>
</div>



@endsection

@push('scripts')
<script>
    // Tab persistency logic (optional, can be added if needed)
    // To keep it simple, we just use standard bootstrap tabs
</script>
@endpush
