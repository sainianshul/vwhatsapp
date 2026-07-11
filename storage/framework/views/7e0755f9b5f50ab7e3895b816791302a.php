
<?php $emptyId = $id ?? 'table-empty'; ?>
<div id="<?php echo e($emptyId); ?>" class="d-none text-center py-20">
    <svg width="140" height="110" viewBox="0 0 140 110" fill="none" xmlns="http://www.w3.org/2000/svg"
        class="mb-6 opacity-75">
        <rect x="10" y="18" width="120" height="80" rx="8" fill="var(--bs-gray-100)" />
        <rect x="22" y="34" width="60" height="7" rx="3.5" fill="var(--bs-gray-300)" />
        <rect x="22" y="48" width="40" height="7" rx="3.5" fill="var(--bs-gray-200)" />
        <rect x="22" y="62" width="50" height="7" rx="3.5" fill="var(--bs-gray-200)" />
        <circle cx="108" cy="38" r="18" fill="var(--bs-warning-light)" />
        <path d="M101 38h14M108 31v14" stroke="var(--bs-warning)" stroke-width="2.5" stroke-linecap="round" />
    </svg>
    <p class="text-gray-600 fw-bold fs-4 mb-2"><?php echo e($title ?? 'No records found'); ?></p>
    <p class="text-muted fs-6 mt-1 mb-6"><?php echo e($subtitle ?? 'Try adjusting your search or filters'); ?></p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($actionUrl) && isset($actionText)): ?>
        <a href="<?php echo e($actionUrl); ?>" class="btn btn-primary fw-semibold btn-sm">
            <i class="ki-outline ki-plus fs-3"></i> <?php echo e($actionText); ?>

        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH /var/www/resources/views/layouts/partials/_table-empty.blade.php ENDPATH**/ ?>