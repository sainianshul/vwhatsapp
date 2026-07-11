{{--
Lightweight DataTables + utility CDN scripts.
Replaces the 2.4MB datatables.bundle.js with individual CDN modules.

Total CDN payload: ~200KB (vs 2,400KB from css bundle)
- DataTables core: ~90KB
- SweetAlert2: ~25KB
- Toastr: ~6KB
- Select2: ~25KB
--}}

{{-- DataTables Core --}}
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

{{-- SweetAlert2 (used for delete confirmations) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

{{-- Toastr (used for success/error notifications) --}}
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

{{-- Flatpickr --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Select2 (used for filter dropdowns) --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- Initialize Select2 on any elements with data-control="select2" --}}
<script>
    $(function () {
        $('[data-control="select2"]').each(function () {
            $(this).select2({
                minimumResultsForSearch: $(this).data('hide-search') === true ? -1 : 10,
                placeholder: $(this).data('placeholder') || '',
                allowClear: $(this).data('allow-clear') === true
            });
        });
    });
</script>