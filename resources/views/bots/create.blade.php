@extends('layouts.app')

@section('title', 'Add Bot')

@section('content')

    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <x-page-header title="Add Bot" description="Register a new automation bot" />
                <x-breadcrumb :items="[
                    ['label' => 'Bots', 'url' => route('bots.index')],
                    ['label' => 'Add Bot'],
                ]" />
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('bots.index') }}" class="btn btn-sm btn-light fw-semibold">
                    <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Back
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            <x-form-errors />

            <form method="POST" action="{{ route('bots.store') }}" class="form d-flex flex-column flex-lg-row" enctype="multipart/form-data">
                @csrf
                
                <!--begin::Aside column-->
                <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                    
                    <!--begin::Status-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">System Status</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <select name="status" class="form-select text-gray-900 border border-gray-300 bg-transparent" data-control="select2" data-hide-search="true">
                                @foreach (\App\Models\Bot::getStatusList() as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', \App\Models\Bot::STATUS_ACTIVE) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-gray-600 fs-7 mt-2">Internal state of this bot in our system.</div>
                        </div>
                    </div>
                    <!--end::Status-->

                    <!--begin::Type-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Bot Type</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <select name="type" class="form-select text-gray-900 border border-gray-300 bg-transparent" data-control="select2" data-hide-search="true">
                                @foreach (\App\Models\Bot::getTypeList() as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', \App\Models\Bot::TYPE_BOTH) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-gray-600 fs-7 mt-2">Capabilities allowed for this bot.</div>
                        </div>
                    </div>
                    <!--end::Type-->

                    <!--begin::Platform Status-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Platform Status</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <select name="platform_status" class="form-select text-gray-900 border border-gray-300 bg-transparent" data-control="select2" data-hide-search="true">
                                @foreach (\App\Models\Bot::getPlatformStatusList() as $value => $label)
                                    <option value="{{ $value }}" {{ old('platform_status', \App\Models\Bot::PLATFORM_STATUS_ACTIVE) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-gray-600 fs-7 mt-2">Status reported by the platform itself (e.g. banned, checkpoint).</div>
                        </div>
                    </div>
                    <!--end::Platform Status-->
                    
                    <div class="d-flex flex-column gap-3">
                        <button type="submit" class="btn btn-light-primary border border-primary fw-bold w-100 shadow-sm">
                            <i class="ki-outline ki-check fs-4 me-1"></i>Create Bot
                        </button>
                    </div>

                </div>
                <!--end::Aside column-->

                <!--begin::Main column-->
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    
                    <!--begin::General Info-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">General Information</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            
                            <div class="row g-9 mb-7">
                                <div class="col-md-6">
                                    <label class="required form-label fw-bold text-gray-900">Bot Name</label>
                                    <input type="text" name="name" class="form-control text-gray-900 border border-gray-300 bg-transparent" placeholder="e.g. FB Scraper #1" value="{{ old('name') }}" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="required form-label fw-bold text-gray-900">Target Platform</label>
                                    <select name="platform" class="form-select text-gray-900 border border-gray-300 bg-transparent" data-control="select2" data-hide-search="true" required>
                                        <option value="">Select Platform...</option>
                                        @foreach (\App\Models\Bot::getPlatformList() as $value => $label)
                                            <option value="{{ $value }}" {{ old('platform') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row g-9 mb-7">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-gray-900">Platform Username (Optional)</label>
                                    <input type="text" name="platform_username" class="form-control text-gray-900 border border-gray-300 bg-transparent" placeholder="@username" value="{{ old('platform_username') }}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-gray-900">Platform User ID (Optional)</label>
                                    <input type="text" name="platform_user_id" class="form-control text-gray-900 border border-gray-300 bg-transparent" placeholder="Account ID" value="{{ old('platform_user_id') }}" />
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--end::General Info-->

                    <!--begin::Persona Settings-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">AI Persona Settings (Optional)</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-9 mb-7">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-gray-900">Gender</label>
                                    <select name="gender" class="form-select text-gray-900 border border-gray-300 bg-transparent">
                                        <option value="">Any/Neutral</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-gray-900">Language</label>
                                    <input type="text" name="language" class="form-control text-gray-900 border border-gray-300 bg-transparent" placeholder="e.g. English, Hinglish" value="{{ old('language') }}" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-gray-900">Slang Level</label>
                                    <select name="slang_level" class="form-select text-gray-900 border border-gray-300 bg-transparent">
                                        <option value="">Normal</option>
                                        <option value="high" {{ old('slang_level') == 'high' ? 'selected' : '' }}>High (Gen Z/Casual)</option>
                                        <option value="none" {{ old('slang_level') == 'none' ? 'selected' : '' }}>None (Professional)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-7">
                                <label class="form-label fw-bold text-gray-900">AI Persona Profile</label>
                                <textarea name="ai_persona" class="form-control text-gray-900 border border-gray-300 bg-transparent" rows="2" placeholder="e.g. You are a 22-year old college student from NY. You use lowercase and abbreviations.">{{ old('ai_persona') }}</textarea>
                                <div class="text-gray-500 fs-8 mt-1">This context makes the AI comments highly realistic and unique to this bot.</div>
                            </div>
                            
                            <div>
                                <label class="form-label fw-bold text-gray-900">System Prompt Override (Advanced)</label>
                                <textarea name="system_prompt_override" class="form-control text-gray-900 border border-gray-300 bg-transparent" rows="2" placeholder="Only use this if you want to completely replace the default system instructions for this bot.">{{ old('system_prompt_override') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!--end::Persona Settings-->

                    <!--begin::Connection Settings-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Connection Settings</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            
                            <div class="mb-7">
                                <label class="form-label fw-bold text-gray-900">Proxy Address</label>
                                <input type="text" name="proxy" class="form-control text-gray-900 border border-gray-300 bg-transparent" placeholder="http://ip:port or http://user:pass@ip:port" value="{{ old('proxy') }}" />
                                <div class="text-gray-500 fs-8 mt-1">Leave blank to use direct connection.</div>
                            </div>

                            <div class="mb-7">
                                <label class="form-label fw-bold text-gray-900">User Agent</label>
                                <textarea name="user_agent" class="form-control text-gray-900 border border-gray-300 bg-transparent" rows="2" placeholder="Custom browser user agent string">{{ old('user_agent') }}</textarea>
                            </div>

                        </div>
                    </div>
                    <!--end::Connection Settings-->

                    <!--begin::Cookie Upload-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Authentication (Cookies)</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            
                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6 border-bottom border-gray-300">
                                <li class="nav-item">
                                    <a class="nav-link active text-gray-900 fw-bold" data-bs-toggle="tab" href="#cookie_file_tab">Upload File</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-gray-600 fw-semibold" data-bs-toggle="tab" href="#cookie_text_tab">Paste JSON</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="cookie_file_tab" role="tabpanel">
                                    <div class="mb-5">
                                        <label class="form-label fw-bold text-gray-900">Cookie JSON File</label>
                                        <input class="form-control text-gray-900 border border-gray-300 bg-transparent" type="file" name="cookie_file" accept=".json,.txt" />
                                        <div class="text-gray-500 fs-8 mt-2">Export cookies using EditThisCookie or Puppeteer extensions and upload the file here.</div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="cookie_text_tab" role="tabpanel">
                                    <div class="mb-5">
                                        <label class="form-label fw-bold text-gray-900">Raw Cookie Data</label>
                                        <textarea name="cookie" class="form-control text-gray-900 border border-gray-300 bg-transparent font-monospace" rows="5" placeholder="[{...}, {...}]">{{ old('cookie') }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--end::Cookie Upload-->

                    <!--begin::Notes-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Notes</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <textarea name="notes" class="form-control text-gray-900 border border-gray-300 bg-transparent" rows="4" placeholder="Any additional information...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <!--end::Notes-->

                </div>
                <!--end::Main column-->
            </form>
        </div>
    </div>
    <!--end::Content-->

@endsection
