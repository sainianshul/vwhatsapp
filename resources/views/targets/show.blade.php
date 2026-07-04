@extends('layouts.adminlte')

@section('title', 'Target: ' . $target->name)
@section('page_title', $target->name)
@section('page_subtitle', 'Facebook Posts & Comment Operations')

@section('content')
@php
    $allPosts = collect();
    foreach($target->socialAccounts as $acc) {
        if($acc->posts) {
            $allPosts = $allPosts->merge($acc->posts);
        }
    }
    $allPosts = $allPosts->sortByDesc('posted_at');
@endphp

<div class="row">
    {{-- ====== LEFT SIDEBAR ====== --}}
    <div class="col-lg-3 col-md-4 mb-4">
        <div class="card border-0 shadow-sm position-sticky" style="border-radius: 20px; overflow: hidden; top: 80px;">
            {{-- Cover --}}
            <div style="height: 90px; background: linear-gradient(135deg, #1877F2 0%, #0d5cbe 100%); position: relative;">
                <div style="position: absolute; inset: 0; background-image: radial-gradient(circle at top right, rgba(255,255,255,0.15) 0%, transparent 60%);"></div>
            </div>

            <div class="card-body text-center position-relative px-3 pb-4" style="margin-top: -45px;">
                @if($target->photo_url)
                    <img src="{{ $target->photo_url }}" class="rounded-circle shadow mb-2 bg-white p-1" width="90" height="90" style="object-fit: cover; border: 3px solid #fff;">
                @else
                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center mx-auto shadow mb-2" style="width: 90px; height: 90px; font-size: 36px; font-weight: 800; border: 3px solid #fff;">
                        {{ substr($target->name, 0, 1) }}
                    </div>
                @endif

                <h5 class="fw-bold text-dark mb-0">{{ $target->name }}</h5>
                <p class="text-muted small mb-2">{{ $target->designation ?? 'Target Profile' }}</p>

                @if($target->party)
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 mb-2">{{ $target->party }}</span>
                @endif

                {{-- Linked Accounts --}}
                <div class="text-start mt-3 mb-3">
                    <h6 class="fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 1px;">Linked Accounts</h6>
                    @foreach($target->socialAccounts as $account)
                    <div class="d-flex align-items-center gap-2 p-2 rounded-3 mb-1 bg-light" style="font-size: 0.82rem;">
                        <i class="bi bi-facebook text-primary"></i>
                        <span class="fw-medium text-dark text-truncate" style="max-width: 140px;">{{ $account->account_name }}</span>
                        @if($account->followers_count)
                        <span class="ms-auto text-muted small">{{ $account->followers_count }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Deep Scrape Buttons --}}
                @foreach($target->socialAccounts as $account)
                <button class="btn btn-primary w-100 rounded-pill py-2 fw-bold mb-2 shadow-sm btn-deep-scrape" 
                        data-account-id="{{ $account->id }}" 
                        data-target-id="{{ $target->id }}" style="font-size: 0.85rem;">
                    <i class="bi bi-arrow-repeat me-2"></i>Fetch Posts
                </button>
                @endforeach

                <a href="{{ route('target.index') }}" class="btn btn-light w-100 rounded-pill py-2 fw-medium border mt-1" style="font-size: 0.85rem;">
                    <i class="bi bi-arrow-left me-2"></i>Back to Targets
                </a>
            </div>
        </div>
    </div>

    {{-- ====== MAIN CONTENT: Posts Feed ====== --}}
    <div class="col-lg-9 col-md-8">
        
        {{-- Posts Count Header --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-grid-3x3-gap me-2 text-primary"></i>{{ $allPosts->count() }} Posts</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-light rounded-pill border filter-btn active" data-filter="all"><i class="bi bi-grid me-1"></i>All</button>
                <button class="btn btn-sm btn-light rounded-pill border filter-btn" data-filter="photo"><i class="bi bi-image me-1 text-success"></i>Photos</button>
                <button class="btn btn-sm btn-light rounded-pill border filter-btn" data-filter="text"><i class="bi bi-card-text me-1 text-primary"></i>Text</button>
            </div>
        </div>

        {{-- Loading Indicator --}}
        <div id="scrapeLoading" class="d-none">
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center mb-4">
                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                <h5 class="fw-bold text-dark">Fetching Posts from Facebook...</h5>
                <p class="text-muted mb-0">This may take 30-60 seconds. Scrolling through the profile to collect posts.</p>
            </div>
        </div>

        {{-- No Posts Message --}}
        @if($allPosts->count() === 0)
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <h5 class="fw-bold text-dark mt-3">No Posts Yet</h5>
            <p class="text-muted">Click <strong>"Fetch Posts"</strong> on the left to scrape posts from this profile.</p>
        </div>
        @endif

        {{-- Posts Feed --}}
        <div id="postsContainer">
            @foreach($allPosts as $post)
            <div class="card border-0 shadow-sm rounded-4 mb-3 post-card" data-type="{{ $post->post_type }}" style="transition: all 0.2s;">
                <div class="card-body p-4">
                    {{-- Post Header --}}
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #1877F2, #0d5cbe); border-radius: 50%;" class="d-flex align-items-center justify-content-center text-white flex-shrink-0">
                                <i class="bi bi-facebook" style="font-size: 20px;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.92rem;">{{ $post->socialAccount->account_name ?? $target->name }}</h6>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $post->posted_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $post->post_type === 'photo' ? 'success' : 'primary' }} bg-opacity-10 text-{{ $post->post_type === 'photo' ? 'success' : 'primary' }} rounded-pill small">
                                <i class="bi {{ $post->post_type === 'photo' ? 'bi-image' : 'bi-card-text' }} me-1"></i>{{ ucfirst($post->post_type) }}
                            </span>
                            @if($post->post_url)
                            <a href="{{ $post->post_url }}" target="_blank" class="btn btn-sm btn-light rounded-circle border" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;" title="Open on Facebook">
                                <i class="bi bi-box-arrow-up-right" style="font-size: 11px;"></i>
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Post Content --}}
                    @if($post->content)
                    <div class="mb-3" style="font-size: 0.95rem; line-height: 1.65; color: #1c1e21;">
                        <span class="post-text-short">{{ Str::limit($post->content, 300) }}</span>
                        @if(strlen($post->content) > 300)
                        <span class="post-text-full d-none">{{ $post->content }}</span>
                        <a href="javascript:void(0)" class="text-primary text-decoration-none fw-medium toggle-text small">See more</a>
                        @endif
                    </div>
                    @endif

                    {{-- Post Image --}}
                    @if($post->media_url)
                    <div class="mb-3 rounded-3 overflow-hidden bg-light" style="max-height: 400px;">
                        <img src="{{ $post->media_url }}" class="w-100" style="object-fit: cover; max-height: 400px;" loading="lazy">
                    </div>
                    @endif

                    {{-- Engagement Stats --}}
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom" style="font-size: 0.82rem;">
                        <span class="text-muted"><i class="bi bi-heart-fill text-danger me-1"></i>{{ number_format($post->reactions_count) }}</span>
                        <span class="text-muted"><i class="bi bi-chat-fill text-primary me-1"></i>{{ number_format($post->comments_count) }}</span>
                        <span class="text-muted"><i class="bi bi-share-fill text-success me-1"></i>{{ number_format($post->shares_count) }}</span>
                    </div>

                    {{-- Like & Quick Comment Buttons --}}
                    <div class="d-flex gap-2 mb-3">
                        <button class="btn btn-sm btn-outline-primary rounded-pill flex-fill btn-like-post" 
                                data-post-url="{{ $post->post_url }}">
                            <i class="bi bi-hand-thumbs-up-fill me-1"></i>Like
                        </button>
                        <button class="btn btn-sm btn-outline-success rounded-pill flex-fill btn-quick-comment" 
                                data-post-url="{{ $post->post_url }}" data-post-id="{{ $post->id }}" data-type="good">
                            <i class="bi bi-chat-left-dots me-1"></i>Good Comment
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill flex-fill btn-quick-comment" 
                                data-post-url="{{ $post->post_url }}" data-post-id="{{ $post->id }}" data-type="bad">
                            <i class="bi bi-chat-left-dots me-1"></i>Bad Comment
                        </button>
                    </div>

                    {{-- Manual Comment Input --}}
                    <div class="d-flex gap-2 align-items-start">
                        <div class="flex-grow-1">
                            <input type="text" class="form-control rounded-pill border comment-input" 
                                   placeholder="Write a comment..." 
                                   data-post-url="{{ $post->post_url }}"
                                   style="font-size: 0.88rem; padding: 8px 16px;">
                        </div>
                        <button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center btn-send-comment" 
                                data-post-url="{{ $post->post_url }}"
                                style="width: 38px; height: 38px; flex-shrink: 0;" title="Send Comment">
                            <i class="bi bi-send-fill" style="font-size: 14px;"></i>
                        </button>
                    </div>
                    {{-- Comment Status --}}
                    <div class="comment-status mt-2 d-none" data-post-url="{{ $post->post_url }}" style="font-size: 0.8rem;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
