<?php $__env->startSection('title', 'Profile Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-column flex-column-fluid">
    
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Profile Details','description' => 'View intelligence and tracking data']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Profile Details','description' => 'View intelligence and tracking data']); ?>
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
                    ['label' => 'Profiles', 'url' => route('subjects.index')],
                    ['label' => $subject->name],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['label' => 'Profiles', 'url' => route('subjects.index')],
                    ['label' => $subject->name],
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
                <a href="<?php echo e(route('subjects.index')); ?>" class="btn btn-sm btn-light fw-semibold">
                    <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Back
                </a>
                <a href="<?php echo e(route('subjects.edit', $subject)); ?>" class="btn btn-sm btn-light-primary border border-primary fw-bold shadow-sm">
                    <i class="ki-outline ki-pencil fs-4 me-1"></i>Edit Profile
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            <?php if (isset($component)) { $__componentOriginal9d684e94d8294933a712f81f8101e557 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d684e94d8294933a712f81f8101e557 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert-success','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert-success'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d684e94d8294933a712f81f8101e557)): ?>
<?php $attributes = $__attributesOriginal9d684e94d8294933a712f81f8101e557; ?>
<?php unset($__attributesOriginal9d684e94d8294933a712f81f8101e557); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d684e94d8294933a712f81f8101e557)): ?>
<?php $component = $__componentOriginal9d684e94d8294933a712f81f8101e557; ?>
<?php unset($__componentOriginal9d684e94d8294933a712f81f8101e557); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal3d251db05a714ec6b2f220d3dccfef6d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-errors','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-errors'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d)): ?>
<?php $attributes = $__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d; ?>
<?php unset($__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3d251db05a714ec6b2f220d3dccfef6d)): ?>
<?php $component = $__componentOriginal3d251db05a714ec6b2f220d3dccfef6d; ?>
<?php unset($__componentOriginal3d251db05a714ec6b2f220d3dccfef6d); ?>
<?php endif; ?>

            <!--begin::Navbar-->
            <div class="card card-bordered border-gray-300 mb-5 mb-xl-10 shadow-sm">
                <div class="card-body pt-9 pb-0">
                    <!--begin::Details-->
                    <div class="d-flex flex-wrap flex-sm-nowrap">
                        <!--begin: Pic-->
                        <div class="me-7 mb-4">
                            <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative border border-gray-300 rounded overflow-hidden">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subject->photo_url): ?>
                                    <img src="<?php echo e(Storage::url($subject->photo_url)); ?>" alt="image" style="object-fit:cover;" />
                                <?php else: ?>
                                    <span class="symbol-label bg-light-primary text-primary fs-2x fw-bold">
                                        <?php echo e(strtoupper(substr($subject->name, 0, 1))); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-<?php echo e($subject->status_color); ?> rounded-circle border border-4 border-body h-20px w-20px"></div>
                            </div>
                        </div>
                        <!--end::Pic-->

                        <!--begin::Info-->
                        <div class="flex-grow-1">
                            <!--begin::Title-->
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <!--begin::User-->
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-2">
                                        <h1 class="text-gray-900 fs-1 fw-bold me-2"><?php echo e($subject->name); ?></h1>
                                        <span class="badge badge-light-<?php echo e($subject->status_color); ?> border border-<?php echo e($subject->status_color); ?> fw-semibold px-3 py-1 me-2">
                                            <?php echo e(ucfirst($subject->status)); ?>

                                        </span>
                                    </div>

                                    <div class="d-flex flex-wrap fw-medium fs-6 mb-4 pe-2 gap-5">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subject->designation): ?>
                                            <span class="d-flex align-items-center text-gray-600">
                                                <i class="ki-outline ki-briefcase fs-4 me-2 text-gray-500"></i>
                                                <?php echo e($subject->designation); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <span class="d-flex align-items-center text-gray-600">
                                            <i class="ki-outline ki-user fs-4 me-2 text-gray-500"></i>
                                            Created by <?php echo e($subject->creator->name ?? 'System'); ?>

                                        </span>
                                    </div>
                                </div>
                                <!--end::User-->

                            </div>
                            <!--end::Title-->

                            <!--begin::Stats-->
                            <div class="d-flex flex-wrap flex-stack mt-4">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-column flex-grow-1 pe-8">
                                    <div class="d-flex flex-wrap">
                                        
                                        <div class="border border-gray-300 rounded min-w-125px py-2 px-4 me-4 mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="ki-outline ki-profile-user fs-3 text-primary me-2"></i>
                                                <div class="fs-4 fw-bold text-gray-900"><?php echo e($subject->accounts_count); ?></div>
                                            </div>
                                            <div class="fw-medium fs-8 text-gray-600 text-uppercase">Social Accounts</div>
                                        </div>

                                        <div class="border border-gray-300 rounded min-w-125px py-2 px-4 me-4 mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="ki-outline ki-message-text-2 fs-3 text-success me-2"></i>
                                                <div class="fs-4 fw-bold text-gray-900"><?php echo e($subject->total_posts_count); ?></div>
                                            </div>
                                            <div class="fw-medium fs-8 text-gray-600 text-uppercase">Total Posts Scraped</div>
                                        </div>

                                        <div class="border border-gray-300 rounded min-w-125px py-2 px-4 me-4 mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="ki-outline ki-time fs-3 text-warning me-2"></i>
                                                <div class="fs-5 fw-bold text-gray-900">
                                                    <?php echo e($subject->created_at->format('d M, Y')); ?>

                                                </div>
                                            </div>
                                            <div class="fw-medium fs-8 text-gray-600 text-uppercase">Tracking Since</div>
                                        </div>

                                    </div>
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Stats-->
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Details-->

                    <!--begin::Navs-->
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-6 fw-semibold mt-6" role="tablist">
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4 active" data-bs-toggle="tab" href="#tab_home" role="tab">
                                <i class="ki-outline ki-home fs-5 me-2"></i>Overview
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#tab_accounts" role="tab">
                                <i class="ki-outline ki-profile-user fs-5 me-2"></i>Linked Accounts (<?php echo e($subject->accounts_count); ?>)
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#tab_posts" role="tab">
                                <i class="ki-outline ki-message-text-2 fs-5 me-2"></i>Posts Data
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#tab_notes" role="tab">
                                <i class="ki-outline ki-notepad fs-5 me-2"></i>Internal Notes
                            </a>
                        </li>
                    </ul>
                    <!--begin::Navs-->
                </div>
            </div>
            <!--end::Navbar-->

            <!--begin::Tab Content-->
            <div class="tab-content" id="subjectTabContent">
                
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="tab_home" role="tabpanel">
                    <?php echo $__env->make('subjects.tabs.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Accounts Tab -->
                <div class="tab-pane fade" id="tab_accounts" role="tabpanel">
                    <?php echo $__env->make('subjects.tabs.accounts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Posts Tab -->
                <div class="tab-pane fade" id="tab_posts" role="tabpanel">
                    <?php echo $__env->make('subjects.tabs.posts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Notes Tab -->
                <div class="tab-pane fade" id="tab_notes" role="tabpanel">
                    <?php echo $__env->make('subjects.tabs.notes', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

            </div>
            <!--end::Tab Content-->

        </div>
    </div>
</div>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Tab persistency logic (optional, can be added if needed)
    // To keep it simple, we just use standard bootstrap tabs
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/subjects/show.blade.php ENDPATH**/ ?>