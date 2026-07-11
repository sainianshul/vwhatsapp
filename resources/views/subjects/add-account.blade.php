@extends('layouts.app')

@section('title', 'Link Social Account')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <x-page-header title="Link Social Account" description="Discover and attach a digital footprint to {{ $subject->name }}" />
                <x-breadcrumb :items="[
                    ['label' => 'Profiles', 'url' => route('subjects.index')],
                    ['label' => $subject->name, 'url' => route('subjects.show', $subject)],
                    ['label' => 'Link Account'],
                ]" />
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('subjects.show', $subject) }}" class="btn btn-sm btn-light fw-semibold">
                    <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Back to Profile
                </a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            <!-- Search Card -->
            <div class="card card-bordered shadow-sm border-gray-300 mb-5 mb-xl-10">
                <div class="card-body p-9 text-center">
                    <i class="ki-outline ki-radar fs-5x text-primary mb-4"></i>
                    <h2 class="text-dark fw-bold mb-3">AI Deep Search</h2>
                    <p class="text-gray-500 fs-6 mb-8 max-w-600px mx-auto">
                        Enter a name, username, or direct profile URL. Our intelligence bots will scan the network to locate the digital footprint.
                    </p>

                    <form id="search-form" class="position-relative w-100 max-w-600px mx-auto mb-5" onsubmit="event.preventDefault(); performSearch();">
                        <i class="ki-outline ki-magnifier fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-4"></i>
                        <input type="text" id="search-query" class="form-control form-control-solid form-control-lg ps-12 border-primary" placeholder="e.g. {{ $subject->name }} or https://facebook.com/..." required minlength="2">
                        <button type="submit" class="btn btn-primary position-absolute top-0 end-0 bottom-0 px-6" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                            Scan Network
                        </button>
                    </form>

                    <div class="text-muted fs-7">
                        Supported Platforms: <i class="ki-outline ki-facebook text-primary fs-5 ms-1 me-2"></i> Facebook
                    </div>
                </div>
            </div>

            <!-- Loader (Hidden initially) -->
            <div id="search-loader" class="d-none card card-bordered shadow-sm border-gray-300 mb-5 mb-xl-10" style="background: rgba(255,255,255,0.9);">
                <div class="card-body p-10 text-center">
                    <div class="spinner-border text-primary w-50px h-50px mb-5" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h3 class="fw-bold text-gray-900 mb-2" id="loader-title">Initializing bots...</h3>
                    <p class="text-gray-500 fs-6 mb-5" id="loader-subtitle">Bypassing detection protocols.</p>
                    
                    <div class="d-flex flex-column align-items-center w-100 max-w-400px mx-auto">
                        <div class="d-flex justify-content-between w-100 fs-7 fw-semibold mb-2">
                            <span class="text-gray-600">Scan Progress</span>
                            <span class="text-primary" id="loader-percentage">0%</span>
                        </div>
                        <div class="h-8px w-100 bg-light-primary rounded">
                            <div class="bg-primary rounded h-8px" role="progressbar" id="loader-progress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Section (Hidden initially) -->
            <div id="results-container" class="d-none">
                <h3 class="fw-bold text-gray-900 mb-5">Scan Results <span class="badge badge-light-primary ms-2" id="results-count">0</span></h3>
                
                <div class="row g-5 g-xl-8" id="results-grid">
                    <!-- Results injected here via JS -->
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .animation-blink {
        animation: blink 1s linear infinite;
    }
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
</style>
@endpush

