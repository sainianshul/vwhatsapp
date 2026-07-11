<div class="row g-5 g-xxl-8">
    <!-- Internal Notes (Subject table field) -->
    <div class="col-xl-6">
        <div class="card card-bordered shadow-sm border-gray-300">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Background Notes</span>
                </h3>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-light btn-active-light-primary" id="edit-notes-btn">
                        <i class="ki-outline ki-pencil fs-4"></i> Edit
                    </button>
                </div>
            </div>
            <div class="card-body py-5">
                <div id="notes-display" class="fs-6 text-gray-800" style="white-space: pre-wrap;">
                    <?php echo e($subject->notes ?: 'No background notes available for this profile.'); ?>

                </div>
                
                <form id="notes-form" class="d-none" action="<?php echo e(route('subjects.update', $subject)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <!-- Hidden required fields just to pass validation if doing a full update, 
                         though a dedicated AJAX route for notes is better. For now, doing simple JS toggle. -->
                    <input type="hidden" name="name" value="<?php echo e($subject->name); ?>">
                    <input type="hidden" name="status" value="<?php echo e($subject->status); ?>">
                    <textarea name="notes" class="form-control form-control-solid mb-4" data-kt-autosize="true" rows="5"><?php echo e($subject->notes); ?></textarea>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light" id="cancel-notes-btn">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Save Notes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Comments Component -->
    <div class="col-xl-6">
        <?php if (isset($component)) { $__componentOriginald04b9949d0dada8faa8863322f9b06a8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04b9949d0dada8faa8863322f9b06a8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.comments','data' => ['type' => 'App\Models\Subject','modelId' => $subject->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('comments'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'App\Models\Subject','modelId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($subject->id)]); ?>
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

<?php $__env->startPush('scripts'); ?>
<script>
    $('#edit-notes-btn').click(function() {
        $('#notes-display').addClass('d-none');
        $('#notes-form').removeClass('d-none');
        $(this).addClass('d-none');
    });

    $('#cancel-notes-btn').click(function() {
        $('#notes-form').addClass('d-none');
        $('#notes-display').removeClass('d-none');
        $('#edit-notes-btn').removeClass('d-none');
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /var/www/resources/views/subjects/tabs/notes.blade.php ENDPATH**/ ?>