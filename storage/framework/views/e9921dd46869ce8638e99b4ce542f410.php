<div class="d-flex align-items-center justify-content-between px-10 py-5 bg-white border-bottom border-gray-200 position-fixed w-100 z-index-3" style="top: 0; left: 0; z-index: 999;">
    <div class="d-flex align-items-center">
        <a href="<?php echo e(route('home')); ?>" class="d-flex align-items-center text-decoration-none">
            <img alt="Logo" src="<?php echo e(asset('icon.png')); ?>" class="h-30px me-3" />
            <h3 class="m-0 fw-bolder text-success fs-2">VWhatsApp</h3>
        </a>
    </div>
    <div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-success fw-bolder">Go to Dashboard</a>
        <?php else: ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Route::currentRouteName() != 'login'): ?>
                <a href="<?php echo e(route('login')); ?>" class="btn btn-success fw-bolder px-8 py-3">Log In</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /var/www/resources/views/layouts/partials/_front_navbar.blade.php ENDPATH**/ ?>