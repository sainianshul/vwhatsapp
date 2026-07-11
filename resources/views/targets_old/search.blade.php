@extends('layouts.adminlte')

@section('title', 'Discover Targets')
@section('page_title', 'Discover Targets')
@section('page_subtitle', 'Find and track public figures, politicians, and influencers.')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Search Box -->
        <div class="card mb-5 border-0" style="background: linear-gradient(135deg, #ffffff, #f8fafc); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05);">
            <div class="card-body p-5">
                <div class="text-center mb-5">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-radar" style="font-size: 36px;"></i>
                    </div>
                    <h3 class="fw-bold text-dark">Radar Search</h3>
                    <p class="text-muted" style="font-size: 1.1rem;">Deploy our AI scraper to locate social footprints across networks.</p>
                </div>
                
                <form id="searchForm">
                    <div class="search-wrapper position-relative mx-auto" style="max-width: 700px;">
                        <input 
                            type="text" 
                            name="query" 
                            id="searchQuery"
                            class="form-control form-control-lg shadow-sm" 
                            placeholder="Enter person's name (e.g. Narendra Modi)..." 
                            required 
                            minlength="2"
                            style="border-radius: 20px; padding: 20px 25px 20px 60px; font-size: 1.2rem; border: 2px solid #e2e8f0; transition: all 0.3s;"
                        >
                        <i class="bi bi-search position-absolute text-muted" style="left: 25px; top: 50%; transform: translateY(-50%); font-size: 1.4rem;"></i>
                        <button 
                            type="submit" 
                            class="btn btn-primary position-absolute px-4 py-3 shadow" 
                            id="searchBtn"
                            style="right: 8px; top: 8px; bottom: 8px; border-radius: 14px; font-size: 1.1rem;"
                        >
                            <i class="bi bi-lightning-charge-fill me-2"></i>Scan Network
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Loading Indicator (hidden by default) -->
        <div class="card mb-5 border-0 d-none glass-card" id="loadingCard" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
            <div class="card-body text-center py-5">
                <div class="position-relative d-inline-block mb-4">
                    <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 4px;" role="status">
                        <span class="visually-hidden">Searching...</span>
                    </div>
                    <i class="bi bi-facebook position-absolute text-primary bg-white rounded-circle" style="top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 1.5rem; padding: 2px;"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Analyzing Social Graph...</h4>
                <p class="text-muted mb-3" style="font-size: 1.1rem;">Bypassing detection and extracting intelligence.</p>
                
                <div class="d-inline-flex align-items-center bg-light rounded-pill px-4 py-2 text-muted fw-medium shadow-sm border">
                    <div class="spinner-grow spinner-grow-sm text-success me-3" role="status"></div>
                    <span id="loadingTimer">Time elapsed: 0s</span>
                </div>
            </div>
        </div>

        <!-- Error container -->
        <div class="d-none mb-4" id="errorContainer">
            <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center p-4" style="background: #fef2f2; color: #991b1b;">
                <i class="bi bi-exclamation-octagon-fill fs-3 me-3 text-danger"></i>
                <div>
                    <h6 class="fw-bold mb-1">Mission Failed</h6>
                    <span id="errorMessage"></span>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>

        <!-- Results Header -->
        <div class="d-none mb-4" id="resultsHeader">
            <div class="d-flex justify-content-between align-items-end border-bottom pb-3">
                <div>
                    <span class="text-uppercase fw-bold text-primary mb-1 d-block" style="letter-spacing: 1px; font-size: 0.8rem;">Intelligence Gathered</span>
                    <h3 class="fw-bold text-dark mb-0">
                        <span id="resultsCount" class="text-primary">0</span> Matches for "<span id="resultsQuery"></span>"
                    </h3>
                </div>
            </div>
        </div>

        <!-- Results Container -->
        <div id="resultsContainer" class="row gx-4"></div>

        <!-- No Results -->
        <div class="d-none" id="noResults">
            <div class="card border-0 shadow-sm" style="background: #f8fafc;">
                <div class="card-body text-center py-5">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-4" style="width: 100px; height: 100px;">
                        <i class="bi bi-search text-muted opacity-50" style="font-size: 40px;"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Ghost Protocol</h4>
                    <p class="text-muted" style="font-size: 1.1rem;">No traces found for this identity. Try an alternative spelling.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Search Input Focus Effects
