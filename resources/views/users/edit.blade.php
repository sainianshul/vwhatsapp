@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <x-page-header title="Edit User" description="Update user details and status" />
                <x-breadcrumb :items="[
                    ['label' => 'People'],
                    ['label' => 'Users', 'url' => route('users.index')],
                    ['label' => 'Edit User'],
                ]" />
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-light fw-semibold">
                    <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Back
                </a>
                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-light-info border border-info fw-bold shadow-sm">
                    <i class="ki-outline ki-eye fs-4 me-1"></i>View Profile
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            <x-form-errors />

            <form method="POST" action="{{ route('users.update', $user) }}" class="form d-flex flex-column flex-lg-row">
                @csrf
                
                <!--begin::Aside column-->
                <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                    <!--begin::Status-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Account Status</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <select name="status" class="form-select text-gray-900 border border-gray-300 bg-transparent" data-control="select2" data-hide-search="true">
                                @foreach (\App\Models\User::getStatusList() as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $user->status) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-gray-600 fs-7 mt-2">Set the user account status.</div>
                        </div>
                    </div>
                    <!--end::Status-->
                    
                    <div class="d-flex flex-column gap-3">
                        <button type="submit" class="btn btn-light-primary border border-primary fw-bold w-100 shadow-sm">
                            <i class="ki-outline ki-check fs-4 me-1"></i>Save Changes
                        </button>
                    </div>
                </div>
                <!--end::Aside column-->

                <!--begin::Main column-->
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">User Information</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-7">
                                <label class="required form-label text-gray-900 fw-semibold">Full Name</label>
                                <input type="text" name="name" class="form-control text-gray-900 bg-transparent @error('name') is-invalid border-danger @else border border-gray-300 @enderror"
                                    value="{{ old('name', $user->name) }}" required />
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="text-gray-600 fs-7 mt-2">Update the user's full name.</div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-7">
                                <label class="required form-label text-gray-900 fw-semibold">Phone Number</label>
                                <div class="position-relative">
                                    <i class="ki-outline ki-phone fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-600"></i>
                                    <input type="text" name="phone" class="form-control text-gray-900 bg-transparent ps-12 @error('phone') is-invalid border-danger @else border border-gray-300 @enderror"
                                        value="{{ old('phone', $user->phone) }}" placeholder="Enter phone number" required pattern="[0-9+]*" minlength="10" maxlength="15" oninput="this.value = this.value.replace(/[^0-9+]/g, '')" />
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="text-gray-600 fs-7 mt-2">Update the user's primary contact number.</div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-7">
                                <label class="form-label text-gray-900 fw-semibold">Email Address</label>
                                <div class="position-relative">
                                    <i class="ki-outline ki-sms fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-900"></i>
                                    <input type="email" name="email" class="form-control text-gray-900 bg-transparent ps-12 @error('email') is-invalid border-danger @else border border-gray-300 @enderror"
                                        placeholder="Enter email address" value="{{ old('email', $user->email) }}" />
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="text-gray-600 fs-7 mt-2">Optional email address for communication and notifications.</div>
                            </div>
                            <!--end::Input group-->
                        </div>
                    </div>
                </div>
                <!--end::Main column-->
            </form>
        </div>
    </div>
    <!--end::Content-->

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('[data-control="select2"]').select2({
            minimumResultsForSearch: Infinity
        });
    });
</script>
@endpush
