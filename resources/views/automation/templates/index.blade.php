@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Comments Templates
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">Automation</li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-gray-900">Templates</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('automation-templates.create') }}" class="btn btn-sm fw-bold btn-primary">
                        <i class="ki-outline ki-plus fs-2"></i> Create Template
                    </a>
                </div>
            </div>
        </div>
        <!-- End Toolbar -->

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                @include('layouts.partials._alerts')

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="templates_table">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-200px">Name / Platform</th>
                                        <th class="min-w-150px text-center">Engine / Tone</th>
                                        <th class="min-w-100px text-center">Limits</th>
                                        <th class="min-w-100px text-center">Assigned Accts</th>
                                        <th class="text-end min-w-100px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @forelse($templates as $template)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    @if($template->platform === 'facebook')
                                                        <span class="symbol-label bg-light-primary"><i class="ki-outline ki-facebook fs-2 text-primary"></i></span>
                                                    @elseif($template->platform === 'instagram')
                                                        <span class="symbol-label bg-light-danger"><i class="ki-outline ki-instagram fs-2 text-danger"></i></span>
                                                    @elseif($template->platform === 'twitter')
                                                        <span class="symbol-label bg-light-info"><i class="ki-outline ki-twitter fs-2 text-info"></i></span>
                                                    @else
                                                        <span class="symbol-label bg-light-dark"><i class="ki-outline ki-youtube fs-2 text-dark"></i></span>
                                                    @endif
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-gray-900 fw-bold fs-6">{{ $template->name }}</span>
                                                    <span class="text-muted fw-semibold text-muted d-block fs-7 text-capitalize">{{ $template->platform }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($template->engine_type === 'ai')
                                                <span class="badge badge-light-primary mb-1">AI Gen</span><br>
                                                <span class="badge badge-light-{{ $template->tone_color }} text-capitalize">{{ $template->ai_tone }}</span>
                                            @else
                                                <span class="badge badge-light-success">Comment Bank</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-muted fs-7">
                                            <div>Delay: {{ $template->min_delay_mins }}-{{ $template->max_delay_mins }}m</div>
                                            <div>Max/Day: {{ $template->max_daily_comments }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary fs-6">{{ $template->automation_rules_count }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('automation-templates.edit', ['automation_template' => $template->id]) }}" class="btn btn-icon btn-active-light-primary w-30px h-30px me-3" data-bs-toggle="tooltip" title="Edit Template">
                                                <i class="ki-outline ki-pencil fs-3"></i>
                                            </a>
                                            <form action="{{ route('automation-templates.destroy', ['automation_template' => $template->id]) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-active-light-danger w-30px h-30px" onclick="return confirm('Are you sure you want to delete this template?')" data-bs-toggle="tooltip" title="Delete Template">
                                                    <i class="ki-outline ki-trash fs-3"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-8">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="ki-outline ki-abstract-11 fs-3x text-muted mb-3"></i>
                                                <span class="fs-5 fw-semibold mb-2">No Templates Found</span>
                                                <span class="text-muted fs-7">You haven't created any AI automation templates yet.</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
