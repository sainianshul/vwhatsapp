@extends('layouts.adminlte')

@section('title', 'Target Users')
@section('page_title', 'Target Intelligence')
@section('page_subtitle', 'Manage and monitor your tracked subjects and their social presence.')

@section('content')
<div class="row fade-in-up">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px; overflow: hidden;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                    Tracked Subjects
                </h5>
                <a href="{{ route('target.search.page') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i> Acquire Target
                </a>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-top" style="--bs-table-bg: transparent;">
                        <thead class="bg-light bg-opacity-50 text-muted" style="font-size: 0.85rem; letter-spacing: 0.5px; text-transform: uppercase;">
                            <tr>
                                <th class="border-0 px-4 py-3 fw-semibold">Subject Profile</th>
                                <th class="border-0 py-3 fw-semibold">Designation</th>
                                <th class="border-0 py-3 fw-semibold text-center">Digital Footprint</th>
                                <th class="border-0 py-3 fw-semibold">Tracking Status</th>
                                <th class="border-0 py-3 text-end px-4 fw-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($targets as $target)
                            <tr style="transition: all 0.2s;" class="target-row">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative me-3 shadow-sm rounded-circle">
                                            @if($target->photo_url)
                                                <img src="{{ $target->photo_url }}" class="rounded-circle border border-2 border-white" width="56" height="56" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary bg-gradient text-white d-flex align-items-center justify-content-center border border-2 border-white" style="width: 56px; height: 56px; font-size: 24px; font-weight: bold;">
                                                    {{ substr($target->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <!-- Online Indicator -->
                                            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-light rounded-circle" style="width: 12px; height: 12px; transform: translate(-10%, -10%);"></span>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 1.1rem;">{{ $target->name }}</h6>
                                            <span class="text-muted small">Added {{ $target->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($target->designation || $target->party)
                                        <div class="fw-medium text-dark">{{ $target->designation ?: 'Unknown' }}</div>
                                        <div class="text-muted small" style="font-size: 0.8rem;">
                                            <i class="bi bi-building me-1"></i>{{ $target->party ?: 'Independent' }}
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic small">Not specified</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary fw-bold rounded-pill px-3 py-1 border border-primary border-opacity-25" style="font-size: 0.85rem;">
                                        <i class="bi bi-diagram-3-fill me-2"></i>{{ $target->socialAccounts->count() }} Nodes
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($target->status === 'active')
                                        <div class="d-inline-flex align-items-center bg-success bg-opacity-10 text-success fw-bold rounded-pill px-3 py-1 border border-success border-opacity-25" style="font-size: 0.85rem;">
                                            <span class="spinner-grow spinner-grow-sm me-2" style="width: 10px; height: 10px;"></span> Active Sync
                                        </div>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2">Paused</span>
                                    @endif
                                </td>
                                <td class="py-3 text-end px-4">
                                    <a href="{{ route('target.show', $target->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm me-2">
                                        View Dossier <i class="bi bi-arrow-right-short ms-1"></i>
                                    </a>
                                    <form action="{{ route('target.destroy', $target->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Erase all intelligence on this target? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; padding: 0;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-4">
                                        <i class="bi bi-inboxes" style="font-size: 64px; color: #cbd5e1;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">Database Empty</h5>
                                    <p class="text-muted mb-4">No targets are currently being monitored.</p>
                                    <a href="{{ route('target.search.page') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                        <i class="bi bi-radar me-2"></i> Start Scanning
                                    </a>
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

<style>
    .target-row:hover {
        background-color: #f8fafc;
        transform: scale(1.002);
        box-shadow: inset 4px 0 0 0 var(--primary);
    }
</style>
@endsection
