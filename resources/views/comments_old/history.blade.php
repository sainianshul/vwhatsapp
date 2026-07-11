@extends('layouts.adminlte')

@section('title', 'Comment History')
@section('page_title', 'Comment History')
@section('page_subtitle', 'Track the status of all auto-commenting tasks')

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 text-muted small text-uppercase ps-4 py-3">Task ID</th>
                        <th class="border-0 text-muted small text-uppercase py-3">Target Post</th>
                        <th class="border-0 text-muted small text-uppercase py-3">Comment Text</th>
                        <th class="border-0 text-muted small text-uppercase py-3">Bot Account</th>
                        <th class="border-0 text-muted small text-uppercase py-3">Status</th>
                        <th class="border-0 text-muted small text-uppercase py-3 text-end pe-4">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $t)
                    <tr>
                        <td class="ps-4 py-3 fw-bold text-muted">#{{ $t->id }}</td>
                        <td class="py-3">
                            @if($t->post)
                                <a href="{{ $t->post->post_url }}" target="_blank" class="text-decoration-none fw-medium text-dark">
                                    {{ Str::limit($t->post->content, 30) ?: 'View Post' }}
                                </a>
                            @else
                                <span class="text-muted">Post deleted</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="badge bg-{{ $t->type === 'good' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $t->type === 'good' ? 'success' : 'danger' }} me-2 rounded-pill px-2">
                                <i class="bi bi-hand-thumbs-{{ $t->type === 'good' ? 'up' : 'down' }}-fill"></i>
                            </span>
                            {{ Str::limit($t->comment->comment_text ?? 'Deleted comment', 40) }}
                        </td>
                        <td class="py-3">
                            <i class="bi bi-robot text-primary me-1"></i>{{ $t->botAccount->account_name ?? 'Deleted bot' }}
                        </td>
                        <td class="py-3">
                            @if($t->status === 'posted')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1"><i class="bi bi-check-circle me-1"></i>Posted</span>
                            @elseif($t->status === 'failed')
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1" title="{{ $t->error_message }}">
                                    <i class="bi bi-x-circle me-1"></i>Failed
                                </span>
                            @elseif($t->status === 'processing')
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1">
                                    <span class="spinner-border spinner-border-sm me-1" style="width: 10px; height: 10px;"></span>Processing
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">Pending</span>
                            @endif
                            
                            @if($t->error_message && $t->status === 'failed')
                                <div class="small text-danger mt-1" style="max-width: 200px; font-size: 0.7rem;">
                                    {{ Str::limit($t->error_message, 50) }}
                                </div>
                            @endif
                        </td>
                        <td class="py-3 text-end pe-4 small text-muted">
                            {{ $t->created_at->format('M d, H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history fs-1 mb-2 d-block"></i>
                            No comment tasks found. Go to a Target Profile to initiate auto-comments.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tasks->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $tasks->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
