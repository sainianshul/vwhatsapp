@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Command Center & Audit Logs
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">Automation</li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-gray-900">Command Center</li>
                    </ul>
                </div>
                
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <!-- Filter Dropdown -->
                    <form method="GET" action="{{ route('command-center.index') }}" class="d-flex">
                        <select name="status" class="form-select form-select-sm form-select-solid w-150px me-3" onchange="this.form.submit()">
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Queue)</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                @include('layouts.partials._alerts')

                <div class="card shadow-sm">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Execution Queue & History</span>
                            <span class="text-muted fw-semibold fs-7">100% Transparent logs of what the AI bots are doing.</span>
                        </h3>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-150px">Scheduled Time</th>
                                        <th class="min-w-200px">Target Account / Post</th>
                                        <th class="min-w-300px">Content To Post</th>
                                        <th class="min-w-100px text-center">Status</th>
                                        <th class="text-end min-w-100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @forelse($operations as $op)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-900 fw-bold fs-6" data-bs-toggle="tooltip" title="{{ $op->scheduled_at->format('d M Y, h:i A') }}">
                                                    {{ $op->scheduled_at->diffForHumans() }}
                                                </span>
                                                @if($op->status === 'completed' && $op->completed_at)
                                                    <span class="text-success fs-8">Done: {{ $op->completed_at->format('h:i A') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-30px me-2">
                                                    @if($op->socialAccount)
                                                        @if($op->socialAccount->platform === 'facebook')
                                                            <span class="symbol-label bg-light-primary"><i class="ki-outline ki-facebook fs-4 text-primary"></i></span>
                                                        @elseif($op->socialAccount->platform === 'twitter')
                                                            <span class="symbol-label bg-light-info"><i class="ki-outline ki-twitter fs-4 text-info"></i></span>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-gray-900 fw-bold fs-7">{{ $op->socialAccount->account_name ?? 'Unknown' }}</span>
                                                    <a href="{{ $op->post->account_url ?? '#' }}" target="_blank" class="text-primary text-hover-primary fw-semibold fs-8">View Post <i class="ki-outline ki-exit-right-corner fs-8"></i></a>
                                                </div>
                                            </div>
                                            @if($op->assignedBot)
                                                <div class="mt-1">
                                                    <span class="badge badge-light-dark fs-8">🤖 Bot: {{ $op->assignedBot->name }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="border border-gray-300 border-dashed rounded p-3 bg-light-secondary text-gray-800" style="max-height: 80px; overflow-y: auto;">
                                                {{ $op->content_to_post }}
                                            </div>
                                            @if($op->status === 'failed' && $op->error_log)
                                                <div class="mt-2 text-danger fs-8">
                                                    <strong>Error:</strong> {{ $op->error_log }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-light-{{ $op->status_color }} fs-7">{{ ucfirst($op->status) }}</span>
                                        </td>
                                        <td class="text-end">
                                            @if($op->status === 'pending')
                                                <form action="{{ route('command-center.cancel', $op) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-light-warning" onclick="return confirm('Cancel this upcoming comment?')">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('command-center.destroy', $op) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-active-light-danger w-30px h-30px" onclick="return confirm('Delete this log?')" data-bs-toggle="tooltip" title="Delete Log">
                                                        <i class="ki-outline ki-trash fs-3"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-8">
                                            No automation logs or pending operations found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex flex-stack flex-wrap pt-10">
                                {{ $operations->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
