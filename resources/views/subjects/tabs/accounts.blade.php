<div class="card card-bordered shadow-sm border-gray-300">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Linked Social Accounts</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Digital footprints associated with this profile.</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ route('subjects.accounts.create', $subject) }}" class="btn btn-sm btn-primary fw-semibold">
                <i class="ki-outline ki-plus fs-4"></i> Add Account
            </a>
        </div>
    </div>
    
    <div class="card-body py-3">
        <div class="table-responsive">
            <table class="table align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 min-w-200px rounded-start">Account / Network</th>
                        <th class="min-w-125px">URL</th>
                        <th class="min-w-125px">Followers</th>
                        <th class="min-w-150px">Last Scraped</th>
                        <th class="min-w-100px text-end rounded-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subject->socialAccounts as $account)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px me-3">
                                        @if($account->profile_pic_url)
                                            <img src="{{ $account->profile_pic_url }}" class="border border-gray-300" alt="pic">
                                        @else
                                            <span class="symbol-label bg-light-{{ $account->platform_color }} text-{{ $account->platform_color }} fw-bold fs-3">
                                                <i class="{{ $account->platform_icon }} fs-2"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <a href="{{ $account->account_url }}" target="_blank" class="text-gray-900 text-hover-primary fw-bold fs-6 mb-1">
                                            {{ $account->account_name }}
                                            @if($account->verified)
                                                <i class="ki-outline ki-verify text-primary fs-6 ms-1" title="Verified"></i>
                                            @endif
                                        </a>
                                        <div class="fs-7 text-muted">
                                            <span class="badge badge-light-{{ $account->platform_color }} px-2 py-1">{{ ucfirst($account->platform) }}</span>
                                            {{ $account->account_type ? '- ' . ucfirst($account->account_type) : '' }}
                                            
                                            @if($account->automationRule && $account->automationRule->is_active)
                                                <span class="badge badge-light-success px-2 py-1 ms-2" data-bs-toggle="tooltip" title="Auto-Engage is ON ({{ $account->automationRule->template->name ?? 'Unknown Template' }})">
                                                    <i class="ki-outline ki-check-circle text-success fs-8 me-1"></i> Auto-Engage ON
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ $account->account_url }}" target="_blank" class="text-gray-600 text-hover-primary fs-7 text-truncate d-inline-block" style="max-width: 150px;">
                                    {{ $account->account_url }}
                                </a>
                            </td>
                            <td>
                                <span class="text-gray-900 fw-bold d-block fs-7">{{ $account->followers_count ? number_format($account->followers_count) : 'N/A' }}</span>
                            </td>
                            <td id="status-col-{{ $account->id }}">
                                @if($account->scrape_status === 'scraping')
                                    <span class="badge badge-light-primary"><i class="fas fa-spinner fa-spin text-primary me-2"></i> Scraping...</span>
                                @elseif($account->last_scraped_at)
                                    <span class="text-gray-900 fw-bold d-block fs-7 last-scraped-text">{{ $account->last_scraped_at->diffForHumans() }}</span>
                                    <span class="text-muted fw-semibold d-block fs-8 posts-found-text">{{ $account->posts()->count() }} posts found</span>
                                @else
                                    <span class="badge badge-light-warning">Pending Scrape</span>
                                @endif
                            </td>
                            <td class="text-end pe-4" id="action-col-{{ $account->id }}">
                                @if($account->scrape_status !== 'scraping')
                                    <button type="button" class="btn btn-sm btn-icon btn-light-success border border-success rounded-circle w-30px h-30px sync-scrape-btn me-2" data-id="{{ $account->id }}" title="Sync Scrape (Wait for completion)">
                                        <i class="ki-outline ki-cloud-download fs-5"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-light-primary border border-primary rounded-circle w-30px h-30px scrape-account-btn me-2" data-id="{{ $account->id }}" title="Async Scrape (Background)">
                                        <i class="ki-outline ki-arrows-circle fs-5"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-icon btn-light-primary border border-primary rounded-circle w-30px h-30px me-2" disabled title="Scraping in progress">
                                        <i class="fas fa-spinner fa-spin fs-5"></i>
                                    </button>
                                @endif
                                
                                <button type="button" class="btn btn-sm btn-icon btn-light-info border border-info rounded-circle w-30px h-30px me-2" data-bs-toggle="modal" data-bs-target="#autoEngageModal{{ $account->id }}" title="Auto-Engage Settings">
                                    <i class="ki-outline ki-robot fs-5"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger border border-danger rounded-circle w-30px h-30px remove-account-btn" data-id="{{ $account->id }}" title="Unlink Account">
                                    <i class="ki-outline ki-trash fs-5"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ki-outline ki-disconnect fs-4x text-gray-400 mb-3"></i>
                                    <h4 class="text-gray-600 fw-bold mb-0">No Accounts Linked</h4>
                                    <p class="text-gray-500 fs-7 mt-1">Connect a social footprint to begin scraping data.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Auto-Engage Modals -->
