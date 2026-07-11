<!--begin::Javascript-->
<script>var hostUrl = "{{ asset('assets') }}/";</script>

<!--
    PERFORMANCE: Replaced the 2.4MB plugins.bundle.js with CDN-hosted
    jQuery (87KB) + Bootstrap (72KB) = ~160KB total (vs 2,400KB).
    CDN versions are likely already cached in the user's browser.
    All  CSS classes still work — only the heavy KT* JS plugins
    and unused libraries (Flatpickr, FormValidation, es6-shim) are dropped.
-->

<!--begin::jQuery-->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<!--end::jQuery-->

<!--begin::Bootstrap 5.3 Bundle (includes Popper.js)-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!--end::Bootstrap 5.3 Bundle-->

<!--begin::SweetAlert2-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!--end::SweetAlert2-->

<!--begin::Custom Sidebar (lightweight, zero-dependency ~3KB)-->
<script src="{{ asset('js/admin-sidebar.js') }}?v=8"></script>
<!--end::Custom Sidebar-->

{{-- DataTables bundle — only on table pages --}}
@stack('datatables_js')

{{-- Page-specific scripts --}}
@stack('scripts')

<!--begin::Hover Prefetch (makes sidebar navigation feel instant)-->
<script>
(function(){
    // When user hovers on any internal link, prefetch it so click is instant
    var defined = {};
    document.addEventListener('mouseover', function(e) {
        var a = e.target.closest('a[href]');
        if (!a) return;
        var url = a.href;
        // Only prefetch same-origin, non-hash, non-javascript links
        if (a.origin !== location.origin) return;
        if (url.indexOf('#') !== -1 || url.indexOf('javascript:') !== -1) return;
        if (a.target === '_blank') return;
        if (defined[url]) return;
        defined[url] = true;
        var link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        document.head.appendChild(link);
    }, {passive: true});
})();
</script>
<!--end::Hover Prefetch-->

<!--begin::Global Notifications Polling-->
<script>
$(document).ready(function() {
    function fetchNotifications() {
        $.get('/notifications/unread', function(response) {
            if (response.success) {
                const count = response.count;
                const iconContainer = $('#kt_drawer_chat_toggle');
                
                // Update badge
                iconContainer.find('.badge').remove();
                if (count > 0) {
                    iconContainer.append(`<span class="badge badge-circle badge-danger position-absolute top-0 end-0 translate-middle-x mt-1 me-1" style="width: 15px; height: 15px; font-size: 0.65rem;">${count}</span>`);
                }

                // Update dropdown content
                $('#notification-header-count').text(`${count} new`);
                const listContainer = $('#notification-list-container');
                
                if (count === 0) {
                    listContainer.html('<div class="text-center py-5 text-muted">No new notifications</div>');
                } else {
                    let html = '';
                    response.notifications.forEach(notif => {
                        // Assuming notification format from ScrapeCompletedNotification
                        const isSuccess = notif.data.status === 'success';
                        const iconClass = isSuccess ? 'ki-check-circle text-success' : 'ki-cross-circle text-danger';
                        const title = notif.data.message || 'Notification';
                        const date = new Date(notif.created_at).toLocaleString();
                        
                        html += `
                        <div class="d-flex flex-stack py-4 border-bottom border-gray-300">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-35px me-4">
                                    <span class="symbol-label bg-light-${isSuccess ? 'success' : 'danger'}">
                                        <i class="ki-outline ${iconClass} fs-2"></i>
                                    </span>
                                </div>
                                <div class="mb-0 me-2">
                                    <a href="javascript:void(0)" class="fs-6 text-gray-800 text-hover-primary fw-bold">${title}</a>
                                    <div class="text-gray-500 fs-7">${date}</div>
                                </div>
                            </div>
                        </div>`;
                    });
                    listContainer.html(html);
                }
            }
        }).fail(function() {
            // Silently fail if session expires
        });
    }

    // Mark all as read
    $('#mark-all-read-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Keep dropdown open or not
        $.post('/notifications/mark-all-as-read', {
            _token: '{{ csrf_token() }}' // Needs token, but we are in a JS block. Might fail if csrf not available. Let's add it.
        }, function(response) {
            if (response.success) {
                fetchNotifications();
            }
        });
    });

    // Fetch initially
    fetchNotifications();

    // Poll every 15 seconds
    setInterval(fetchNotifications, 15000);
});
</script>
<!--end::Global Notifications Polling-->

<!--end::Javascript-->