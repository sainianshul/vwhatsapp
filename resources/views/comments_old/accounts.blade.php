@extends('layouts.adminlte')

@section('title', 'Facebook Bot Accounts')
@section('page_title', 'Bot Accounts')
@section('page_subtitle', 'Manage Facebook credentials for auto-commenting')

@section('content')
<div class="row">
    <!-- Add New Account Form -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white pt-4 pb-0 border-0">
                <h6 class="fw-bold text-dark"><i class="bi bi-robot text-primary me-2"></i>Add FB Account</h6>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-warning small rounded-3 border-warning">
                    <i class="bi bi-shield-lock me-1"></i> Credentials are encrypted in the database. Use dummy/burner accounts to prevent primary account bans.
                </div>
                <form id="addAccountForm" onsubmit="addAccount(event)">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Display Name</label>
                        <input type="text" class="form-control rounded-3" name="account_name" placeholder="e.g. Bot Alpha" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Facebook Email/Phone</label>
                        <input type="text" class="form-control rounded-3" name="fb_email" placeholder="For reference only" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold d-flex justify-content-between">
                            <span>Facebook Session Cookies (JSON)</span>
                            <a href="https://chrome.google.com/webstore/detail/export-cookie-json-file-f/nmckfndlhmjkefjdndocnbofnhonmkim" target="_blank" class="text-decoration-none small"><i class="bi bi-box-arrow-up-right me-1"></i>Get Extension</a>
                        </label>
                        <textarea class="form-control rounded-3 text-monospace" name="fb_cookies" rows="5" placeholder='[{"domain": ".facebook.com", "name": "c_user", ...}]' required style="font-size: 0.8rem;"></textarea>
                        <div class="form-text small mt-2">
                            Log into Facebook on your browser, use a Cookie Export extension, and paste the JSON here.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm" id="btnSubmit">
                        Securely Save Account
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Accounts List -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 text-muted small text-uppercase ps-4 py-3">Bot Name</th>
                                <th class="border-0 text-muted small text-uppercase py-3">Status</th>
                                <th class="border-0 text-muted small text-uppercase py-3">Total Comments</th>
                                <th class="border-0 text-muted small text-uppercase py-3">Last Used</th>
                                <th class="border-0 text-muted small text-uppercase text-end pe-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $acc)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #1877F2, #0d5cbe); border-radius: 8px;" class="d-flex align-items-center justify-content-center text-white flex-shrink-0">
                                            <i class="bi bi-facebook" style="font-size: 16px;"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $acc->account_name }}</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($acc->status === 'active')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Active</span>
                                    @elseif($acc->status === 'cooldown')
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1">Cooldown</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">Banned</span>
                                    @endif
                                </td>
                                <td class="py-3 fw-bold">{{ $acc->total_comments_posted }}</td>
                                <td class="py-3 small text-muted">
                                    {{ $acc->last_used_at ? $acc->last_used_at->diffForHumans() : 'Never' }}
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <button class="btn btn-sm btn-light text-danger rounded-circle" onclick="deleteAccount({{ $acc->id }}, this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-robot fs-1 mb-2 d-block"></i>
                                    No bot accounts found. Add one on the left.
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
    async function addAccount(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmit');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        const ogText = btn.innerHTML;
        btn.innerHTML = 'Saving...';
        btn.disabled = true;

        try {
            const res = await fetch('{{ route("comments.accounts.store") }}', {
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

    async function deleteAccount(id, btn) {
        if(!confirm('Delete this bot account? Credentials will be destroyed.')) return;
        
        try {
            const res = await fetch(`/comments/accounts/${id}`, {
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
