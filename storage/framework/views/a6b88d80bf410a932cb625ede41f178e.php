<?php $__env->startSection('content'); ?>
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Create Bulk Campaign</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?php echo e(route('admin.bulk_campaigns.index')); ?>" class="text-muted text-hover-primary">Bulk Campaigns</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-gray-900">Create</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
                <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form action="<?php echo e(route('admin.bulk_campaigns.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <!-- Left Col -->
                    <div class="col-lg-8">
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0 cursor-pointer">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Campaign Details</h3>
                                </div>
                            </div>
                            <div class="card-body border-top p-9">
                                
                                <div class="mb-7">
                                    <label class="required form-label fw-semibold">Campaign Name</label>
                                    <input type="text" name="campaign_name" class="form-control" placeholder="e.g., Summer Sale 2026" required value="<?php echo e(old('campaign_name')); ?>" />
                                </div>

                                <div class="mb-7">
                                    <label class="required form-label fw-semibold">Select WhatsApp Account</label>
                                    <select name="whatsapp_account_id" class="form-select" data-control="select2" required>
                                        <option value="">Select an account</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($account->id); ?>" <?php echo e(old('whatsapp_account_id') == $account->id ? 'selected' : ''); ?>>
                                                <?php echo e($account->phone_number); ?> (<?php echo e($account->pushname); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-7">
                                    <div class="d-flex justify-content-between mb-2">
                                        <label class="required form-label fw-semibold mb-0">Upload Contacts CSV</label>
                                        <a href="<?php echo e(route('admin.bulk_campaigns.sample_csv')); ?>" class="text-primary fs-7 fw-bold"><i class="fa fa-download me-1"></i> Download Sample CSV</a>
                                    </div>
                                    <input type="file" name="csv_file" id="csvFile" class="form-control" accept=".csv" required />
                                    <div class="text-muted fs-7 mt-2">CSV must contain a <strong>phone</strong> column. Other columns can be used as variables like <code>{{name}}</code>.</div>
                                </div>

                                <div class="mb-7">
                                    <div class="d-flex justify-content-between mb-2">
                                        <label class="form-label fw-semibold mb-0">Attach Media (Optional)</label>
                                    </div>
                                    <input type="file" name="media_file" id="mediaFile" class="form-control" accept="image/*,video/mp4,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.zip" />
                                    <div class="text-muted fs-7 mt-2">Attach an image, video, audio, or document (Max 16MB). This media will be sent to all contacts.</div>
                                </div>

                                <div class="mb-7">
                                    <label class="required form-label fw-semibold">Message Template</label>
                                    <textarea name="message_template" id="messageTemplate" class="form-control" rows="6" placeholder="Hi {{name}}, here is your special offer!" required><?php echo e(old('message_template')); ?></textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Right Col -->
                    <div class="col-lg-4">
                        <div class="card mb-5">
                            <div class="card-header border-0 cursor-pointer">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Settings & Preview</h3>
                                </div>
                            </div>
                            <div class="card-body border-top p-9">
                                
                                <!-- Anti Ban Settings -->
                                <h4 class="fs-5 fw-bold text-gray-800 mb-4">Anti-Ban Delays</h4>
                                <div class="row mb-7">
                                    <div class="col-6">
                                        <label class="form-label fw-semibold fs-7">Min Delay (sec)</label>
                                        <input type="number" name="delay_min" class="form-control form-control-sm" value="12" min="1" required />
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold fs-7">Max Delay (sec)</label>
                                        <input type="number" name="delay_max" class="form-control form-control-sm" value="30" min="2" required />
                                    </div>
                                </div>
                                <div class="text-muted fs-8 mb-7">Random gap between sending messages to mimic human behavior.</div>

                                <!-- Live Preview -->
                                <h4 class="fs-5 fw-bold text-gray-800 mb-4">Live Preview</h4>
                                <div class="p-5 mb-5 rounded" style="background-color: #efeae2; min-height: 200px; border-radius: 8px;">
                                    <div class="d-flex justify-content-end">
                                        <div class="p-3 shadow-sm position-relative" style="background-color: #d9fdd3; color: #111b21; border-radius: 8px 0px 8px 8px; max-width: 85%;">
                                            <!-- Message Tail (Fake CSS triangle) -->
                                            <div style="position: absolute; top: 0; right: -8px; width: 0; height: 0; border-top: 0px solid transparent; border-bottom: 12px solid transparent; border-left: 10px solid #d9fdd3;"></div>
                                            
                                            <div id="previewBox" class="text-wrap text-break fs-6" style="white-space: pre-wrap; margin-bottom: 15px;">Select a CSV and type a message to see the preview here...</div>
                                            
                                            <div class="text-end position-absolute bottom-0 end-0 p-2" style="font-size: 11px; color: #667781;">
                                                <span class="me-1">12:00</span>
                                                <i class="ki-solid ki-check-double text-info fs-7"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <button type="submit" class="btn btn-primary">Start Campaign</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('csvFile');
        const messageInput = document.getElementById('messageTemplate');
        const previewBox = document.getElementById('previewBox');
        let firstRowVariables = {};

        // Parse first row of CSV when selected
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                const text = event.target.result;
                const rows = text.split('\n');
                if (rows.length > 1) {
                    const headers = rows[0].split(',').map(h => h.trim().toLowerCase());
                    const firstData = rows[1].split(',').map(d => d.trim());
                    
                    firstRowVariables = {};
                    headers.forEach((header, index) => {
                        if (firstData[index]) {
                            firstRowVariables[header] = firstData[index];
                        }
                    });
                    updatePreview();
                }
            };
            reader.readAsText(file);
        });

        messageInput.addEventListener('input', updatePreview);

        function updatePreview() {
            let text = messageInput.value;
            if (!text) {
                previewBox.innerHTML = "Select a CSV and type a message to see the preview here...";
                return;
            }

            if (Object.keys(firstRowVariables).length > 0) {
                for (const [key, value] of Object.entries(firstRowVariables)) {
                    // Use {{ to prevent Laravel Blade from parsing it as PHP
                    const regex = new RegExp(`@{{${key}}}`, 'gi');
                    text = text.replace(regex, `<span class="badge badge-light-primary px-1">${value}</span>`);
                }
            }
            previewBox.innerHTML = text;
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/bulk_campaigns/create.blade.php ENDPATH**/ ?>