<?php $__env->startSection('title', 'Users'); ?>

<?php $__env->startSection('content'); ?>

    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => 'All Users'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => 'All Users'],
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

    <div class="card shadow-sm">

        
        <div class="card-header border-0 pt-5 pb-3">
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
        placeholder="Search by patient name, email or phone..."
    />

</div>
                
                <div class="d-flex align-items-center gap-2">

                    
                    <button type="button" class="btn btn-icon btn-light btn-active-light-primary border border-gray-300 w-35px h-35px" id="refresh-table-btn" data-bs-toggle="tooltip" title="Refresh">
                        <i class="ki-outline ki-arrows-circle fs-3"></i>
                    </button>

                    
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\User::getStatusList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value); ?>">
                    <?php echo e($label); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </select>

    </div>
</div>

                    
                    <div style="width: 145px;">
                        <div class="position-relative">
                            <i class="ki-duotone ki-filter fs-5 text-gray-900 position-absolute top-50 start-0 translate-middle-y ms-4 z-index-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <select
                                id="filter-role"
                                class="form-select form-select-transparent border border-gray-800 text-gray-900 form-select-sm fw-semibold ps-11 shadow-sm"
                                data-control="select2"
                                data-placeholder="All Roles"
                                data-allow-clear="true"
                                data-hide-search="true"
                            >
                                <option></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\User::getRoleList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>">
                                        <?php echo e($label); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                    </div>

                    
                    <a
                        href="<?php echo e(route('users.create')); ?>"
                        class="btn btn-sm btn-primary fw-semibold btn-flex btn-center"
                    >
                        <i class="ki-duotone ki-plus-square fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Add User
                    </a>

                </div>
            </div>
        </div>

        
        <div class="card-body py-4">

            <div id="users-table-wrapper" class="table-responsive">

                <table
                    id="users-table"
                    class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 w-100"
                >
                    <thead>
                        <tr class="text-start text-gray-900 fw-medium fs-7 text-uppercase gs-0 border-bottom border-gray-200 border-1">

                            <th class="w-50px">S.No</th>

                            <th class="min-w-250px">
                                User
                            </th>

                            <th class="min-w-120px">
                                Status
                            </th>

                            <th class="min-w-120px">
                                Role
                            </th>

                            <th class="min-w-150px">
                                Last Login
                            </th>

                            <th class="min-w-140px">
                                Joined
                            </th>

                            <th class="text-end min-w-120px pe-3">
                                Actions
                            </th>

                        </tr>
                    </thead>

                    <tbody></tbody>

                </table>
            </div>

            <?php echo $__env->make('layouts.partials._table-skeleton', [
                'id' => 'users-skeleton'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('layouts.partials._table-empty', [
                'id' => 'users-empty',
                'title' => 'No users found',
                'subtitle' => 'There are no users matching your criteria. Add a new user to get started.',
                'actionUrl' => route('users.create'),
                'actionText' => 'Add User'
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

            // ── Init ──────────────────────────────────────────────────────────
            let table = $('#users-table').DataTable({
                serverSide: true,
                processing: false,

                ajax: {
                    url: '<?php echo e(route('users.index')); ?>',
                    data: function (d) {
                        d.status = $('#filter-status').val();
                        d.role = $('#filter-role').val();
                    }
                },

                columns: [
                    { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },

                    { data: 'name', name: 'name' },

                    { data: 'status', name: 'status' },

                    { data: 'role_badge', name: 'role' },

                    { data: 'last_login_at', name: 'last_login_at' },

                    { data: 'created_at', name: 'created_at' },

                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-end pe-3'
                    },
                ],

                order: [[4, 'desc']],

                pageLength: 15,

                lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],

                // ──  Bootstrap 5 DOM ────────────────────────────────
                dom:
                    "<'row'<'col-12'tr>>" +
                    "<'row align-items-center mt-3 pt-3 flex-nowrap'" +
                    "<'col-sm-12 col-md-5'i>" +
                    "<'col-sm-12 col-md-7 d-flex justify-content-md-end align-items-center gap-3'lp>>",

                language: {
                    emptyTable: ' ',
                    zeroRecords: ' ',
                    loadingRecords: ' ',

                    info: 'Showing _START_–_END_ of _TOTAL_ patients',

                    infoEmpty: 'No patients to show',

                    infoFiltered: '(filtered from _MAX_)',

                    lengthMenu: 'Show _MENU_',

                    paginate: {
                        previous: '<i class="ki-duotone ki-arrow-left"></i>',
                        next: '<i class="ki-duotone ki-arrow-right"></i>',
                    },
                },



                initComplete: function () {
                    $('#users-skeleton').fadeOut(200, function () {
                        $(this).remove();
                    });
                },

                drawCallback: function () {
                    let total = this.api().page.info().recordsDisplay;

                    if (total === 0) {
                        $('#users-table-wrapper').addClass('d-none');
                        $('#users-empty').removeClass('d-none');
                    } else {
                        $('#users-empty').addClass('d-none');
                        $('#users-table-wrapper').removeClass('d-none');
                    }

                    $('[data-bs-toggle="tooltip"]').tooltip({
                        trigger: 'hover'
                    });
                }
            });

            // ── Search ───────────────────────────────────────────────────────
            let searchTimer;

            $('#dt-search').on('input', function () {
                clearTimeout(searchTimer);
                let query = $(this).val();

                searchTimer = setTimeout(function () {
                    table.search(query).draw();
                }, 400);
            });

            // ── Status Filter ────────────────────────────────────────────────
            $('#filter-status, #filter-role').on('change', function () {
                table.draw();
            });

            // ── Refresh Button ───────────────────────────────────────────────
            $('#refresh-table-btn').on('click', function () {
                $(this).tooltip('hide');
                table.ajax.reload(null, false);
                Swal.fire({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 1500,
                    icon: 'success',
                    title: 'Data refreshed successfully'
                });
            });



            // ── Delete ───────────────────────────────────────────────────────
            $(document).on('click', '.btn-delete', function () {
                let url = $(this).data('url');

                Swal.fire({
                    title: 'Delete User?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-light ms-2'
                    },
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.post(url, {
                            _method: 'DELETE',
                            _token: '<?php echo e(csrf_token()); ?>'
                        })
                        .done(function () {
                            table.ajax.reload(null, false);
                            Swal.fire({ toast: true, position: 'top', showConfirmButton: false, timer: 1500, icon: 'success', title: 'User deleted successfully' });
                        })
                        .fail(function (xhr) {
                            toastr.error('Something went wrong.');
                        });
                        toastr.error('Something went wrong.');
                    });
                });
            });

        });
    </script>
<?php $__env->stopPush(); ?>




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/users/index.blade.php ENDPATH**/ ?>