const searchInput = document.getElementById('searchQuery');
searchInput.addEventListener('focus', function() {
    this.style.borderColor = 'var(--primary)';
    this.style.boxShadow = '0 0 0 4px rgba(79, 70, 229, 0.1)';
});
searchInput.addEventListener('blur', function() {
    this.style.borderColor = '#e2e8f0';
    this.style.boxShadow = 'none';
});

document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const query = document.getElementById('searchQuery').value.trim();
    if (query.length < 2) return;

    // UI State
    document.getElementById('loadingCard').classList.remove('d-none');
    document.getElementById('errorContainer').classList.add('d-none');
    document.getElementById('resultsHeader').classList.add('d-none');
    document.getElementById('resultsContainer').innerHTML = '';
    document.getElementById('noResults').classList.add('d-none');

    const btn = document.getElementById('searchBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Scanning...';

    // Timer
    let seconds = 0;
    const timer = setInterval(() => {
        seconds++;
        document.getElementById('loadingTimer').textContent = `Time elapsed: ${seconds}s`;
    }, 1000);

    try {
        const response = await fetch("{{ route('target.search') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ query: query }),
        });

        clearInterval(timer);
        const data = await response.json();

        if (data.success && data.results && data.results.length > 0) {
            // Show search method badge
            const methodBadge = data.method === 'graphql_intercept' 
                ? '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 ms-3"><i class="bi bi-shield-check me-1"></i>GraphQL Direct</span>'
                : '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 ms-3"><i class="bi bi-globe me-1"></i>Web Search</span>';

            document.getElementById('resultsCount').textContent = data.results.length;
            document.getElementById('resultsQuery').innerHTML = query + methodBadge;
            document.getElementById('resultsHeader').classList.remove('d-none');

            let html = '';
            data.results.forEach((result, index) => {
                const initial = (result.name || '?').charAt(0).toUpperCase();
                
                // Profile picture with fallback
                const profilePic = result.profilePic 
                    ? `<img src="${result.profilePic}" alt="${result.name}" class="rounded-circle shadow-sm" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #fff;" onerror="this.outerHTML='<div class=\\'rounded-circle shadow-sm d-flex align-items-center justify-content-center\\' style=\\'width:80px;height:80px;background:linear-gradient(135deg,#4f46e5,#ec4899);color:white;font-size:32px;font-weight:700;border:3px solid #fff;\\'>${initial}</div>'">`
                    : `<div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:linear-gradient(135deg,#4f46e5,#ec4899);color:white;font-size:32px;font-weight:700;border:3px solid #fff;">${initial}</div>`;

                // Verified badge
                const verifiedBadge = result.verified 
                    ? '<svg class="ms-1" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M22.5 12.5c0-1.58-.875-2.95-2.148-3.6.154-.435.238-.905.238-1.4 0-2.21-1.71-3.998-3.818-3.998-.47 0-.92.084-1.336.25C14.818 2.415 13.51 1.5 12 1.5s-2.816.917-3.437 2.25c-.415-.165-.866-.25-1.336-.25-2.11 0-3.818 1.79-3.818 4 0 .494.083.964.237 1.4-1.272.65-2.147 2.018-2.147 3.6 0 1.495.782 2.798 1.942 3.486-.02.17-.032.34-.032.514 0 2.21 1.708 4 3.818 4 .47 0 .92-.086 1.335-.25.62 1.334 1.926 2.25 3.437 2.25 1.512 0 2.818-.916 3.437-2.25.415.163.865.248 1.336.248 2.11 0 3.818-1.79 3.818-4 0-.174-.012-.344-.033-.513 1.158-.687 1.943-1.99 1.943-3.484zm-6.616-3.334l-4.334 6.5c-.145.217-.382.334-.625.334-.143 0-.288-.04-.416-.126l-.115-.094-2.415-2.415c-.293-.293-.293-.768 0-1.06s.768-.294 1.06 0l1.77 1.767 3.825-5.74c.23-.345.696-.436 1.04-.207.346.23.44.696.21 1.04z"/></svg>'
                    : '';

                // Account type badge with color coding
                let typeBadgeClass = 'bg-primary bg-opacity-10 text-primary border-primary';
                let typeIcon = 'bi-flag';
                if (result.accountType === 'group') {
                    typeBadgeClass = 'bg-success bg-opacity-10 text-success border-success';
                    typeIcon = 'bi-people';
                } else if (result.accountType === 'user' || result.accountType === 'profile') {
                    typeBadgeClass = 'bg-info bg-opacity-10 text-info border-info';
                    typeIcon = 'bi-person';
                }
                
                const typeBadge = result.accountType && result.accountType !== 'unknown'
                    ? `<span class="badge ${typeBadgeClass} border border-opacity-25 ms-1" style="font-size:0.7rem;"><i class="bi ${typeIcon} me-1"></i>${result.accountType}</span>`
                    : '';

                // Category pill
                const categoryPill = result.category 
                    ? `<span class="badge bg-light text-muted border mb-2" style="font-size:0.75rem;"><i class="bi bi-tag me-1"></i>${result.category}</span>`
                    : '';

                // Followers display
                const followers = result.followers
                    ? `<div class="d-inline-flex align-items-center text-dark fw-medium bg-light rounded-pill px-3 py-1 mb-2" style="font-size:0.85rem;"><i class="bi bi-people-fill text-primary me-2"></i>${result.followers}</div>`
                    : '';

                const desc = result.description 
                    ? result.description.substring(0, 120) + (result.description.length > 120 ? '...' : '')
                    : '';

                // Extract username from URL for display
                let username = '';
                try {
                    const urlObj = new URL(result.url);
                    username = urlObj.pathname.replace(/^\//, '').replace(/\/$/, '');
                    if (username && !username.includes('/')) username = '@' + username;
                    else username = '';
                } catch(e) {}

                html += `
                <div class="col-md-6 mb-4 fade-in-up" style="animation-delay: ${index * 0.08}s">
                    <div class="card h-100 border-0 target-card shadow-sm">
                        <div class="card-body p-4 position-relative">
                            <div class="position-absolute top-0 end-0 p-3">
                                <i class="bi bi-facebook" style="font-size:24px; color:#1877F2; opacity:0.8;"></i>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3 position-relative">
                                    ${profilePic}
                                    ${result.verified ? '<div class="position-absolute" style="bottom:2px;right:2px;background:#fff;border-radius:50%;line-height:0;padding:1px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#1877F2"><path d="M22.5 12.5c0-1.58-.875-2.95-2.148-3.6.154-.435.238-.905.238-1.4 0-2.21-1.71-3.998-3.818-3.998-.47 0-.92.084-1.336.25C14.818 2.415 13.51 1.5 12 1.5s-2.816.917-3.437 2.25c-.415-.165-.866-.25-1.336-.25-2.11 0-3.818 1.79-3.818 4 0 .494.083.964.237 1.4-1.272.65-2.147 2.018-2.147 3.6 0 1.495.782 2.798 1.942 3.486-.02.17-.032.34-.032.514 0 2.21 1.708 4 3.818 4 .47 0 .92-.086 1.335-.25.62 1.334 1.926 2.25 3.437 2.25 1.512 0 2.818-.916 3.437-2.25.415.163.865.248 1.336.248 2.11 0 3.818-1.79 3.818-4 0-.174-.012-.344-.033-.513 1.158-.687 1.943-1.99 1.943-3.484zm-6.616-3.334l-4.334 6.5c-.145.217-.382.334-.625.334-.143 0-.288-.04-.416-.126l-.115-.094-2.415-2.415c-.293-.293-.293-.768 0-1.06s.768-.294 1.06 0l1.77 1.767 3.825-5.74c.23-.345.696-.436 1.04-.207.346.23.44.696.21 1.04z"/></svg></div>' : ''}
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center flex-wrap gap-1" style="line-height:1.4;">
                                        ${result.name || 'Unknown'}${verifiedBadge} ${typeBadge}
                                    </h5>
                                    ${username ? `<span class="text-muted small">${username}</span>` : ''}
                                    <div class="mt-1">
                                        <a href="${result.url || '#'}" target="_blank" class="text-primary text-decoration-none small d-inline-flex align-items-center">
                                            View Profile <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.65rem;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                ${followers}
                                ${categoryPill}
                            </div>
                            
                            ${desc ? `<p class="text-muted mb-3" style="font-size:0.9rem; line-height: 1.5;">${desc}</p>` : ''}
                            
                            <div class="mt-auto pt-3 border-top">
                                <form class="add-target-form m-0 w-100">
                                    <input type="hidden" name="name" value="${(result.name || '').replace(/"/g, '&quot;')}">
                                    <input type="hidden" name="photo_url" value="${result.profilePic || ''}">
                                    <input type="hidden" name="accounts[0][platform]" value="facebook">
                                    <input type="hidden" name="accounts[0][url]" value="${result.url || ''}">
                                    <input type="hidden" name="accounts[0][name]" value="${(result.name || '').replace(/"/g, '&quot;')}">
                                    <input type="hidden" name="accounts[0][followers]" value="${result.followers || ''}">
                                    <input type="hidden" name="accounts[0][accountType]" value="${result.accountType || ''}">
                                    <input type="hidden" name="accounts[0][profilePic]" value="${result.profilePic || ''}">
                                    <button type="submit" class="btn btn-primary w-100 py-2 btn-add shadow-sm">
                                        <i class="bi bi-person-plus-fill me-2"></i> Track This Target
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>`;
            });

            document.getElementById('resultsContainer').innerHTML = html;
            attachAddTargetListeners();

        } else if (data.success && (!data.results || data.results.length === 0)) {
            document.getElementById('noResults').classList.remove('d-none');
        } else {
            document.getElementById('errorMessage').textContent = data.error || 'Unknown error';
            document.getElementById('errorContainer').classList.remove('d-none');
        }

    } catch (error) {
        clearInterval(timer);
        document.getElementById('errorMessage').textContent = 'Network error: ' + error.message;
        document.getElementById('errorContainer').classList.remove('d-none');
    } finally {
        document.getElementById('loadingCard').classList.add('d-none');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-lightning-charge-fill me-2"></i>Scan Network';
    }
});

