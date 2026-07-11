<div class="card card-bordered shadow-sm border-gray-300">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Scraped Posts</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Data extracted from linked accounts.</span>
        </h3>
        <div class="card-toolbar">
            <!-- Future: Add filters here (by account, type, date) -->
            <button class="btn btn-sm btn-light btn-active-light-primary">
                <i class="ki-outline ki-filter fs-4"></i> Filter
            </button>
        </div>
    </div>
    
    <div class="card-body py-3">
        @php
            $allPosts = collect();
            foreach($subject->socialAccounts as $account) {
                $allPosts = $allPosts->merge($account->posts);
            }
            $allPosts = $allPosts->sortByDesc('posted_at');
            $templates = \App\Models\AutomationTemplate::where('created_by_id', auth()->id())->get();
        @endphp

        @if($allPosts->isEmpty())
            <div class="d-flex flex-column align-items-center justify-content-center py-10">
                <i class="ki-outline ki-folder-empty fs-5x text-gray-400 mb-3"></i>
                <h4 class="text-gray-500 fw-semibold">No posts scraped yet.</h4>
                <p class="text-muted fs-7 mt-1 text-center max-w-400px">Once the bots start scraping the linked accounts, the data will appear here.</p>
            </div>
        @else
            <form id="bulkEngageForm" action="{{ route('posts.bulk-engage') }}" method="POST">
                @csrf
                <div class="row g-5 g-xl-8">
                    @foreach($allPosts as $post)
                        <div class="col-xl-4 col-lg-6">
                            <div class="card card-bordered h-100 shadow-sm position-relative post-card">
                                <!-- Checkbox -->
                                <div class="position-absolute top-0 start-0 mt-4 ms-4 z-index-1">
                                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                                        <input class="form-check-input post-checkbox" type="checkbox" name="post_ids[]" value="{{ $post->id }}" />
                                    </div>
                                </div>
                                
                                <div class="card-body d-flex flex-column p-5 pt-12">
                                    <!-- Post Header -->
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="symbol symbol-35px me-3">
                                            @if($post->socialAccount->profile_pic_url)
                                                <img src="{{ $post->socialAccount->profile_pic_url }}" alt="">
                                            @else
                                                <span class="symbol-label bg-light-{{ $post->socialAccount->platform_color }} text-{{ $post->socialAccount->platform_color }}">
                                                    <i class="{{ $post->socialAccount->platform_icon }}"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1">
                                            <a href="{{ $post->socialAccount->account_url }}" target="_blank" class="text-gray-900 text-hover-primary fw-bold fs-6">
                                                {{ $post->socialAccount->account_name }}
                                            </a>
                                            <span class="text-muted fw-semibold fs-8">{{ $post->posted_at ? $post->posted_at->format('d M Y, H:i') : 'Unknown date' }}</span>
                                        </div>
                                        <div>
                                            <span class="badge badge-light-{{ $post->type_color }}">{{ ucfirst($post->post_type) }}</span>
                                        </div>
                                    </div>

                                    <!-- Post Content -->
                                    @if($post->media_url)
                                        <div class="bgi-no-repeat bgi-position-center bgi-size-cover min-h-200px rounded mb-4 border border-gray-200" 
                                             style="background-image: url('{{ $post->media_url }}')">
                                        </div>
                                    @endif
                                    
                                    <div class="text-gray-800 fw-medium fs-6 mb-5 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $post->content ?: 'No text content available.' }}
                                    </div>

                                    <!-- Post Footer (Engagement) -->
                                    <div class="d-flex flex-stack border-top border-gray-200 pt-4 mt-auto">
                                        <div class="d-flex align-items-center gap-4">
                                            <span class="d-flex align-items-center text-gray-600 fs-7 fw-semibold">
                                                <i class="ki-outline ki-heart fs-5 me-1 text-danger"></i> {{ number_format($post->likes_count) }}
                                            </span>
                                            <span class="d-flex align-items-center text-gray-600 fs-7 fw-semibold">
                                                <i class="ki-outline ki-message-text fs-5 me-1 text-primary"></i> {{ number_format($post->comments_count) }}
                                            </span>
                                            <span class="d-flex align-items-center text-gray-600 fs-7 fw-semibold">
                                                <i class="ki-outline ki-send fs-5 me-1 text-info"></i> {{ number_format($post->shares_count) }}
                                            </span>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-light-primary px-3 py-2 btn-quick-reply" data-id="{{ $post->id }}">
                                                <i class="ki-outline ki-message-text-2 fs-5 me-1"></i> Reply
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Floating Action Bar -->
                <div id="bulkActionBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-10 z-index-3 shadow-lg rounded-pill bg-body border border-gray-300 px-6 py-4 d-none" style="min-width: 400px; display: flex; align-items: center; justify-content: space-between;">
                    <div class="d-flex align-items-center">
                        <span class="badge badge-primary rounded-circle w-30px h-30px d-flex align-items-center justify-content-center me-3" id="selectedCount">0</span>
                        <span class="fw-bold text-gray-800 fs-5">Posts Selected</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-light me-2" id="clearSelectionBtn">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#engageModal">
                            💬 Engage Selected
                        </button>
                    </div>
                </div>

                <!-- Engage Modal -->
                <div class="modal fade" id="engageModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header pb-0 border-0 justify-content-end">
                                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                    <i class="ki-outline ki-cross fs-1"></i>
                                </div>
                            </div>
                            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                                <div class="mb-13 text-center">
                                    <h1 class="mb-3">Engage with Posts</h1>
                                    <div class="text-muted fw-semibold fs-5">
                                        Choose how you want to comment on the selected posts.
                                    </div>
                                </div>

                                <!-- Nav tabs -->
                                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold mb-8">
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary active" data-bs-toggle="tab" href="#kt_tab_template">Use AI Template</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary" data-bs-toggle="tab" href="#kt_tab_custom">Custom Text</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <!-- Template Tab -->
                                    <div class="tab-pane fade show active" id="kt_tab_template" role="tabpanel">
                                        <input type="hidden" name="engage_type" id="engageTypeInput" value="template">
                                        
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                <span class="required">Select Comments Template</span>
                                            </label>
                                            <select name="automation_template_id" id="modalTemplateSelect" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Select a Template">
                                                <option value="">Select a template...</option>
                                                @foreach($templates as $template)
                                                    <option value="{{ $template->id }}">{{ $template->name }} ({{ $template->platform }})</option>
                                                @endforeach
                                            </select>
                                            <div class="text-muted fs-7 mt-2">The AI Brain will evaluate the selected posts against this template's rules (keywords, likes) and generate comments.</div>
                                        </div>
                                    </div>

                                    <!-- Custom Tab -->
                                    <div class="tab-pane fade" id="kt_tab_custom" role="tabpanel">
                                        <div class="d-flex flex-column mb-8 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                <span class="required">Custom Comment Text</span>
                                            </label>
                                            <textarea name="custom_text" id="modalCustomText" class="form-control form-control-solid" rows="4" placeholder="Type your exact comment here..."></textarea>
                                            <div class="text-muted fs-7 mt-2">This exact text will be posted on all selected posts, bypassing AI rules. A random delay will be added to prevent spam.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-5">
                                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" id="submitEngageBtn">
                                        <span class="indicator-label">Queue Comments <i class="ki-outline ki-arrow-right ms-2 fs-5"></i></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const checkboxes = $('.post-checkbox');
    const actionBar = $('#bulkActionBar');
    const countBadge = $('#selectedCount');
    const engageTypeInput = $('#engageTypeInput');
    const modalTemplateSelect = $('#modalTemplateSelect');
    const modalCustomText = $('#modalCustomText');

    function updateActionBar() {
        const selectedCount = $('.post-checkbox:checked').length;
        countBadge.text(selectedCount);
        
        if (selectedCount > 0) {
            actionBar.removeClass('d-none');
            // Add a subtle bounce animation when it appears
            if (!actionBar.hasClass('show')) {
                actionBar.addClass('show').css({ opacity: 0, bottom: '-50px' }).animate({ opacity: 1, bottom: '2.5rem' }, 300);
            }
        } else {
            actionBar.animate({ opacity: 0, bottom: '-50px' }, 200, function() {
                actionBar.addClass('d-none').removeClass('show');
            });
        }
    }

    checkboxes.on('change', function() {
        const card = $(this).closest('.post-card');
        if ($(this).is(':checked')) {
            card.addClass('border-primary bg-light-primary');
        } else {
            card.removeClass('border-primary bg-light-primary');
        }
        updateActionBar();
    });

    $('#clearSelectionBtn').on('click', function() {
        checkboxes.prop('checked', false).trigger('change');
    });

    // Quick Reply single post
    $('.btn-quick-reply').on('click', function() {
        const postId = $(this).data('id');
        // Clear all checkboxes
        checkboxes.prop('checked', false).trigger('change');
        // Check only this one
        $(`.post-checkbox[value="${postId}"]`).prop('checked', true).trigger('change');
        // Open modal
        $('#engageModal').modal('show');
    });

    // Tab tracking
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        if (target === '#kt_tab_template') {
            engageTypeInput.val('template');
            modalTemplateSelect.prop('required', true);
            modalCustomText.prop('required', false);
        } else {
            engageTypeInput.val('custom');
            modalTemplateSelect.prop('required', false);
            modalCustomText.prop('required', true);
        }
    });
    
    // Initial setup for required attributes
    modalTemplateSelect.prop('required', true);
    
    // Form submission confirmation
    $('#bulkEngageForm').on('submit', function() {
        $('#submitEngageBtn').prop('disabled', true).html('<span class="indicator-label">Processing... <i class="fas fa-spinner fa-spin ms-2"></i></span>');
    });
});
</script>
@endpush
