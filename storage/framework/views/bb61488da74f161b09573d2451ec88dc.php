<?php $__env->startSection('title', $bot->name); ?>

<?php $__env->startSection('content'); ?>

    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Bot Details','description' => 'View automation bot profile']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Bot Details','description' => 'View automation bot profile']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['label' => 'Bots', 'url' => route('bots.index')],
                    ['label' => $bot->name],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['label' => 'Bots', 'url' => route('bots.index')],
                    ['label' => $bot->name],
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
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="<?php echo e(route('bots.index')); ?>" class="btn btn-sm btn-light fw-semibold">
                    <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Back
                </a>
                <a href="<?php echo e(route('bots.edit', $bot->id)); ?>" class="btn btn-sm btn-primary fw-semibold">
                    <i class="ki-outline ki-pencil fs-4 me-1"></i>Edit
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            <!--begin::Overview Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-body pt-9 pb-0">
                    
                    <div class="d-flex flex-wrap flex-sm-nowrap">
                        <!--begin: Pic-->
                        <div class="me-7 mb-4">
                            <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                                <span class="symbol-label bg-light-<?php echo e($bot->platform_color); ?> border border-<?php echo e($bot->platform_color); ?> border-dashed text-<?php echo e($bot->platform_color); ?> fs-1 fw-bold">
                                    <i class="<?php echo e($bot->platform_icon); ?> fs-3x"></i>
                                </span>
                            </div>
                        </div>
                        <!--end::Pic-->

                        <!--begin::Info-->
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="text-gray-900 fs-2 fw-bold me-1"><?php echo e($bot->name); ?></span>
                                        <span class="badge badge-light-<?php echo e($bot->status_color); ?> fw-bold ms-2"><?php echo e(ucfirst($bot->status)); ?></span>
                                    </div>
                                    <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                        <span class="d-flex align-items-center text-gray-500 me-5 mb-2">
                                            <i class="ki-outline ki-profile-circle fs-4 me-1"></i>
                                            <?php echo e($bot->platform_username ?? 'No Username'); ?>

                                        </span>
                                        <span class="d-flex align-items-center text-gray-500 me-5 mb-2">
                                            <i class="ki-outline ki-geolocation fs-4 me-1"></i>
                                            <?php echo e($bot->proxy ?? 'Direct IP'); ?>

                                        </span>
                                        <span class="d-flex align-items-center text-gray-500 mb-2">
                                            <i class="ki-outline ki-calendar fs-4 me-1"></i>
                                            Added <?php echo e($bot->created_at->format('d M, Y')); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap flex-stack">
                                <div class="d-flex flex-column flex-grow-1 pe-8">
                                    <div class="d-flex flex-wrap">
                                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="fs-2 fw-bold text-gray-900"><?php echo e(ucfirst($bot->type)); ?></div>
                                            </div>
                                            <div class="fw-semibold fs-6 text-gray-500">Bot Type</div>
                                        </div>
                                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="fs-2 fw-bold text-gray-900"><?php echo e(number_format($bot->total_actions_count)); ?></div>
                                            </div>
                                            <div class="fw-semibold fs-6 text-gray-500">Total Actions</div>
                                        </div>
                                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="fs-2 fw-bold text-<?php echo e($bot->platform_status_color); ?>"><?php echo e(\App\Models\Bot::getPlatformStatusList()[$bot->platform_status] ?? ucfirst($bot->platform_status)); ?></div>
                                            </div>
                                            <div class="fw-semibold fs-6 text-gray-500">Platform Status</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--end::Info-->
                    </div>

                    <!--begin::Navs-->
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold mt-6" id="bot_tabs">
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5 active" data-bs-toggle="tab" href="#kt_tab_overview">Overview</a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_tab_activity">Activity Log</a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_tab_scraping">Scraping History</a>
                        </li>
                        <li class="nav-item mt-2">
                            <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_tab_actions">Action History</a>
                        </li>
                    </ul>
                    <!--end::Navs-->

                </div>
            </div>
            <!--end::Overview Card-->

            <!--begin::Tab Content-->
            <div class="tab-content">
                
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="kt_tab_overview" role="tabpanel">
                    <div class="row g-5 g-xxl-8">
                        <div class="col-xl-6">
                            
                            <!-- Cookie Status Card -->
                            <div class="card mb-5 mb-xxl-8 shadow-none border border-gray-300">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 text-gray-900">Authentication</span>
                                    </h3>
                                    <div class="card-toolbar">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bot->has_cookie): ?>
                                            <span class="badge badge-light-success fw-bold">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-light-danger fw-bold">Missing</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body pt-3">
                                    <div class="d-flex flex-column text-gray-600 mb-5">
                                        <span class="fw-semibold fs-6">Last Updated:</span>
                                        <span class="fw-bold fs-5 text-gray-900"><?php echo e($bot->cookie_updated_at ? $bot->cookie_updated_at->format('d M Y, h:i A') : 'Never'); ?></span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-light-primary border border-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_update_cookie">
                                            Update Cookie
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light-success border border-success" onclick="checkBotHealth(this)">
                                            <span class="indicator-label">Check Health</span>
                                            <span class="indicator-progress">Checking... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Details Card -->
                            <div class="card mb-5 mb-xxl-8 shadow-none border border-gray-300">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 text-gray-900">Platform Details</span>
                                    </h3>
                                </div>
                                <div class="card-body pt-3">
                                    <div class="row mb-5">
                                        <label class="col-lg-4 fw-semibold text-gray-500">Username</label>
                                        <div class="col-lg-8"><span class="fw-bold text-gray-900"><?php echo e($bot->platform_username ?? '-'); ?></span></div>
                                    </div>
                                    <div class="row mb-5">
                                        <label class="col-lg-4 fw-semibold text-gray-500">User ID</label>
                                        <div class="col-lg-8"><span class="fw-bold text-gray-900"><?php echo e($bot->platform_user_id ?? '-'); ?></span></div>
                                    </div>
                                    <div class="row mb-5">
                                        <label class="col-lg-4 fw-semibold text-gray-500">User Agent</label>
                                        <div class="col-lg-8">
                                            <div class="bg-light rounded p-3 text-gray-900 fs-7" style="word-break: break-all;">
                                                <?php echo e($bot->user_agent ?? 'Default System Agent'); ?>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-5">
                                        <label class="col-lg-4 fw-semibold text-gray-500">Created By</label>
                                        <div class="col-lg-8"><span class="fw-bold text-gray-900"><?php echo e($bot->creator->name ?? 'System'); ?></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <!-- Notes Card -->
                            <div class="card mb-5 mb-xxl-8 shadow-none border border-gray-300">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3 text-gray-900">Notes</span>
                                    </h3>
                                </div>
                                <div class="card-body pt-3">
                                    <div class="text-gray-900 fs-6">
                                        <?php echo nl2br(e($bot->notes ?: 'No notes available.')); ?>

                                    </div>
                                </div>
                            </div>

                            <?php if (isset($component)) { $__componentOriginald04b9949d0dada8faa8863322f9b06a8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04b9949d0dada8faa8863322f9b06a8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.comments','data' => ['type' => ''.e(\App\Models\Comment::TYPE_BOT).'','modelId' => $bot->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('comments'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => ''.e(\App\Models\Comment::TYPE_BOT).'','model-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bot->id)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04b9949d0dada8faa8863322f9b06a8)): ?>
