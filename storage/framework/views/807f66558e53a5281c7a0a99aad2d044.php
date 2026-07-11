<?php $__env->startSection('content'); ?>
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Create Comments Template
                    </h1>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="<?php echo e(route('automation-templates.index')); ?>" class="btn btn-sm btn-light">Cancel</a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <?php echo $__env->make('layouts.partials._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <form action="<?php echo e(route('automation-templates.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <!-- BASIC SETTINGS -->
                    <div class="card shadow-sm mb-5">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Basic Settings</span>
                                <span class="text-muted fw-semibold fs-7">Core rules for your automation</span>
                            </h3>
                        </div>
                        <div class="card-body py-4">
                            <div class="row g-9 mb-8">
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Template Name</label>
                                    <input type="text" class="form-control" name="name" placeholder="e.g. Angry Citizen Campaign" required>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Target Platform</label>
                                    <select class="form-select" data-control="select2" data-hide-search="true" name="platform" required>
                                        <option value="facebook">Facebook</option>
                                        <option value="instagram">Instagram</option>
                                        <option value="twitter">Twitter</option>
                                        <option value="youtube">YouTube</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-9 mb-8">
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Engine Type</label>
                                    <select class="form-select" data-control="select2" data-hide-search="true" name="engine_type" id="engine_type_select" required>
                                        <option value="ai" selected>AI Generator (Smart)</option>
                                        <option value="bank">Comment Bank (Manual)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 fv-row" id="tone_container">
                                    <label class="required fs-6 fw-semibold mb-2">AI Tone / Sentiment</label>
                                    <select class="form-select" data-control="select2" data-hide-search="true" name="ai_tone" required>
                                        <option value="positive">Positive & Supportive</option>
                                        <option value="neutral">Neutral & Informative</option>
                                        <option value="negative">Negative & Angry</option>
                                        <option value="custom">Custom Persona</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-9 mb-8" id="ai_prompt_container">
                                <div class="col-12 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Custom AI Prompt (Optional)</label>
                                    <textarea class="form-control" name="ai_prompt" rows="3" placeholder="Example: Act as a local citizen complaining about traffic. Use local slang."></textarea>
                                    <div class="text-muted fs-7 mt-2">Give specific instructions to the AI on how to behave. If left blank, it will just use the basic Tone selected above.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ADVANCED SETTINGS ACCORDION -->
                    <div class="accordion accordion-icon-toggle" id="advanced_accordion">
                        <div class="accordion-item mb-5 shadow-sm rounded border-0">
                            <h2 class="accordion-header" id="advanced_header">
                                <button class="accordion-button fs-4 fw-bold collapsed bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#advanced_body" aria-expanded="false" aria-controls="advanced_body">
                                    <i class="ki-outline ki-setting-2 fs-2 me-2"></i> Show Advanced Options
                                </button>
                            </h2>
                            <div id="advanced_body" class="accordion-collapse collapse" aria-labelledby="advanced_header" data-bs-parent="#advanced_accordion">
                                <div class="accordion-body">
                                    
                                    <div class="row g-9 mb-8 mt-2">
                                        <div class="col-md-6 fv-row">
                                            <label class="fs-6 fw-semibold mb-2">Keywords MUST Include</label>
                                            <input type="text" class="form-control" name="keywords_include" placeholder="e.g. traffic, pothole, mayor">
                                            <div class="text-muted fs-7 mt-2">Comma separated. Bot will ONLY comment if post contains at least one.</div>
                                        </div>
                                        <div class="col-md-6 fv-row">
                                            <label class="fs-6 fw-semibold mb-2">Keywords to EXCLUDE</label>
                                            <input type="text" class="form-control" name="keywords_exclude" placeholder="e.g. rip, sad, death">
                                            <div class="text-muted fs-7 mt-2">Comma separated. Bot will skip the post if it contains any of these.</div>
                                        </div>
                                    </div>

                                    <div class="row g-9 mb-8">
                                        <div class="col-md-4 fv-row">
                                            <label class="fs-6 fw-semibold mb-2">Min Post Likes Required</label>
                                            <input type="number" class="form-control" name="min_likes_required" value="0" min="0">
                                            <div class="text-muted fs-7 mt-2">To avoid commenting on dead posts.</div>
                                        </div>
                                        <div class="col-md-4 fv-row">
                                            <label class="fs-6 fw-semibold mb-2">Delay Range (Minutes)</label>
                                            <div class="d-flex align-items-center">
                                                <input type="number" class="form-control me-2" name="min_delay_mins" value="5" min="1" max="120" placeholder="Min">
                                                <span class="text-muted mx-2">to</span>
                                                <input type="number" class="form-control ms-2" name="max_delay_mins" value="15" min="1" max="240" placeholder="Max">
                                            </div>
                                            <div class="text-muted fs-7 mt-2">Random delay before firing comment (Anti-ban).</div>
                                        </div>
                                        <div class="col-md-4 fv-row">
                                            <label class="fs-6 fw-semibold mb-2">Max Daily Comments</label>
                                            <input type="number" class="form-control" name="max_daily_comments" value="20" min="1" max="500">
                                            <div class="text-muted fs-7 mt-2">Limit per account using this template.</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label"><i class="ki-outline ki-check fs-2"></i> Save Template</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('#engine_type_select').on('change', function() {
        if($(this).val() === 'ai') {
            $('#tone_container').show();
            $('#ai_prompt_container').show();
        } else {
            $('#tone_container').hide();
            $('#ai_prompt_container').hide();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/automation/templates/create.blade.php ENDPATH**/ ?>