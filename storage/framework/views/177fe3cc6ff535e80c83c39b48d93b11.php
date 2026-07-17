<?php $__env->startSection('title', 'Group Assets - ' . $group->name); ?>

<?php $__env->startSection('content'); ?>

    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['label' => 'Media Library', 'url' => route('admin.media_library.index')],
        ['label' => $group->name],
        ['label' => 'Assets'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Media Library', 'url' => route('admin.media_library.index')],
        ['label' => $group->name],
        ['label' => 'Assets'],
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
                    <input type="text" id="dt-search" class="form-control form-control-transparent border border-gray-800 text-gray-900 w-250px ps-11 pe-4 fs-7 fw-semibold shadow-sm" placeholder="Search assets..." />
                </div>

                
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-icon btn-light btn-active-light-primary border border-gray-300 w-35px h-35px" id="refresh-table-btn" data-bs-toggle="tooltip" title="Refresh">
                        <i class="ki-outline ki-arrows-circle fs-3"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-primary fw-semibold btn-flex btn-center" data-bs-toggle="modal" data-bs-target="#upload_asset_modal">
                        <i class="ki-outline ki-file-up fs-4 me-1"></i> Upload Asset
                    </button>
                </div>
            </div>
        </div>

        
        <div class="card-body py-4">
            <?php echo $__env->make('layouts.partials._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div id="assets-table-wrapper" class="table-responsive">
                <table id="assets-table" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 w-100">
                    <thead>
                        <tr class="text-start text-gray-900 fw-medium fs-7 text-uppercase gs-0 border-bottom border-gray-200 border-1">
                            <th class="w-50px">S.No</th>
                            <th class="min-w-200px">Name & Preview</th>
                            <th class="min-w-150px">Asset Code</th>
                            <th class="min-w-100px">Size</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-150px">Created At</th>
                            <th class="text-end min-w-100px pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <?php echo $__env->make('layouts.partials._table-skeleton', ['id' => 'assets-skeleton'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('layouts.partials._table-empty', [
                'id' => 'assets-empty',
                'title' => 'No assets found',
                'subtitle' => 'Upload a media asset to this group to use it in campaigns.',
                'actionUrl' => '#',
                'actionText' => 'Upload Asset',
                'actionAttrs' => 'data-bs-toggle="modal" data-bs-target="#upload_asset_modal"'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <!-- Upload Asset Modal -->
    <div class="modal fade" id="upload_asset_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form id="uploadAssetForm" action="<?php echo e(route('admin.media_library.assets.store', $group->id)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h2 class="fw-bold">Upload Media Asset</h2>
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                            <i class="ki-outline ki-information-5 fs-2hx text-primary me-4"></i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-primary">Dynamic Asset Code</h4>
                                <span>System will auto-generate a unique code from the name you enter. You will paste this code in your CSV file.</span>
                            </div>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold mb-2">Asset Name (Readable)</label>
                            <input type="text" class="form-control form-control-solid" name="name" placeholder="e.g. Rohtak Promo Video" required />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold mb-2">Media File</label>
                            <input type="file" class="form-control form-control-solid" name="file" required accept="image/*,video/*,application/pdf" />
                            <div class="text-muted fs-7 mt-2">Max file size is 16MB. Allowed types: Images, Videos, PDFs.</div>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="uploadBtn">
                            <span class="indicator-label">Upload Asset</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Asset Modal -->
    <div class="modal fade" id="edit_asset_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form id="editAssetForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-header">
                        <h2 class="fw-bold">Edit Media Asset</h2>
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <i class="ki-outline ki-cross fs-1"></i>
                        </div>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold mb-2">Asset Name</label>
                            <input type="text" id="editAssetName" class="form-control form-control-solid" name="name" required />
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('datatables_css'); ?>
    <?php echo $__env->make('layouts.partials._datatable-cdn-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('datatables_js'); ?>
    <?php echo $__env->make('layouts.partials._datatable-cdn-js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        let table;
        $(function () {
            table = $('#assets-table').DataTable({
                serverSide: true,
                processing: false,
                ajax: {
                    url: '<?php echo e(route('admin.media_library.groups.show', $group->id)); ?>',
                },
                columns: [
                    { data: null, name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false, searchable: false },
                    { data: 'preview', name: 'preview', orderable: false, searchable: false },
                    { data: 'asset_code_html', name: 'asset_code_html', orderable: false, searchable: false },
                    { data: 'size', name: 'size', orderable: false, searchable: false },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end pe-3' },
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
                    info: 'Showing _START_–_END_ of _TOTAL_ assets',
                    infoEmpty: 'No assets to show',
                    infoFiltered: '(filtered from _MAX_)',
                    lengthMenu: 'Show _MENU_',
                    paginate: {
                        previous: '<i class="ki-duotone ki-arrow-left"></i>',
                        next: '<i class="ki-duotone ki-arrow-right"></i>',
                    },
                },
                initComplete: function () {
                    $('#assets-skeleton').fadeOut(200, function () {
                        $(this).remove();
                    });
                },
                drawCallback: function () {
                    let total = this.api().page.info().recordsDisplay;
                    if (total === 0) {
                        $('#assets-table-wrapper').addClass('d-none');
                        $('#assets-empty').removeClass('d-none');
                    } else {
                        $('#assets-empty').addClass('d-none');
                        $('#assets-table-wrapper').removeClass('d-none');
                    }
                    $('[data-bs-toggle="tooltip"]').tooltip({ trigger: 'hover' });
                }
            });

            let searchTimer;
            $('#dt-search').on('input', function () {
                clearTimeout(searchTimer);
                let query = $(this).val();
                searchTimer = setTimeout(function () {
                    table.search(query).draw();
                }, 400);
            });

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
        });

        // Copy functionality
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                toastr.success("Code copied to clipboard: " + text);
            }, function(err) {
                toastr.error("Failed to copy code");
            });
        }

        // Toggle Status functionality
        function toggleStatus(assetId, isActive) {
            let status = isActive ? 'active' : 'inactive';
            
            fetch(`/media-library/assets/${assetId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success("Asset status updated.");
                } else {
                    toastr.error("Failed to update status.");
                    table.ajax.reload(null, false); // Reload to reset switch state on error
                }
            })
            .catch(err => {
                toastr.error("Error connecting to server.");
                table.ajax.reload(null, false); // Reload to reset switch state on error
            });
        }

        // Edit Asset functionality
        function editAsset(id, name) {
            $('#editAssetName').val(name);
            $('#editAssetForm').attr('action', `/media-library/assets/${id}`);
            var modal = new bootstrap.Modal(document.getElementById('edit_asset_modal'));
            modal.show();
        }

        // Loader on form submit
        document.getElementById('uploadAssetForm').addEventListener('submit', function() {
            let btn = document.getElementById('uploadBtn');
            btn.setAttribute('data-kt-indicator', 'on');
            btn.disabled = true;
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/media_library/show.blade.php ENDPATH**/ ?>