<?php $attributes = $__attributesOriginald04b9949d0dada8faa8863322f9b06a8; ?>
<?php unset($__attributesOriginald04b9949d0dada8faa8863322f9b06a8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04b9949d0dada8faa8863322f9b06a8)): ?>
<?php $component = $__componentOriginald04b9949d0dada8faa8863322f9b06a8; ?>
<?php unset($__componentOriginald04b9949d0dada8faa8863322f9b06a8); ?>
<?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Activity Log Tab -->
                <div class="tab-pane fade" id="kt_tab_activity" role="tabpanel">
                    <?php echo $__env->make('bots.tabs.activity', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Scraping History Tab -->
                <div class="tab-pane fade" id="kt_tab_scraping" role="tabpanel">
                    <?php echo $__env->make('bots.tabs.scraping-history', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Action History Tab -->
                <div class="tab-pane fade" id="kt_tab_actions" role="tabpanel">
                    <?php echo $__env->make('bots.tabs.action-history', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

            </div>
            <!--end::Tab Content-->

        </div>
    </div>
    <!--end::Content-->

    <!-- Modal: Update Cookie -->
    <div class="modal fade" tabindex="-1" id="kt_modal_update_cookie">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Update Bot Cookie</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <form action="<?php echo e(route('bots.cookie.update', $bot->id)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-5">
                            <label class="form-label fw-bold">Upload JSON File</label>
                            <input type="file" name="cookie_file" class="form-control" accept=".json,.txt" />
                            <div class="text-muted fs-8 mt-1">Exported from Puppeteer/EditThisCookie.</div>
                        </div>
                        <div class="separator separator-dashed my-5"></div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">Or Paste Raw Cookie Data</label>
                            <textarea name="cookie" class="form-control font-monospace" rows="4" placeholder="[{...}]"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Cookie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function checkBotHealth(btn) {
    btn.setAttribute('data-kt-indicator', 'on');
    btn.disabled = true;

    fetch('<?php echo e(route('bots.health-check', $bot->id)); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                text: data.message,
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            }).then(function () {
                location.reload();
            });
        } else {
            Swal.fire({
                text: data.message,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
        }
    })
    .catch(error => {
        let msg = "An unexpected error occurred.";
        Swal.fire({
            text: msg,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
                confirmButton: "btn btn-danger"
            }
        });
    })
    .finally(() => {
        btn.removeAttribute('data-kt-indicator');
        btn.disabled = false;
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/bots/show.blade.php ENDPATH**/ ?>