@foreach($subject->socialAccounts as $account)
<div class="modal fade" id="autoEngageModal{{ $account->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form class="form" action="{{ route('automation-rules.store', $account) }}" method="POST">
                    @csrf
                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Auto-Engage Settings</h1>
                        <div class="text-muted fw-semibold fs-5">
                            Automate interactions for <a href="{{ $account->account_url }}" class="fw-bold link-primary" target="_blank">{{ $account->account_name }}</a>
                        </div>
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Select Comments Template</span>
                        </label>
                        <select name="automation_template_id" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Select a Template" required>
                            <option value="">Select a template...</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ ($account->automationRule?->automation_template_id === $template->id) ? 'selected' : '' }}>
                                    {{ $template->name }} ({{ $template->platform }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Sync Interval (Hours)</span>
                        </label>
                        <select name="sync_interval_hours" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                            <option value="1" {{ ($account->automationRule?->sync_interval_hours == 1) ? 'selected' : '' }}>Every 1 hour (Aggressive)</option>
                            <option value="6" {{ ($account->automationRule?->sync_interval_hours == 6 || !$account->automationRule) ? 'selected' : '' }}>Every 6 hours (Standard)</option>
                            <option value="12" {{ ($account->automationRule?->sync_interval_hours == 12) ? 'selected' : '' }}>Every 12 hours</option>
                            <option value="24" {{ ($account->automationRule?->sync_interval_hours == 24) ? 'selected' : '' }}>Once a day</option>
                        </select>
                        <div class="text-muted fs-7 mt-2">How often the engine will check for new posts to engage with.</div>
                    </div>

                    <div class="d-flex flex-stack mb-8">
                        <div class="me-5">
                            <label class="fs-6 fw-semibold">Enable Auto-Engage</label>
                            <div class="fs-7 fw-semibold text-muted">Toggle the automation engine for this account</div>
                        </div>
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ ($account->automationRule?->is_active) ? 'checked' : '' }}/>
                        </label>
                    </div>

                    <div class="text-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Save Settings</span>
                        </button>
                    </div>
                </form>
                
                @if($account->automationRule)
                <div class="text-center mt-5 pt-5 border-top border-gray-300">
                    <form action="{{ route('automation-rules.destroy', $account) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light-danger" onclick="return confirm('Completely remove Auto-Engage for this account? (Does not delete past logs)')">
                            Turn Off & Remove Rule
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
$(document).ready(function() {
    // 10s Polling for accounts currently scraping
    setInterval(function() {
        $('.scrape-account-btn').each(function() {
            // We only poll if the button is disabled (which means it's currently scraping)
            // Wait, the logic above replaces the button when scraping.
            // Let's poll all rows that have the "Scraping..." badge
        });

        // Better approach: poll accounts that are currently scraping
        $('td[id^="status-col-"]').each(function() {
            const col = $(this);
            if (col.html().includes('Scraping...')) {
                const id = col.attr('id').replace('status-col-', '');
                
                $.get(`/social-accounts/${id}/status`, function(response) {
                    if (response.success && response.status !== 'scraping') {
                        // Refresh page or update row to show completed state
                        window.location.reload(); 
                    }
                });
            }
        });
    }, 10000);

    // Handle Scrape Account
    $('.scrape-account-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');
        const statusCol = $(`#status-col-${id}`);
        const actionCol = $(`#action-col-${id}`);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin fs-5"></i>');
        statusCol.html('<span class="badge badge-light-primary"><i class="fas fa-spinner fa-spin text-primary me-2"></i> Scraping...</span>');

        $.ajax({
            url: `/social-accounts/${id}/scrape`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (!response.success) {
                    alert(response.message);
                    window.location.reload();
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'An error occurred while starting scrape.');
                window.location.reload();
            }
        });
    });

    // Handle Sync Scrape Account
    $('.sync-scrape-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');
        const statusCol = $(`#status-col-${id}`);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin fs-5"></i>');
        $('.scrape-account-btn[data-id="' + id + '"]').prop('disabled', true);
        statusCol.html('<span class="badge badge-light-success"><i class="fas fa-spinner fa-spin text-success me-2"></i> Syncing... (Please wait)</span>');

        console.log(`[Scraper] Starting synchronous scrape for account ID: ${id}`);

        $.ajax({
            url: `/social-accounts/${id}/sync-scrape`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log(`[Scraper] Synchronous scrape complete:`, response);
                if (!response.success) {
                    alert('Scrape failed: ' + (response.log?.message || response.message));
                } else {
                    alert(response.log?.message || 'Scraping successful!');
                }
                window.location.reload();
            },
            error: function(xhr) {
                console.error(`[Scraper] Synchronous scrape error:`, xhr);
                alert(xhr.responseJSON?.message || 'An error occurred while scraping.');
                window.location.reload();
            }
        });
    });

    // Handle Unlink Account
    $('.remove-account-btn').on('click', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        
        if (confirm('Are you sure you want to unlink this account? Scraped posts associated with it will also be deleted.')) {
            $.ajax({
                url: `/social-accounts/${id}`,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                            // Optional: Check if table is empty and show empty state
                            window.location.reload(); // Quickest way to refresh stats
                        });
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while unlinking the account.');
                }
            });
        }
    });
});
</script>
@endpush
