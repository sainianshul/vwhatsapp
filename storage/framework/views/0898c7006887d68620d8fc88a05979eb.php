<?php $__env->startSection('title', 'Edit Bot'); ?>

<?php $__env->startSection('content'); ?>

    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Edit Bot','description' => 'Update automation bot details']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Edit Bot','description' => 'Update automation bot details']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['label' => 'Bots', 'url' => route('bots.index')],
                    ['label' => $bot->name, 'url' => route('bots.show', $bot->id)],
                    ['label' => 'Edit'],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['label' => 'Bots', 'url' => route('bots.index')],
                    ['label' => $bot->name, 'url' => route('bots.show', $bot->id)],
                    ['label' => 'Edit'],
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
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="<?php echo e(route('bots.show', $bot->id)); ?>" class="btn btn-sm btn-light fw-semibold">
                    <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Back
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            <?php if (isset($component)) { $__componentOriginal3d251db05a714ec6b2f220d3dccfef6d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form-errors','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-errors'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d)): ?>
<?php $attributes = $__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d; ?>
<?php unset($__attributesOriginal3d251db05a714ec6b2f220d3dccfef6d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3d251db05a714ec6b2f220d3dccfef6d)): ?>
<?php $component = $__componentOriginal3d251db05a714ec6b2f220d3dccfef6d; ?>
<?php unset($__componentOriginal3d251db05a714ec6b2f220d3dccfef6d); ?>
<?php endif; ?>

            <form method="POST" action="<?php echo e(route('bots.update', $bot->id)); ?>" class="form d-flex flex-column flex-lg-row" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <!--begin::Aside column-->
                <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                    
                    <!--begin::Status-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">System Status</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <select name="status" class="form-select text-gray-900 border border-gray-300 bg-transparent" data-control="select2" data-hide-search="true">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\Bot::getStatusList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>" <?php echo e(old('status', $bot->status) == $value ? 'selected' : ''); ?>>
                                        <?php echo e($label); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <div class="text-gray-600 fs-7 mt-2">Internal state of this bot in our system.</div>
                        </div>
                    </div>
                    <!--end::Status-->

                    <!--begin::Type-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Bot Type</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <select name="type" class="form-select text-gray-900 border border-gray-300 bg-transparent" data-control="select2" data-hide-search="true">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\Bot::getTypeList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>" <?php echo e(old('type', $bot->type) == $value ? 'selected' : ''); ?>>
                                        <?php echo e($label); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <div class="text-gray-600 fs-7 mt-2">Capabilities allowed for this bot.</div>
                        </div>
                    </div>
                    <!--end::Type-->

                    <!--begin::Platform Status-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Platform Status</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <select name="platform_status" class="form-select text-gray-900 border border-gray-300 bg-transparent" data-control="select2" data-hide-search="true">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\Bot::getPlatformStatusList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>" <?php echo e(old('platform_status', $bot->platform_status) == $value ? 'selected' : ''); ?>>
                                        <?php echo e($label); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <div class="text-gray-600 fs-7 mt-2">Status reported by the platform itself.</div>
                        </div>
                    </div>
                    <!--end::Platform Status-->
                    
                    <div class="d-flex flex-column gap-3">
                        <button type="submit" class="btn btn-light-warning border border-warning fw-bold w-100 shadow-sm">
                            <i class="ki-outline ki-check fs-4 me-1"></i>Update Bot
                        </button>
                    </div>

                </div>
                <!--end::Aside column-->

                <!--begin::Main column-->
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    
                    <!--begin::General Info-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">General Information</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            
                            <div class="row g-9 mb-7">
                                <div class="col-md-6">
                                    <label class="required form-label fw-bold text-gray-900">Bot Name</label>
                                    <input type="text" name="name" class="form-control text-gray-900 border border-gray-300 bg-transparent" value="<?php echo e(old('name', $bot->name)); ?>" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="required form-label fw-bold text-gray-900">Target Platform</label>
                                    <select name="platform" class="form-select text-gray-900 border border-gray-300 bg-transparent" data-control="select2" data-hide-search="true" required>
                                        <option value="">Select Platform...</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\Bot::getPlatformList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value); ?>" <?php echo e(old('platform', $bot->platform) == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-9 mb-7">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-gray-900">Platform Username (Optional)</label>
                                    <input type="text" name="platform_username" class="form-control text-gray-900 border border-gray-300 bg-transparent" value="<?php echo e(old('platform_username', $bot->platform_username)); ?>" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-gray-900">Platform User ID (Optional)</label>
                                    <input type="text" name="platform_user_id" class="form-control text-gray-900 border border-gray-300 bg-transparent" value="<?php echo e(old('platform_user_id', $bot->platform_user_id)); ?>" />
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--end::General Info-->

                    <!--begin::Persona Settings-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">AI Persona Settings (Optional)</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-9 mb-7">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-gray-900">Gender</label>
                                    <select name="gender" class="form-select text-gray-900 border border-gray-300 bg-transparent">
                                        <option value="">Any/Neutral</option>
                                        <option value="male" <?php echo e(old('gender', $bot->gender) == 'male' ? 'selected' : ''); ?>>Male</option>
                                        <option value="female" <?php echo e(old('gender', $bot->gender) == 'female' ? 'selected' : ''); ?>>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-gray-900">Language</label>
                                    <input type="text" name="language" class="form-control text-gray-900 border border-gray-300 bg-transparent" placeholder="e.g. English, Hinglish" value="<?php echo e(old('language', $bot->language)); ?>" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-gray-900">Slang Level</label>
                                    <select name="slang_level" class="form-select text-gray-900 border border-gray-300 bg-transparent">
                                        <option value="">Normal</option>
                                        <option value="high" <?php echo e(old('slang_level', $bot->slang_level) == 'high' ? 'selected' : ''); ?>>High (Gen Z/Casual)</option>
                                        <option value="none" <?php echo e(old('slang_level', $bot->slang_level) == 'none' ? 'selected' : ''); ?>>None (Professional)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-7">
                                <label class="form-label fw-bold text-gray-900">AI Persona Profile</label>
                                <textarea name="ai_persona" class="form-control text-gray-900 border border-gray-300 bg-transparent" rows="2" placeholder="e.g. You are a 22-year old college student from NY. You use lowercase and abbreviations."><?php echo e(old('ai_persona', $bot->ai_persona)); ?></textarea>
                                <div class="text-gray-500 fs-8 mt-1">This context makes the AI comments highly realistic and unique to this bot.</div>
                            </div>
                            
                            <div>
                                <label class="form-label fw-bold text-gray-900">System Prompt Override (Advanced)</label>
                                <textarea name="system_prompt_override" class="form-control text-gray-900 border border-gray-300 bg-transparent" rows="2" placeholder="Only use this if you want to completely replace the default system instructions for this bot."><?php echo e(old('system_prompt_override', $bot->system_prompt_override)); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <!--end::Persona Settings-->

                    <!--begin::Connection Settings-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Connection Settings</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            
                            <div class="mb-7">
                                <label class="form-label fw-bold text-gray-900">Proxy Address</label>
                                <input type="text" name="proxy" class="form-control text-gray-900 border border-gray-300 bg-transparent" value="<?php echo e(old('proxy', $bot->proxy)); ?>" />
                            </div>

                            <div class="mb-7">
                                <label class="form-label fw-bold text-gray-900">User Agent</label>
                                <textarea name="user_agent" class="form-control text-gray-900 border border-gray-300 bg-transparent" rows="2"><?php echo e(old('user_agent', $bot->user_agent)); ?></textarea>
                            </div>

                        </div>
                    </div>
                    <!--end::Connection Settings-->

                    <!--begin::Cookie Upload-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Authentication (Cookies)</h2>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bot->has_cookie): ?>
                            <div class="card-toolbar">
                                <span class="badge badge-light-success fw-bold">Cookie Active</span>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="card-body pt-0">
                            
                            <div class="alert alert-primary bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row p-5 mb-5">
                                <i class="ki-outline ki-information fs-2hx text-primary me-4 mb-5 mb-sm-0"></i>
                                <div class="d-flex flex-column pe-0 pe-sm-10">
                                    <h5 class="mb-1 text-gray-900">Updating Cookie</h5>
                                    <span>If you upload a new file or paste new JSON, it will overwrite the existing cookie. Leave blank to keep current cookies.</span>
                                </div>
                            </div>

                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6 border-bottom border-gray-300">
                                <li class="nav-item">
                                    <a class="nav-link active text-gray-900 fw-bold" data-bs-toggle="tab" href="#cookie_file_tab">Upload File</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-gray-600 fw-semibold" data-bs-toggle="tab" href="#cookie_text_tab">Paste JSON</a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="cookie_file_tab" role="tabpanel">
                                    <div class="mb-5">
                                        <label class="form-label fw-bold text-gray-900">Cookie JSON File</label>
                                        <input class="form-control text-gray-900 border border-gray-300 bg-transparent" type="file" name="cookie_file" accept=".json,.txt" />
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="cookie_text_tab" role="tabpanel">
                                    <div class="mb-5">
                                        <label class="form-label fw-bold text-gray-900">Raw Cookie Data</label>
                                        <textarea name="cookie" class="form-control text-gray-900 border border-gray-300 bg-transparent font-monospace" rows="5" placeholder="Leave empty to keep existing..."><?php echo e(old('cookie', $bot->cookie)); ?></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--end::Cookie Upload-->

                    <!--begin::Notes-->
                    <div class="card card-flush py-4 card-bordered border-gray-300">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Notes</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <textarea name="notes" class="form-control text-gray-900 border border-gray-300 bg-transparent" rows="4"><?php echo e(old('notes', $bot->notes)); ?></textarea>
                        </div>
                    </div>
                    <!--end::Notes-->

                </div>
                <!--end::Main column-->
            </form>
        </div>
    </div>
    <!--end::Content-->

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/bots/edit.blade.php ENDPATH**/ ?>