<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['items' => [], 'badge' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['items' => [], 'badge' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 mb-4 mt-2">

    
    <li class="breadcrumb-item">
        <a href="<?php echo e(route('dashboard')); ?>" class="text-muted text-hover-primary d-flex align-items-center"
            style="transition: color .2s">
            <i class="ki-outline ki-home-2 fs-5"></i>
        </a>
    </li>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        
        <li class="breadcrumb-item d-flex align-items-center px-1">
            <i class="ki-outline ki-right fs-8 text-muted opacity-50"></i>
        </li>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>
            
            <li class="breadcrumb-item">
                <a href="<?php echo e($item['url'] ?? '#'); ?>" class="text-muted text-hover-primary fw-semibold"
                    style="transition: color .2s">
                    <?php echo e($item['label']); ?>

                </a>
            </li>
        <?php else: ?>
            
            <li class="breadcrumb-item d-flex align-items-center gap-2">
                <span class="text-gray-800 fw-bold"><?php echo e($item['label']); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($badge): ?>
                    <span class="badge badge-light-primary fs-9 fw-bold px-3 py-1 ms-1"
                        style="border-radius: 20px; letter-spacing: .02em">
                        <?php echo e($badge); ?>

                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </li>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</ul><?php /**PATH /var/www/resources/views/components/breadcrumb.blade.php ENDPATH**/ ?>