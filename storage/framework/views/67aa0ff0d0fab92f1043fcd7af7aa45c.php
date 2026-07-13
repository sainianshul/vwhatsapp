<?php $__env->startSection('title', 'Account Details'); ?>

<?php $__env->startSection('content'); ?>

    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['label' => 'WhatsApp Accounts', 'url' => route('whatsapp_accounts.index')],
        ['label' => 'Account Details'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'WhatsApp Accounts', 'url' => route('whatsapp_accounts.index')],
        ['label' => 'Account Details'],
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

    <div class="row g-5 g-xl-10 mt-5">
        <!-- Left Profile Section -->
        <div class="col-xl-4">
            <div class="card shadow-sm border-0 mb-5 mb-xl-8 hover-elevate-up">
                <div class="card-body pt-15">
                    <div class="d-flex flex-center flex-column mb-5">
                        <div class="symbol symbol-100px symbol-circle mb-7 shadow-sm border border-light">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->status === 'connected'): ?>
                                <span class="symbol-label bg-light-success text-success fs-1 fw-bolder">
                                    <?php echo e(strtoupper(substr($account->name ?? $account->push_name ?? 'W', 0, 1))); ?>

                                </span>
                            <?php else: ?>
                                <span class="symbol-label bg-light-danger text-danger fs-1 fw-bolder">
                                    <?php echo e(strtoupper(substr($account->name ?? $account->push_name ?? 'W', 0, 1))); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bolder mb-1">
                            <?php echo e($account->name ?? ($account->push_name ?? 'Unknown Account')); ?>

                        </a>
                        <div class="fs-5 fw-semibold text-muted mb-6">
                            <i class="ki-outline ki-phone fs-4 me-1"></i> <?php echo e($account->phone_number ?? 'No Phone Number'); ?>

                        </div>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->status === 'connected'): ?>
                            <span class="badge badge-light-success border border-success fw-bold px-4 py-3 text-uppercase">Connected</span>
                        <?php else: ?>
                            <span class="badge badge-light-danger border border-danger fw-bold px-4 py-3 text-uppercase">Disconnected</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    
                    <div class="d-flex flex-stack fs-4 py-3">
                        <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">Details
                            <span class="ms-2 rotate-180">
                                <i class="ki-outline ki-down fs-3"></i>
                            </span>
                        </div>
                        <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Edit account details">
                            <a href="<?php echo e(route('whatsapp_accounts.edit', $account->id)); ?>" class="btn btn-sm btn-light-primary fw-bold">Edit</a>
                        </span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>

                    <div id="kt_user_view_details" class="collapse show">
                        <div class="pb-5 fs-6">
                            <div class="fw-bold mt-5">Session ID</div>
                            <div class="text-gray-600 badge badge-light border border-gray-300 fs-8 fw-semibold mt-1"><?php echo e($account->session_id); ?></div>

                            <div class="fw-bold mt-5">WhatsApp Push Name</div>
                            <div class="text-gray-600"><?php echo e($account->push_name ?? 'N/A'); ?></div>
                            
                            <div class="fw-bold mt-5">Added On</div>
                            <div class="text-gray-600"><?php echo e($account->created_at->format('d M Y, h:i A')); ?></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right Stats & Details Section -->
        <div class="col-xl-8">
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-md-6">
                    <div class="card card-flush bg-light-primary border border-primary border-dashed shadow-sm hover-elevate-up">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2"><?php echo e(number_format($stats['total_messages'])); ?></span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Messages Sent</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-send fs-3x text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-flush bg-light-info border border-info border-dashed shadow-sm hover-elevate-up">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2"><?php echo e(number_format($stats['total_campaigns'])); ?></span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Bulk Campaigns</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-rocket fs-3x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="mt-8">
                <?php if (isset($component)) { $__componentOriginald04b9949d0dada8faa8863322f9b06a8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04b9949d0dada8faa8863322f9b06a8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.comments','data' => ['type' => 'App\Models\WhatsAppAccount','modelId' => $account->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('comments'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'App\Models\WhatsAppAccount','model-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($account->id)]); ?>
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/whatsapp_accounts/show.blade.php ENDPATH**/ ?>