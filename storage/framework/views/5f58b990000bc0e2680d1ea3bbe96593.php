<?php $__env->startSection('content'); ?>
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Command Center & Audit Logs
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo e(route('dashboard')); ?>" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">Automation</li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-gray-900">Command Center</li>
                    </ul>
                </div>
                
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <!-- Filter Dropdown -->
                    <form method="GET" action="<?php echo e(route('command-center.index')); ?>" class="d-flex">
                        <select name="status" class="form-select form-select-sm form-select-solid w-150px me-3" onchange="this.form.submit()">
                            <option value="all" <?php echo e(request('status') === 'all' ? 'selected' : ''); ?>>All Status</option>
                            <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending (Queue)</option>
                            <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                            <option value="failed" <?php echo e(request('status') === 'failed' ? 'selected' : ''); ?>>Failed</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <?php echo $__env->make('layouts.partials._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <div class="card shadow-sm">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Execution Queue & History</span>
                            <span class="text-muted fw-semibold fs-7">100% Transparent logs of what the AI bots are doing.</span>
                        </h3>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-150px">Scheduled Time</th>
                                        <th class="min-w-200px">Target Account / Post</th>
                                        <th class="min-w-300px">Content To Post</th>
                                        <th class="min-w-100px text-center">Status</th>
                                        <th class="text-end min-w-100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $operations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-900 fw-bold fs-6" data-bs-toggle="tooltip" title="<?php echo e($op->scheduled_at->format('d M Y, h:i A')); ?>">
                                                    <?php echo e($op->scheduled_at->diffForHumans()); ?>

                                                </span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($op->status === 'completed' && $op->completed_at): ?>
                                                    <span class="text-success fs-8">Done: <?php echo e($op->completed_at->format('h:i A')); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-30px me-2">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($op->socialAccount): ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($op->socialAccount->platform === 'facebook'): ?>
                                                            <span class="symbol-label bg-light-primary"><i class="ki-outline ki-facebook fs-4 text-primary"></i></span>
                                                        <?php elseif($op->socialAccount->platform === 'twitter'): ?>
                                                            <span class="symbol-label bg-light-info"><i class="ki-outline ki-twitter fs-4 text-info"></i></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-gray-900 fw-bold fs-7"><?php echo e($op->socialAccount->account_name ?? 'Unknown'); ?></span>
                                                    <a href="<?php echo e($op->post->account_url ?? '#'); ?>" target="_blank" class="text-primary text-hover-primary fw-semibold fs-8">View Post <i class="ki-outline ki-exit-right-corner fs-8"></i></a>
                                                </div>
                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($op->assignedBot): ?>
                                                <div class="mt-1">
                                                    <span class="badge badge-light-dark fs-8">🤖 Bot: <?php echo e($op->assignedBot->name); ?></span>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="border border-gray-300 border-dashed rounded p-3 bg-light-secondary text-gray-800" style="max-height: 80px; overflow-y: auto;">
                                                <?php echo e($op->content_to_post); ?>

                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($op->status === 'failed' && $op->error_log): ?>
                                                <div class="mt-2 text-danger fs-8">
                                                    <strong>Error:</strong> <?php echo e($op->error_log); ?>

                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-light-<?php echo e($op->status_color); ?> fs-7"><?php echo e(ucfirst($op->status)); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($op->status === 'pending'): ?>
                                                <form action="<?php echo e(route('command-center.cancel', $op)); ?>" method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-light-warning" onclick="return confirm('Cancel this upcoming comment?')">
                                                        Cancel
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form action="<?php echo e(route('command-center.destroy', $op)); ?>" method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-icon btn-active-light-danger w-30px h-30px" onclick="return confirm('Delete this log?')" data-bs-toggle="tooltip" title="Delete Log">
                                                        <i class="ki-outline ki-trash fs-3"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-8">
                                            No automation logs or pending operations found.
                                        </td>
                                    </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                            <div class="d-flex flex-stack flex-wrap pt-10">
                                <?php echo e($operations->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/automation/command_center/index.blade.php ENDPATH**/ ?>