@push('scripts')
<script>
    const subjectId = {{ $subject->id }};
    
    function performSearch() {
        const query = $('#search-query').val();
        if (!query) return;

        // Reset UI
        $('#results-container').addClass('d-none');
        $('#scraping-loader').addClass('d-none');
        $('#search-loader').removeClass('d-none');
        
        // Fake Progress Animation for Search
        let progress = 0;
        $('#loader-progress').css('width', '0%');
        $('#loader-percentage').text('0%');
        $('#loader-title').text('Scanning Facebook Graph...');
        $('#loader-subtitle').text('Locating potential matches based on query.');

        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90; // Hold at 90% until ajax returns
            
            $('#loader-progress').css('width', `${progress}%`);
            $('#loader-percentage').text(`${Math.round(progress)}%`);
        }, 300);

        // Make AJAX call to our search service
        $.ajax({
            url: "{{ route('social-accounts.search') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                query: query
            },
            success: function(response) {
                clearInterval(interval);
                $('#loader-progress').css('width', '100%');
                $('#loader-percentage').text('100%');
                
                setTimeout(() => {
                    $('#search-loader').addClass('d-none');
                    renderResults(response.results);
                }, 500);
            },
            error: function(xhr) {
                clearInterval(interval);
                $('#search-loader').addClass('d-none');
                alert('Search failed. The scraper service might be offline.');
            }
        });
    }

    function renderResults(results) {
        const grid = $('#results-grid');
        grid.empty();
        $('#results-container').removeClass('d-none');
        $('#results-count').text(results.length || 0);

        if (!results || results.length === 0) {
            grid.html(`
                <div class="col-12 text-center py-10">
                    <i class="ki-outline ki-cross-circle fs-4x text-gray-400 mb-3"></i>
                    <h4 class="text-gray-600 fw-bold">No results found</h4>
                    <p class="text-muted">Try a different name or paste a direct URL.</p>
                </div>
            `);
            return;
        }

        results.forEach(item => {
            const followersText = item.followers ? `<span class="badge badge-light-primary fw-bold px-3 py-2 me-2 mb-2"><i class="ki-outline ki-profile-user fs-5 me-1 text-primary"></i> ${item.followers}</span>` : '';
            const categoryText = item.category ? `<span class="badge badge-light-info fw-bold px-3 py-2 mb-2"><i class="ki-outline ki-tag fs-5 me-1 text-info"></i> ${item.category}</span>` : '';
            const descriptionText = item.description ? `<p class="text-muted fs-8 mb-4 text-start" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">${item.description}</p>` : '';

            // Generate a card for each result
            const html = `
                <div class="col-md-6 col-xl-4">
                    <div class="card card-bordered shadow-sm h-100 border-hover-primary transition-all">
                        <div class="card-body p-5 d-flex flex-column text-center">
                            <div class="symbol symbol-75px mb-3 mx-auto">
                                ${item.profilePic ? 
                                    `<img src="${item.profilePic}" alt="pic" class="border border-gray-300">` : 
                                    `<span class="symbol-label bg-light-primary text-primary fs-2x fw-bold"><i class="ki-outline ki-facebook"></i></span>`
                                }
                            </div>
                            <a href="${item.url}" target="_blank" class="text-gray-900 text-hover-primary fs-4 fw-bold mb-1 text-truncate">
                                ${item.name}
                                ${item.verified ? '<i class="ki-outline ki-verify text-primary fs-5 ms-1"></i>' : ''}
                            </a>
                            <span class="text-muted fs-7 fw-semibold mb-3">${item.accountType ? item.accountType.toUpperCase() : 'PROFILE'}</span>
                            
                            <div class="d-flex justify-content-center flex-wrap mb-3">
                                ${followersText}
                                ${categoryText}
                            </div>
                            
                            ${descriptionText}

                            <div class="mt-auto pt-3">
                                <button class="btn btn-light-primary w-100 link-account-btn" 
                                    data-url="${item.url}" 
                                    data-name="${item.name}"
                                    data-type="${item.accountType || 'profile'}"
                                    data-pic="${item.profilePic || ''}">
                                    <i class="ki-outline ki-link fs-4"></i> Link Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            grid.append(html);
        });

        // Attach events to new buttons
        $('.link-account-btn').click(function() {
            linkAccount(this);
        });
    }

    function linkAccount(btn) {
        const data = $(btn).data();
        const originalHtml = $(btn).html();
        
        // Disable button and show spinner
        $(btn).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Linking...');

        // Make actual AJAX call to store the account
        $.ajax({
            url: "{{ route('social-accounts.store') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                subject_id: subjectId,
                platform: 'facebook',
                account_name: data.name,
                account_url: data.url,
                account_type: data.type,
                profile_pic_url: data.pic
            },
            success: function(response) {
                // Success - turn button green
                $(btn).removeClass('btn-light-primary').addClass('btn-success')
                      .html('<i class="ki-outline ki-check fs-4"></i> Linked');
                
                // Optional: show a small toast notification here
                toastr.success('Account linked successfully!');
            },
            error: function(xhr) {
                // Restore button
                $(btn).prop('disabled', false).html(originalHtml);
                
                let errorMsg = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors)[0][0];
                }
                toastr.error(errorMsg);
            }
        });
    }
</script>
@endpush
