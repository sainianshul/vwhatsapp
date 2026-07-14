<?php $__env->startSection('title', 'API Documentation'); ?>

<?php $__env->startSection('content'); ?>

    <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
        ['label' => 'Developer API', 'url' => route('admin.developer_settings.index')],
        ['label' => 'API Documentation'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Developer API', 'url' => route('admin.developer_settings.index')],
        ['label' => 'API Documentation'],
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

    <div class="card shadow-sm border-0">
        <div class="card-header pt-7 border-bottom">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-dark fs-4">API Documentation</span>
                <span class="text-muted mt-1 fw-semibold fs-7">Quick integration guide</span>
            </h3>
            <div class="card-toolbar">
                <a href="<?php echo e(route('admin.developer_settings.index')); ?>" class="btn btn-sm btn-light">Back to Settings</a>
            </div>
        </div>
        <div class="card-body pt-6">
            
            <div class="mb-8">
                <h5 class="fw-bold text-dark mb-3">Endpoint</h5>
                <div class="d-flex align-items-center bg-light-primary border border-primary border-dashed p-3 rounded" style="max-width: 600px;">
                    <span class="badge badge-primary me-3 fs-7">POST</span>
                    <span class="text-primary fw-bold fs-6"><?php echo e(url('/api/v1/messages/send')); ?></span>
                </div>
            </div>

            <div class="mb-8">
                <h5 class="fw-bold text-dark mb-3">Authentication Header</h5>
                <div class="bg-light p-4 rounded border border-gray-300" style="max-width: 600px;">
                    <code class="text-dark fs-6 d-block mb-1">Content-Type: <span class="text-primary">application/json</span></code>
                    <code class="text-dark fs-6 d-block">Authorization: Bearer <span class="text-danger">YOUR_API_KEY</span></code>
                </div>
            </div>

            <div class="mb-0">
                <h5 class="fw-bold text-dark mb-3">JSON Request Body</h5>
                <div class="bg-light p-4 rounded border border-gray-300" style="max-width: 600px;">
                    <pre class="mb-0"><code class="text-dark fs-6">{
  <span class="text-primary">"to"</span>: <span class="text-success">"919876543210"</span>,
  <span class="text-primary">"text"</span>: <span class="text-success">"Hello from CRM!"</span>,
  <span class="text-muted">"from"</span>: <span class="text-success">"918888888888"</span> <span class="text-muted">// Optional</span>
}</code></pre>
                </div>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/developer_settings/docs.blade.php ENDPATH**/ ?>