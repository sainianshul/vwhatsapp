<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type',
    'modelId',
    'isVisible' => true
]));

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

foreach (array_filter(([
    'type',
    'modelId',
    'isVisible' => true
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    if ($isVisible) {
        $comments = \App\Models\Comment::with('creator')
            ->where('commentable_type', $type)
            ->where('commentable_id', $modelId)
            ->latest()
            ->get();
    } else {
        $comments = collect();
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isVisible): ?>
<div class="card shadow-none border border-gray-300 mb-5 mb-xl-8 mt-5 mt-xl-8">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-4 mb-1 text-gray-900">Notes & Comments</span>
            <span class="text-gray-500 mt-1 fw-semibold fs-8">Internal communication and updates</span>
        </h3>
    </div>
    <div class="card-body pt-4 pb-8">
        
        
        <form action="#" method="POST" class="mb-8 add-comment-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="commentable_type" value="<?php echo e($type); ?>">
            <input type="hidden" name="commentable_id" value="<?php echo e($modelId); ?>">
            
            <div class="d-flex align-items-start gap-4">
                <div class="symbol symbol-45px symbol-circle">
                    <span class="symbol-label bg-light-primary border border-primary border-dashed fs-3 fw-bold text-primary">
                        <?php echo e(substr(auth()->user()->name ?? 'U', 0, 1)); ?>

                    </span>
                </div>
                <div class="flex-grow-1">
                    <textarea name="body" class="form-control form-control-solid bg-transparent border border-gray-300 text-gray-900 px-4 py-3" rows="2" placeholder="Add a new note or comment..." required style="resize: none;"></textarea>
                    <div class="mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-sm btn-light-primary border border-primary fw-bold px-6 py-2 shadow-sm transition-all btn-post-comment">
                            Post Note
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="separator separator-dashed border-gray-300 mb-8"></div>

        
        <div class="d-flex flex-column gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="d-flex align-items-start gap-4">
                    <div class="symbol symbol-45px symbol-circle">
                        <span class="symbol-label bg-light border border-gray-300 text-gray-700 fs-4 fw-bold">
                            <?php echo e(substr($comment->creator->name ?? '?', 0, 1)); ?>

                        </span>
                    </div>
                    <div class="flex-grow-1 border border-gray-300 rounded p-5 bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-7"><?php echo e($comment->creator->name ?? 'Unknown User'); ?></span>
                                <span class="text-gray-500 fs-8 fw-semibold mt-1">
                                    <?php echo e($comment->created_at->format('d M Y, h:i A')); ?> 
                                    <span class="ms-1 fw-bold text-gray-400">(<?php echo e($comment->created_at->diffForHumans()); ?>)</span>
                                </span>
                            </div>
                            
                            
                            <form action="#" method="POST" class="delete-comment-form">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="button" class="btn btn-icon btn-sm btn-active-light-danger h-25px w-25px rounded btn-delete-comment" title="Delete note">
                                    <i class="ki-outline ki-trash fs-6 text-gray-500"></i>
                                </button>
                            </form>
                        </div>
                        <div class="text-gray-800 fs-7 fw-normal lh-lg">
                            <?php echo nl2br(e($comment->body)); ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="d-flex align-items-center justify-content-center flex-column py-10 border border-gray-300 border-dashed rounded bg-light">
                    <i class="ki-outline ki-message-text-2 text-gray-400 mb-4" style="font-size: 80px;"></i>
                    <span class="text-gray-500 fw-medium fs-5">No notes have been added yet.</span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        
        // Handle add comment via AJAX to prevent browser history back-button issues
        $('.add-comment-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = form.find('.btn-post-comment');
            var originalText = btn.html();
            
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);
            
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        // Reload the page smoothly by replacing the current state
                        window.location.replace(window.location.href);
                    }
                },
                error: function(xhr) {
                    btn.html(originalText).prop('disabled', false);
                    toastr.error('Failed to post comment.');
                }
            });
        });

        // Handle delete comment via AJAX
        $('.btn-delete-comment').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Delete Note?',
                text: 'Are you sure you want to delete this note? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                customClass: { 
                    confirmButton: 'btn btn-danger', 
                    cancelButton: 'btn btn-light ms-2' 
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST', // the form contains _method=DELETE
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                window.location.replace(window.location.href);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to delete comment.');
                        }
                    });
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php /**PATH /var/www/resources/views/components/comments.blade.php ENDPATH**/ ?>