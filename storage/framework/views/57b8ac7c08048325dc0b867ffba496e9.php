<?php $__env->startSection('content'); ?>

    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['label' => 'WhatsApp Accounts', 'url' => route('whatsapp_accounts.index')],
        ['label' => 'Edit Account'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'WhatsApp Accounts', 'url' => route('whatsapp_accounts.index')],
        ['label' => 'Edit Account'],
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

    <div class="card shadow-sm max-w-600px mx-auto mt-10">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">Edit Account Details</span>
                <span class="text-muted fw-semibold fs-7">Update alias for phone number <?php echo e($account->phone_number ?? 'Unknown'); ?></span>
            </h3>
        </div>
        
        <form action="<?php echo e(route('whatsapp_accounts.update', $account->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="card-body pt-5">
                <div class="mb-5">
                    <label class="form-label fw-bold">Custom Name (Alias)</label>
                    <input type="text" name="name" class="form-control form-control-solid" placeholder="e.g. Sales Team, Support Line" value="<?php echo e(old('name', $account->name)); ?>">
                    <div class="text-muted fs-7 mt-2">Set a custom name to easily identify this account in dropdowns and reports.</div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-danger mt-1 fs-7"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                
                <div class="mb-5">
                    <label class="form-label fw-bold">WhatsApp Name (Push Name)</label>
                    <input type="text" class="form-control form-control-solid text-gray-500" value="<?php echo e($account->push_name ?? 'N/A'); ?>" readonly>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold">Status</label>
                    <div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($account->status === 'connected'): ?>
                            <span class="badge badge-light-success fw-bold border border-success">Connected</span>
                        <?php else: ?>
                            <span class="badge badge-light-danger fw-bold border border-danger">Disconnected</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card-footer d-flex justify-content-end py-6">
                <a href="<?php echo e(route('whatsapp_accounts.index')); ?>" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/whatsapp_accounts/edit.blade.php ENDPATH**/ ?>