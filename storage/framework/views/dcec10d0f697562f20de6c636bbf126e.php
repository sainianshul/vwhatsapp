<?php $__env->startSection('title', 'Login History'); ?>

<?php $__env->startSection('content'); ?>

    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['label' => 'System'],
        ['label' => 'Login History'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'System'],
        ['label' => 'Login History'],
    ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>

    <div class="card shadow-sm border border-gray-200">

        
        <div class="card-header border-bottom border-gray-200 pt-5 pb-4">

            <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-3">

                
                <div class="d-flex align-items-center position-relative">

                    <i class="ki-duotone ki-magnifier fs-5 text-gray-900 position-absolute ms-4 z-index-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>

                    <input
                        type="text"
                        id="dt-search"
                        class="form-control form-control-transparent border border-gray-800 text-gray-900 w-250px ps-11 pe-4 fs-7 fw-semibold shadow-sm"
                        placeholder="Search users..."
                    />

                </div>

                
                <div class="d-flex align-items-center gap-2">

                    
                    <div style="width: 145px;">

                        <div class="position-relative">

                            <i class="ki-duotone ki-filter fs-5 text-gray-900 position-absolute top-50 start-0 translate-middle-y ms-4 z-index-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>

                            <select
                                id="filter-status"
                                class="form-select form-select-transparent border border-gray-800 text-gray-900 form-select-sm fw-semibold ps-11 shadow-sm"
                                data-control="select2"
                                data-placeholder="All Status"
                                data-allow-clear="true"
                                data-hide-search="true"
                            >

                                <option></option>

                                <option value="1">
                                    Success
                                </option>

                                <option value="0">
                                    Failed
                                </option>

                            </select>

                        </div>

                    </div>



                    
                    <button
                        type="button"
                        id="btn-empty-logs"
                        class="btn btn-sm btn-light-danger fw-semibold border border-danger-subtle shadow-sm px-4"
                    >

                        <i class="ki-duotone ki-trash-square fs-5 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>

                        Empty Logs

                    </button>

                </div>

            </div>

        </div>

        
        <div class="card-body py-4">

            <div id="login-history-table-wrapper" class="table-responsive">

                <table
                    id="login-history-table"
                    class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 w-100"
                >

                    <thead>

                        <tr class="text-start text-gray-500 fw-bold fs-9 text-uppercase gs-0 border-bottom border-gray-200 border-1">

                            <th class="w-50px">
                                S.No
                            </th>

                            <th class="min-w-300px">
                                User
                            </th>

                            <th class="min-w-180px">
                                Device
                            </th>

                            <th class="min-w-150px">
                                IP Address
                            </th>

                            <th class="min-w-130px">
                                Status
                            </th>

                            <th class="min-w-140px">
                                Login Time
                            </th>

                            <th class="text-end min-w-100px pe-3">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody></tbody>

                </table>

            </div>

            <?php echo $__env->make('layouts.partials._table-skeleton', [
                'id' => 'login-history-skeleton',
                'columns' => 6
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('layouts.partials._table-empty', [
                'id' => 'login-history-empty'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </div>

    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('datatables_css'); ?>

    <?php echo $__env->make('layouts.partials._datatable-cdn-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopPush(); ?>

<?php $__env->startPush('datatables_js'); ?>

    <?php echo $__env->make('layouts.partials._datatable-cdn-js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>

        $(function () {

            var table = $('#login-history-table').DataTable({
                serverSide: true,
                processing: false,
                ajax: {
                    url: '<?php echo e(route('login_history.index')); ?>',
                    data: function (d) {
                        d.status = $('#filter-status').val();
                    }
                },

                columns: [
                    {
                        data: null,
                        name: 'id',
                        render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; },
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'user',
                        name: 'user',
                        orderable: false
                    },

                    {
                        data: 'device',
                        name: 'device',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'ip',
                        name: 'ip',
                        searchable: false
                    },

                    {
                        data: 'status_badge',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'logged_in_at',
                        name: 'logged_in_at'
                    },

                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-end pe-3'
                    },
                ],

                order: [[5, 'desc']],
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
                dom:
                    "<'row'<'col-12'tr>>" +
                    "<'row align-items-center mt-3 pt-3 flex-nowrap'" +
                    "<'col-sm-12 col-md-5'i>" +
                    "<'col-sm-12 col-md-7 d-flex justify-content-md-end align-items-center gap-3'lp>>",

                language: {
                    emptyTable: ' ',
                    zeroRecords: ' ',
                    loadingRecords: ' ',
                    info: 'Showing _START_–_END_ of _TOTAL_ login records',
                    infoEmpty: 'No login records to show',
                    infoFiltered: '(filtered from _MAX_)',
                    lengthMenu: 'Show _MENU_',
                    paginate: {
                        previous: '<i class="ki-duotone ki-arrow-left"></i>',
                        next: '<i class="ki-duotone ki-arrow-right"></i>',
                    },
                },

                initComplete: function () {
                    $('#login-history-skeleton').fadeOut(200, function () {
                        $(this).remove();
                    });
                },

                drawCallback: function () {

                    var total = this.api().page.info().recordsDisplay;

                    if (total === 0) {

                        $('#login-history-table-wrapper').addClass('d-none');

                        $('#login-history-empty').removeClass('d-none');

                    } else {

                        $('#login-history-empty').addClass('d-none');

                        $('#login-history-table-wrapper').removeClass('d-none');
                    }
                }
            });

            // ── Search ─────────────────────────────────────────────
            var searchTimer;

            $('#dt-search').on('input', function () {

                clearTimeout(searchTimer);

                var q = $(this).val();

                searchTimer = setTimeout(function () {

                    table.search(q).draw();

                }, 400);
            });

            // ── Filter ─────────────────────────────────────────────
            $('#filter-status').on('change', function () {

                table.ajax.reload();
            });



            // ── Empty Logs ─────────────────────────────────────────
            $('#btn-empty-logs').on('click', function () {

                Swal.fire({
                    title: 'Empty Login History?',
                    text: 'All login history records will be permanently removed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Empty Logs',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-light ms-2'
                    },
                    buttonsStyling: false,
                }).then(function (result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.post('<?php echo e(route('login_history.empty')); ?>', {

                        _token: '<?php echo e(csrf_token()); ?>'

                    })

                    .done(function () {

                        table.ajax.reload();

                        toastr.success('Login history cleared successfully.');

                    })

                    .fail(function () {

                        toastr.error('Something went wrong.');
                    });
                });
            });

        });

    </script>

<?php $__env->stopPush(); ?>




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/users/login_history.blade.php ENDPATH**/ ?>