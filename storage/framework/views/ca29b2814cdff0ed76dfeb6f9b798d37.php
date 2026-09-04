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

    <div class="card">
        <div class="card-body">
            
            <h4 class="mb-5">WhatsApp Sending API Documentation</h4>
            <p>Use the following API to send WhatsApp messages from your external CRM or application.</p>
            
            <hr>

            <h5 class="mt-5">1. Endpoint URL</h5>
            <p>Make a <strong>POST</strong> request to the following URL:</p>
            <code><?php echo e(url('/api/v1/messages/send')); ?></code>

            <h5 class="mt-5">2. Required Headers</h5>
            <p>You must provide the following HTTP headers in your request:</p>
            <ul>
                <li><strong>Content-Type:</strong> <code>application/json</code></li>
                <li><strong>Authorization:</strong> <code>Bearer YOUR_API_KEY_HERE</code></li>
            </ul>

            <h5 class="mt-5">3. JSON Request Body</h5>
            <p>Provide the data in JSON format:</p>
            <pre class="bg-light p-3 rounded" style="max-width: 500px;"><code>{
  "to": "919876543210",
  "text": "Hello from CRM!"
}</code></pre>

            <p class="mt-5 mb-0"><strong>Note:</strong> The "to" field must include the country code without any + or spaces.</p>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/developer_settings/docs.blade.php ENDPATH**/ ?>