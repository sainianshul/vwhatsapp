


<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


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
</script><?php /**PATH /var/www/resources/views/layouts/partials/_datatable-cdn-js.blade.php ENDPATH**/ ?>