@extends('layouts.adminlte')

@section('title', 'Target: ' . $target->name)
@section('page_title', $target->name)
@section('page_subtitle', 'Facebook Posts & Comment Operations')

@section('content')


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
                <div class="d-flex gap-1 mb-2">
                    <button class="btn btn-primary flex-fill rounded-pill py-2 fw-bold shadow-sm btn-deep-scrape" 
                            data-account-id="{{ $account->id }}" 
                            data-target-id="{{ $target->id }}" style="font-size: 0.8rem;">
                        <i class="bi bi-arrow-repeat me-1"></i>Sync
                    </button>
                    <button class="btn btn-outline-warning flex-fill rounded-pill py-2 fw-bold btn-gql-scrape" 
                            data-account-id="{{ $account->id }}" 
                            data-target-id="{{ $target->id }}"
                            data-account-name="{{ $account->account_name }}" 
                            style="font-size: 0.8rem;" title="GraphQL Intercept Scrape — Demo">
                        <i class="bi bi-bug me-1"></i>Own Bot
                    </button>
                </div>
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
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-grid-3x3-gap me-2 text-primary"></i>{{ $posts->total() }} Posts</h5>
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
        @if($posts->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <h5 class="fw-bold text-dark mt-3">No Posts Yet</h5>
            <p class="text-muted">Click <strong>"Fetch Posts"</strong> on the left to scrape posts from this profile.</p>
        </div>
        @endif

        {{-- Posts Feed --}}
        <div id="postsContainer">
            @foreach($posts as $post)
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
                    @if($post->media_url && !str_contains($post->media_url, 'facebook.com/photo'))
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
        
        {{-- Pagination Links --}}
        <div class="d-flex justify-content-center mt-4 mb-5">
            {{ $posts->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- ====== GRAPHQL SCRAPE FULL-PAGE OVERLAY (No Bootstrap Modal — avoids z-index issues) ====== --}}
<div id="gqlOverlay" style="display:none; position:fixed; inset:0; z-index:99999; background:#fff; overflow-y:auto;">
    {{-- Header --}}
    <div style="background:linear-gradient(135deg,#f59e0b,#d97706); padding:14px 24px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:10;">
        <div>
            <h5 style="margin:0; font-weight:700; color:#fff; font-size:1.1rem;">🐛 GraphQL Scrape — Raw JSON Viewer</h5>
            <small style="color:rgba(255,255,255,0.8);" id="gqlModalSubtitle">Analyzing...</small>
        </div>
        <button onclick="closeGqlOverlay()" style="background:rgba(255,255,255,0.25); border:none; color:#fff; border-radius:50%; width:36px; height:36px; cursor:pointer; font-size:18px; line-height:36px;">✕</button>
    </div>

    {{-- Stats Bar --}}
    <div id="gqlStats" style="display:none; background:#1e293b; color:#fff; padding:10px 24px;">
        <div style="display:flex; gap:24px; flex-wrap:wrap; font-size:0.85rem;">
            <div>⚡ GraphQL Responses: <strong id="statGqlCount">0</strong></div>
            <div>📦 Raw Nodes: <strong id="statNodeCount">0</strong></div>
            <div>📄 Parsed Posts: <strong id="statPostCount">0</strong></div>
            <div>🤖 Bot: <strong id="statBotName">-</strong></div>
            <div>🕐 Time: <strong id="statTime">-</strong></div>
        </div>
    </div>

    {{-- Loading --}}
    <div id="gqlLoading" style="text-align:center; padding:60px 20px;">
        <div class="spinner-border text-warning mb-3" style="width:3rem; height:3rem;"></div>
        <h5 style="font-weight:700;">🔬 GraphQL Intercept Scraping...</h5>
        <p style="color:#6b7280;">Loading profile with cookies → Scrolling → Capturing GraphQL</p>
        <p style="color:#9ca3af; font-size:0.85rem;">Takes 30-90 seconds.</p>
        <div style="display:inline-flex; align-items:center; background:#f3f4f6; border-radius:50px; padding:8px 20px; border:1px solid #e5e7eb;">
            <div class="spinner-grow spinner-grow-sm text-warning" style="margin-right:12px;"></div>
            <span id="gqlTimer">Elapsed: 0s</span>
        </div>
    </div>

    {{-- Error --}}
    <div id="gqlError" style="display:none; padding:20px 24px;">
        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:16px 20px; color:#991b1b;">
            <strong>❌ Scrape Failed:</strong> <span id="gqlErrorMsg"></span>
        </div>
    </div>

    {{-- Tab Buttons (vanilla JS onclick — no Bootstrap tabs) --}}
    <div id="gqlTabs" style="display:none;">
        <div style="display:flex; gap:4px; padding:12px 24px 0; border-bottom:2px solid #e5e7eb;">
            <button class="gql-tab-btn gql-tab-active" onclick="switchGqlTab('tabParsed',this)">📊 Parsed Posts (<span id="tabParsedCount">0</span>)</button>
            <button class="gql-tab-btn" onclick="switchGqlTab('tabRawJson',this)">🔤 Full JSON Response</button>
            <button class="gql-tab-btn" onclick="switchGqlTab('tabDebug',this)">🐛 Debug (Raw GraphQL)</button>
        </div>

        {{-- Tab 1: Parsed Posts --}}
        <div id="tabParsed" class="gql-tab-pane" style="padding:16px 24px;">
            <div style="max-height:calc(100vh - 280px); overflow-y:auto; border:1px solid #e5e7eb; border-radius:8px;">
                <table style="width:100%; border-collapse:collapse; font-size:0.82rem;">
                    <thead style="background:#f8fafc; position:sticky; top:0;">
                        <tr>
                            <th style="padding:10px 12px; border-bottom:2px solid #e5e7eb;">#</th>
                            <th style="padding:10px 12px; border-bottom:2px solid #e5e7eb;">Type</th>
                            <th style="padding:10px 12px; border-bottom:2px solid #e5e7eb; max-width:350px;">Message</th>
                            <th style="padding:10px 12px; border-bottom:2px solid #e5e7eb;">Date</th>
                            <th style="padding:10px 12px; border-bottom:2px solid #e5e7eb; text-align:center;">❤️</th>
                            <th style="padding:10px 12px; border-bottom:2px solid #e5e7eb; text-align:center;">💬</th>
                            <th style="padding:10px 12px; border-bottom:2px solid #e5e7eb; text-align:center;">🔄</th>
                            <th style="padding:10px 12px; border-bottom:2px solid #e5e7eb; text-align:center;">Media</th>
                            <th style="padding:10px 12px; border-bottom:2px solid #e5e7eb; text-align:center;">URL</th>
                        </tr>
                    </thead>
                    <tbody id="parsedPostsBody"></tbody>
                </table>
            </div>
        </div>

        {{-- Tab 2: Full JSON --}}
        <div id="tabRawJson" class="gql-tab-pane" style="display:none; padding:16px 24px;">
            <div style="margin-bottom:8px; text-align:right;">
                <button onclick="copyJson()" style="padding:6px 16px; border:1px solid #374151; background:#fff; border-radius:50px; cursor:pointer; font-weight:600; font-size:0.8rem;">📋 Copy JSON</button>
            </div>
            <pre id="rawJsonPre" style="background:#1e293b; color:#4ade80; padding:16px; border-radius:12px; max-height:calc(100vh - 300px); overflow:auto; font-size:0.75rem; line-height:1.5; white-space:pre-wrap; word-break:break-all;"></pre>
        </div>

        {{-- Tab 3: Debug --}}
        <div id="tabDebug" class="gql-tab-pane" style="display:none; padding:16px 24px;">
            <p style="color:#6b7280; font-size:0.85rem; margin-bottom:8px;">Raw GraphQL response previews (first 3 captured). Shows Facebook's internal API structure.</p>
            <pre id="debugRawPre" style="background:#1e293b; color:#67e8f9; padding:16px; border-radius:12px; max-height:calc(100vh - 300px); overflow:auto; font-size:0.72rem; line-height:1.4; white-space:pre-wrap; word-break:break-all;"></pre>
        </div>
    </div>
</div>

<style>
.post-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important; }
.filter-btn.active { background: #1877F2 !important; color: #fff !important; border-color: #1877F2 !important; }
.comment-input:focus { border-color: #1877F2; box-shadow: 0 0 0 3px rgba(24,119,242,0.15); }
.btn-send-comment:disabled { opacity: 0.5; }
.gql-tab-btn { padding:10px 20px; border:none; background:#e5e7eb; color:#374151; border-radius:8px 8px 0 0; font-weight:700; cursor:pointer; font-size:0.85rem; }
.gql-tab-btn:hover { background:#d1d5db; }
.gql-tab-active { background:#f59e0b !important; color:#fff !important; }
#parsedPostsBody tr:hover { background:#f8fafc; }
#parsedPostsBody td { padding:8px 12px; border-bottom:1px solid #f3f4f6; }
</style>
@endsection

@section('scripts')
<script>
    // ============ GRAPHQL SCRAPE (Own Bot) — Uses plain overlay, no Bootstrap modal ============
    let gqlJsonData = null;

    // Tab switching (vanilla JS)
    function switchGqlTab(tabId, btn) {
        document.querySelectorAll('.gql-tab-pane').forEach(p => p.style.display = 'none');
        document.querySelectorAll('.gql-tab-btn').forEach(b => b.classList.remove('gql-tab-active'));
        document.getElementById(tabId).style.display = 'block';
        btn.classList.add('gql-tab-active');
    }

    function closeGqlOverlay() {
        document.getElementById('gqlOverlay').style.display = 'none';
        document.body.style.overflow = ''; // Re-enable page scroll
    }

    document.querySelectorAll('.btn-gql-scrape').forEach(btn => {
        btn.addEventListener('click', async function() {
            const accountId = this.dataset.accountId;
            const targetId = this.dataset.targetId;
            const accountName = this.dataset.accountName;

            // Show overlay
            const overlay = document.getElementById('gqlOverlay');
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent page scroll behind overlay

            document.getElementById('gqlModalSubtitle').textContent = `Account: ${accountName}`;
            document.getElementById('gqlLoading').style.display = 'block';
            document.getElementById('gqlError').style.display = 'none';
            document.getElementById('gqlTabs').style.display = 'none';
            document.getElementById('gqlStats').style.display = 'none';

            // Timer
            let seconds = 0;
            const timer = setInterval(() => {
                seconds++;
                document.getElementById('gqlTimer').textContent = `Elapsed: ${seconds}s`;
            }, 1000);

            try {
                const res = await fetch(`/target/${targetId}/graphql-scrape/${accountId}`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({ max_scrolls: 6 })
                });

                clearInterval(timer);
                const json = await res.json();
                gqlJsonData = json;

                document.getElementById('gqlLoading').style.display = 'none';

                if (json.success && json.scraper_response) {
                    const sr = json.scraper_response;

                    // Show stats bar
                    document.getElementById('gqlStats').style.display = 'block';
                    document.getElementById('statGqlCount').textContent = sr.stats?.graphql_responses_captured || 0;
                    document.getElementById('statNodeCount').textContent = sr.stats?.raw_post_nodes_found || 0;
                    document.getElementById('statPostCount').textContent = sr.stats?.unique_posts_extracted || 0;
                    document.getElementById('statBotName').textContent = json.bot_used || '-';
                    document.getElementById('statTime').textContent = `${seconds}s`;

                    // Tab 1: Parsed posts table
                    const posts = sr.posts || [];
                    document.getElementById('tabParsedCount').textContent = posts.length;
                    let tableHtml = '';
                    posts.forEach((p, i) => {
                        const msg = (p.message || '').substring(0, 120) + ((p.message || '').length > 120 ? '...' : '');
                        const date = p.created_time ? new Date(p.created_time).toLocaleString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A';
                        const mediaIcon = p.media_url ? `<a href="${p.media_url}" target="_blank" style="color:#22c55e;">🖼️</a>` : '<span style="color:#9ca3af;">—</span>';
                        const urlLink = p.post_url ? `<a href="${p.post_url}" target="_blank" style="color:#3b82f6;">🔗</a>` : '—';
                        const typeBadge = p.post_type === 'photo' ? '<span style="background:#dcfce7;color:#16a34a;padding:2px 8px;border-radius:4px;font-size:0.75rem;">Photo</span>' 
                                        : p.post_type === 'video' ? '<span style="background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:4px;font-size:0.75rem;">Video</span>'
                                        : '<span style="background:#dbeafe;color:#2563eb;padding:2px 8px;border-radius:4px;font-size:0.75rem;">Text</span>';
                        tableHtml += `<tr>
                            <td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;color:#9ca3af;">${i+1}</td>
                            <td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;">${typeBadge}</td>
                            <td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;max-width:350px;word-wrap:break-word;">${msg || '<em style="color:#9ca3af;">No text</em>'}</td>
                            <td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;white-space:nowrap;">${date}</td>
                            <td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;text-align:center;"><strong>${(p.reactions_count || 0).toLocaleString()}</strong></td>
                            <td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;text-align:center;"><strong>${(p.comments_count || 0).toLocaleString()}</strong></td>
                            <td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;text-align:center;"><strong>${(p.shares_count || 0).toLocaleString()}</strong></td>
                            <td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;text-align:center;">${mediaIcon}</td>
                            <td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;text-align:center;">${urlLink}</td>
                        </tr>`;
                    });
                    document.getElementById('parsedPostsBody').innerHTML = tableHtml || '<tr><td colspan="9" style="text-align:center;padding:30px;color:#9ca3af;">No posts parsed from GraphQL responses</td></tr>';

                    // Tab 2: Full JSON
                    document.getElementById('rawJsonPre').textContent = JSON.stringify(json, null, 2);

                    // Tab 3: Debug raw GraphQL
                    const debugData = sr.debug_raw_responses || [];
                    let debugHtml = '';
                    debugData.forEach((d, i) => {
                        debugHtml += `=== Response #${i} (${(d.size_bytes/1024).toFixed(1)}KB) ===\nKeys: ${JSON.stringify(d.keys)}\n\n${d.preview}\n\n`;
                    });
                    debugHtml += `\n=== Captured Endpoints (${(sr.captured_endpoints || []).length}) ===\n`;
                    (sr.captured_endpoints || []).forEach((ep, i) => {
                        debugHtml += `${i+1}. [${ep.size} bytes] ${ep.url}\n`;
                    });
                    document.getElementById('debugRawPre').textContent = debugHtml || 'No GraphQL responses captured.';

                    // Show tabs (default to Parsed tab)
                    document.getElementById('gqlTabs').style.display = 'block';
                    document.querySelectorAll('.gql-tab-pane').forEach(p => p.style.display = 'none');
                    document.getElementById('tabParsed').style.display = 'block';

                } else {
                    // Error
                    document.getElementById('gqlError').style.display = 'block';
                    document.getElementById('gqlErrorMsg').textContent = json.error || 'Unknown error';

                    // Still show raw JSON for debugging
                    document.getElementById('rawJsonPre').textContent = JSON.stringify(json, null, 2);
                    document.getElementById('gqlTabs').style.display = 'block';
                    document.querySelectorAll('.gql-tab-pane').forEach(p => p.style.display = 'none');
                    document.getElementById('tabRawJson').style.display = 'block';
                }

            } catch (err) {
                clearInterval(timer);
                document.getElementById('gqlLoading').style.display = 'none';
                document.getElementById('gqlError').style.display = 'block';
                document.getElementById('gqlErrorMsg').textContent = err.message;
            }
        });
    });

    function copyJson() {
        if (gqlJsonData) {
            navigator.clipboard.writeText(JSON.stringify(gqlJsonData, null, 2));
            alert('JSON copied to clipboard!');
        }
    }
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
