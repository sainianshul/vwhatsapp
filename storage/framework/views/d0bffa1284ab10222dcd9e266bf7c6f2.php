<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
    <div class="alert bg-light-danger border border-danger border-dashed d-flex align-items-center p-3 mb-6 rounded">
        <i class="ki-outline ki-information-5 fs-3 text-danger me-3"></i>
        <div class="d-flex flex-column">
            <ul class="mb-0 text-danger fw-medium fs-7 px-3 m-0" style="list-style-type: disc;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        </div>
        <button type="button" class="btn btn-icon ms-auto m-0 p-0" data-bs-dismiss="alert" style="width: 24px; height: 24px;">
            <i class="ki-outline ki-cross fs-4 text-danger"></i>
        </button>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /var/www/resources/views/components/form-errors.blade.php ENDPATH**/ ?>