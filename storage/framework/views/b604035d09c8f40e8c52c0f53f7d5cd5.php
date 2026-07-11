
<?php $skelId = $id ?? 'table-skeleton'; ?>
<div id="<?php echo e($skelId); ?>" class="placeholder-glow">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 6; $i++): ?>
        <div class="d-flex align-items-center gap-3 py-4 border-bottom border-gray-200 px-4">
            <span class="placeholder bg-secondary rounded" style="width:35px;height:35px;"></span>
            <div class="flex-grow-1">
                <span class="placeholder bg-secondary rounded col-3 mb-2 d-block"></span>
                <span class="placeholder bg-secondary rounded col-2 d-block"></span>
            </div>
            <span class="placeholder bg-secondary rounded col-1"></span>
            <span class="placeholder bg-secondary rounded col-1"></span>
            <span class="placeholder bg-secondary rounded col-2"></span>
        </div>
    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /var/www/resources/views/components/table-skeleton.blade.php ENDPATH**/ ?>