function attachAddTargetListeners() {
    const forms = document.querySelectorAll('.add-target-form');
    forms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button');
            const originalHtml = btn.innerHTML;
            
            // Button transition to loading state
            btn.disabled = true;
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-secondary');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Initiating tracking...';

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            const payload = {
                name: data.name,
                photo_url: data.photo_url,
                accounts: [{
                    platform: data['accounts[0][platform]'],
                    url: data['accounts[0][url]'],
                    name: data['accounts[0][name]'],
                    followers: data['accounts[0][followers]'],
                    accountType: data['accounts[0][accountType]'],
                    profilePic: data['accounts[0][profilePic]'],
                }]
            };

            try {
                const response = await fetch("{{ route('target.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                
                const result = await response.json();
                
                if (result.success) {
                    btn.classList.remove('btn-secondary');
                    btn.classList.add('btn-success');
                    btn.innerHTML = '<i class="bi bi-check2-all me-2"></i> Target Acquired!';
                    
                    // Add success animation class
                    const card = this.closest('.card');
                    card.style.transform = 'scale(1.02)';
                    card.style.borderColor = '#10b981';
                    
                    setTimeout(() => {
                        window.location.href = "{{ route('target.index') }}";
                    }, 1200);
                } else {
                    alert('Error: ' + (result.message || 'Failed to add target'));
                    btn.disabled = false;
                    btn.classList.remove('btn-secondary');
                    btn.classList.add('btn-primary');
                    btn.innerHTML = originalHtml;
                }
            } catch (error) {
                alert('Network Error: ' + error.message);
                btn.disabled = false;
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-primary');
                btn.innerHTML = originalHtml;
            }
        });
    });
}
</script>

<style>
    .target-card {
        border-radius: 24px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .target-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15) !important;
    }
    .btn-add {
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
        opacity: 0;
    }
</style>
@endsection
