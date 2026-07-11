@extends('layouts.app')

@section('title', 'Add Profile')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <x-page-header title="Add Profile" description="Create a new intelligence profile" />
                <x-breadcrumb :items="[
                    ['label' => 'Profiles', 'url' => route('subjects.index')],
                    ['label' => 'Add Profile'],
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
            
            <x-form-errors />

            <div class="card card-bordered shadow-sm border-gray-300">
                <form action="{{ route('subjects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="card-body p-9">
                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3 text-dark">Profile Photo</div>
                                <div class="text-muted fs-7">Optional avatar or image of the subject.</div>
                            </div>
                            <div class="col-xl-9 fv-row">
                                <div class="image-input image-input-outline image-input-empty" data-kt-image-input="true" style="background-image: url('{{ asset('media/svg/avatars/blank.svg') }}')">
                                    <div class="image-input-wrapper w-125px h-125px shadow-sm"></div>
                                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                        <i class="ki-outline ki-pencil fs-7"></i>
                                        <input type="file" name="photo" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="avatar_remove" />
                                    </label>
                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                        <i class="ki-outline ki-cross fs-2"></i>
                                    </span>
                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                        <i class="ki-outline ki-cross fs-2"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3 text-dark">Subject Name <span class="text-danger">*</span></div>
                                <div class="text-muted fs-7">The person or entity's name.</div>
                            </div>
                            <div class="col-xl-9 fv-row">
                                <input type="text" class="form-control form-control-solid" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required />
                            </div>
                        </div>

                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3 text-dark">Designation / Role</div>
                                <div class="text-muted fs-7">Their profession, title, or significance.</div>
                            </div>
                            <div class="col-xl-9 fv-row">
                                <input type="text" class="form-control form-control-solid" name="designation" value="{{ old('designation') }}" placeholder="e.g. Politician, Influencer, Competitor" />
                            </div>
                        </div>

                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3 text-dark">Internal Notes</div>
                                <div class="text-muted fs-7">Private notes about why you are tracking this profile.</div>
                            </div>
                            <div class="col-xl-9 fv-row">
                                <textarea name="notes" class="form-control form-control-solid" data-kt-autosize="true" rows="4" placeholder="Type background information here...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-end py-6 px-9 border-top border-gray-300">
                        <a href="{{ route('subjects.index') }}" class="btn btn-light btn-active-light-primary me-2 fw-semibold shadow-sm">Discard</a>
                        <button type="submit" class="btn btn-primary fw-semibold shadow-sm" id="kt_submit_btn">
                            <span class="indicator-label"><i class="ki-outline ki-check fs-4 me-1"></i> Create Profile</span>
                            <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