.post-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important; }
.filter-btn.active { background: #1877F2 !important; color: #fff !important; border-color: #1877F2 !important; }
.comment-input:focus { border-color: #1877F2; box-shadow: 0 0 0 3px rgba(24,119,242,0.15); }
.btn-send-comment:disabled { opacity: 0.5; }
</style>
@endsection

@section('scripts')
<script>
    // ============ DEEP SCRAPE ============
    document.querySelectorAll('.btn-deep-scrape').forEach(btn => {
        btn.addEventListener('click', async function() {
            const accountId = this.dataset.accountId;
            const targetId = this.dataset.targetId;
            const loading = document.getElementById('scrapeLoading');
            
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Scraping...';
            loading.classList.remove('d-none');

            try {
                const res = await fetch(`/target/${targetId}/deep-scrape/${accountId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (json.success) {
                    window.location.reload();
                } else {
                    alert('Scrape failed: ' + (json.error || 'Unknown error'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Fetch Posts';
                loading.classList.add('d-none');
            }
        });
    });

    // ============ FILTER POSTS ============
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.post-card').forEach(card => {
                card.style.display = (filter === 'all' || card.dataset.type === filter) ? '' : 'none';
            });
        });
    });

    // ============ TOGGLE TEXT ============
    document.querySelectorAll('.toggle-text').forEach(link => {
        link.addEventListener('click', function() {
            const parent = this.parentElement;
            const short = parent.querySelector('.post-text-short');
            const full = parent.querySelector('.post-text-full');
            if (full.classList.contains('d-none')) {
                short.classList.add('d-none');
                full.classList.remove('d-none');
                this.textContent = 'See less';
            } else {
                full.classList.add('d-none');
                short.classList.remove('d-none');
                this.textContent = 'See more';
            }
        });
    });

    // ============ MANUAL COMMENT (Send Button) ============
    document.querySelectorAll('.btn-send-comment').forEach(btn => {
        btn.addEventListener('click', function() {
            const postUrl = this.dataset.postUrl;
            const input = document.querySelector(`.comment-input[data-post-url="${postUrl}"]`);
            const text = input.value.trim();
            if (!text) { input.focus(); return; }
            sendComment(postUrl, text, this, input);
        });
    });

    // Enter key to send
    document.querySelectorAll('.comment-input').forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const postUrl = this.dataset.postUrl;
                const text = this.value.trim();
                if (!text) return;
                const btn = document.querySelector(`.btn-send-comment[data-post-url="${postUrl}"]`);
                sendComment(postUrl, text, btn, this);
            }
        });
    });

    // ============ GOOD/BAD COMMENT BUTTONS ============
    document.querySelectorAll('.btn-quick-comment').forEach(btn => {
        btn.addEventListener('click', async function() {
            const postUrl = this.dataset.postUrl;
            const postId = this.dataset.postId;
            const type = this.dataset.type;
            
            if (!confirm(`Post a ${type} comment on this post?`)) return;

            const statusEl = document.querySelector(`.comment-status[data-post-url="${postUrl}"]`);
            this.disabled = true;
            this.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Posting...`;
            showStatus(statusEl, 'warning', '<span class="spinner-border spinner-border-sm me-1"></span>Sending comment...');

            try {
                const res = await fetch('{{ route("comments.execute") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ 
                        post_ids: [postId], 
                        type: type
                    })
                });
                const json = await res.json();
                if (json.success) {
                    showStatus(statusEl, 'success', `<i class="bi bi-check-circle-fill me-1"></i>${json.message}`);
                } else {
                    showStatus(statusEl, 'danger', `<i class="bi bi-x-circle-fill me-1"></i>${json.error}`);
                }
            } catch (err) {
                showStatus(statusEl, 'danger', `<i class="bi bi-x-circle-fill me-1"></i>${err.message}`);
            } finally {
                this.disabled = false;
                this.innerHTML = `<i class="bi bi-chat-left-dots me-1"></i>${type === 'good' ? 'Good' : 'Bad'} Comment`;
            }
        });
    });

    // ============ LIKE POST BUTTON ============
    document.querySelectorAll('.btn-like-post').forEach(btn => {
        btn.addEventListener('click', async function() {
            const postUrl = this.dataset.postUrl;
            
            if (!confirm(`Like this post?`)) return;

            const statusEl = document.querySelector(`.comment-status[data-post-url="${postUrl}"]`);
            this.disabled = true;
            this.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Liking...`;
            showStatus(statusEl, 'warning', '<span class="spinner-border spinner-border-sm me-1"></span>Liking post...');

            try {
                const res = await fetch('{{ route("comments.like-post") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ 
                        post_url: postUrl 
                    })
                });
                const json = await res.json();
                if (json.success) {
                    showStatus(statusEl, 'success', `<i class="bi bi-check-circle-fill me-1"></i>${json.message}`);
                } else {
                    showStatus(statusEl, 'danger', `<i class="bi bi-x-circle-fill me-1"></i>${json.error}`);
                }
            } catch (err) {
                showStatus(statusEl, 'danger', `<i class="bi bi-x-circle-fill me-1"></i>${err.message}`);
            } finally {
                this.disabled = false;
                this.innerHTML = `<i class="bi bi-hand-thumbs-up-fill me-1"></i>Like`;
            }
        });
    });

    // ============ SEND COMMENT HELPER ============
    async function sendComment(postUrl, text, btn, input) {
        const statusEl = document.querySelector(`.comment-status[data-post-url="${postUrl}"]`);
        btn.disabled = true;
        input.disabled = true;
        showStatus(statusEl, 'warning', '<span class="spinner-border spinner-border-sm me-1"></span>Posting your comment...');

        try {
            const res = await fetch('{{ route("comments.post-single") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ 
                    post_url: postUrl, 
                    comment_text: text,
                    target_name: '{{ $target->name }}'
                })
            });
            const json = await res.json();
            if (json.success) {
                showStatus(statusEl, 'success', `<i class="bi bi-check-circle-fill me-1"></i>${json.message}`);
                input.value = '';
            } else {
                showStatus(statusEl, 'danger', `<i class="bi bi-x-circle-fill me-1"></i>${json.error}`);
            }
        } catch (err) {
            showStatus(statusEl, 'danger', `<i class="bi bi-x-circle-fill me-1"></i>${err.message}`);
        } finally {
            btn.disabled = false;
            input.disabled = false;
        }
    }

    function showStatus(el, type, html) {
        el.classList.remove('d-none', 'text-success', 'text-danger', 'text-warning');
        el.classList.add(`text-${type}`);
        el.innerHTML = html;
    }
</script>
@endsection
