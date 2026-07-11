@extends('layouts.adminlte')

@section('title', 'Comment Bank')
@section('page_title', 'Comment Bank')
@section('page_subtitle', 'Manage pre-written auto-comments')

@section('content')
<div class="row">
    <!-- Add New Comment Form -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white pt-4 pb-0 border-0">
                <h6 class="fw-bold text-dark"><i class="bi bi-plus-circle text-primary me-2"></i>Add New Comment</h6>
            </div>
            <div class="card-body p-4">
                <form id="addCommentForm" onsubmit="addComment(event)">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Comment Text</label>
                        <textarea class="form-control rounded-3" name="comment_text" rows="4" placeholder="Write a great comment..." required></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Comment Type</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="typeGood" value="good" checked>
                                <label class="form-check-label fw-medium text-success" for="typeGood">
                                    <i class="bi bi-hand-thumbs-up-fill me-1"></i>Good
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="typeBad" value="bad">
                                <label class="form-check-label fw-medium text-danger" for="typeBad">
                                    <i class="bi bi-hand-thumbs-down-fill me-1"></i>Bad
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm" id="btnSubmit">
                        Save Comment
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Comment List -->
    <div class="col-md-8">
        <div class="row mb-3">
            <div class="col-6">
                <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10">
                    <div class="card-body p-3 text-center">
                        <h4 class="fw-bold text-success mb-0">{{ $goodCount }}</h4>
                        <small class="text-success fw-medium">Good Comments</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10">
                    <div class="card-body p-3 text-center">
                        <h4 class="fw-bold text-danger mb-0">{{ $badCount }}</h4>
                        <small class="text-danger fw-medium">Bad Comments</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 text-muted small text-uppercase ps-4 py-3">Type</th>
                                <th class="border-0 text-muted small text-uppercase py-3">Comment Text</th>
                                <th class="border-0 text-muted small text-uppercase text-end pe-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comments as $c)
                            <tr>
                                <td class="ps-4 py-3">
                                    @if($c->type === 'good')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="bi bi-hand-thumbs-up-fill me-1"></i>Good</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2"><i class="bi bi-hand-thumbs-down-fill me-1"></i>Bad</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <p class="mb-0 text-dark" style="font-size: 0.95rem;">{{ $c->comment_text }}</p>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <button class="btn btn-sm btn-light text-danger rounded-circle" onclick="deleteComment({{ $c->id }}, this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="bi bi-chat-square-text fs-1 mb-2 d-block"></i>
                                    No comments found. Add one on the left.
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

@section('scripts')
<script>
    async function addComment(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmit');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        const ogText = btn.innerHTML;
        btn.innerHTML = 'Saving...';
        btn.disabled = true;

        try {
            const res = await fetch('{{ route("comments.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });
            const json = await res.json();
            if(json.success) window.location.reload();
            else alert(json.error || 'Failed');
        } catch (err) {
            alert(err.message);
        } finally {
            btn.innerHTML = ogText;
            btn.disabled = false;
        }
    }

    async function deleteComment(id, btn) {
        if(!confirm('Delete this comment?')) return;
        
        try {
            const res = await fetch(`/comments/bank/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const json = await res.json();
            if(json.success) {
                btn.closest('tr').remove();
            }
        } catch (err) {
            alert(err.message);
        }
    }
</script>
@endsection
@endsection
