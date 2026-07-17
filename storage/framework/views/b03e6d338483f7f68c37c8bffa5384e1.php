<?php $__env->startSection('title', 'Send WhatsApp Message'); ?>

<?php $__env->startSection('content'); ?>

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-outline ki-check fs-2hx text-success me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-success">Success</h4>
                        <span><?php echo e(session('success')); ?></span>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-outline ki-cross-circle fs-2hx text-danger me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Error</h4>
                        <span><?php echo e(session('error')); ?></span>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form id="quick-send-form" method="POST" action="<?php echo e(route('whatsapp_messages.store')); ?>" enctype="multipart/form-data" class="form d-flex flex-column flex-lg-row">
                <?php echo csrf_field(); ?>
                
                <!--begin::Aside column-->
                <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                    
                    <!--begin::Sender Account-->
                    <div class="card card-flush py-4 card-bordered border-gray-300 shadow-sm">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Sender Account</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <label class="required form-label">From</label>
                            <select name="whatsapp_account_id" class="form-select <?php $__errorArgs = ['whatsapp_account_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" data-control="select2" data-hide-search="true" required>
                                <option value="">Select Account</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $activeAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($account->id); ?>" <?php echo e(old('whatsapp_account_id') == $account->id ? 'selected' : ''); ?>>
                                        <?php echo e($account->push_name ?? $account->phone_number ?? 'Unknown'); ?> (<?php echo e($account->phone_number); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['whatsapp_account_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <!--end::Sender Account-->
                    
                    <div class="d-flex flex-column gap-3">
                        <button type="submit" class="btn btn-primary fw-bold w-100 shadow-sm" id="btn-send-msg">
                            <i class="ki-outline ki-send fs-4 me-1"></i> Send Message
                        </button>
                    </div>
                </div>
                <!--end::Aside column-->

                <!--begin::Main column-->
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <div class="card card-flush py-4 card-bordered border-gray-300 shadow-sm">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Message Details</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            
                            <!--begin::Input group-->
                            <div class="mb-7">
                                <label class="required form-label">Receiver Numbers</label>
                                <input class="form-control" value="<?php echo e(old('receiver_numbers')); ?>" name="receiver_numbers" id="receiver_numbers" placeholder="Enter up to 10 numbers (e.g. 919876543210)" required/>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-7">
                                <label class="required form-label">Message Text</label>
                                <textarea name="message_text" class="form-control <?php $__errorArgs = ['message_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid border-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="5" placeholder="Type your message here..." required><?php echo e(old('message_text')); ?></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['message_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-7">
                                <label class="form-label">Attach Media (Optional)</label>
                                <input type="file" name="media_file" id="mediaFile" class="form-control" accept="image/*,video/mp4,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.zip" />
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-7">
                                <label class="form-label">Schedule At (Optional)</label>
                                <input type="datetime-local" name="scheduled_at" id="scheduledAt" class="form-control" />
                            </div>
                            <!--end::Input group-->

                        </div>
                    </div>
                </div>
                <!--end::Main column-->

            </form>
        </div>
    </div>
    <!--end::Content-->

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script>
        $(document).ready(function() {
            var tagifyInstance = new Tagify(document.querySelector("#receiver_numbers"), {
                maxTags: 10,
                pattern: /^[0-9+]+$/,
                delimiters: ",| |\n|\r",
                pasteAsTags: true,
                placeholder: "Enter numbers and press Enter",
                dropdown: { enabled: 0 }
            });

            $('#quick-send-form').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let btn = $('#btn-send-msg');
                let originalText = btn.html();

                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending...');

                let formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.success) {
                            // Clear form
                            tagifyInstance.removeAllTags();
                            form.find('textarea[name="message_text"]').val('');
                            form.find('input[name="media_file"]').val('');

                            Swal.fire({
                                toast: true,
                                position: 'top',
                                showConfirmButton: false,
                                timer: 2000,
                                icon: 'success',
                                title: response.message || 'Messages sent successfully'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 3000,
                            icon: 'error',
                            title: xhr.responseJSON?.message || 'Something went wrong!'
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Schedule At: Dynamic button text
            $('#scheduledAt').on('change', function() {
                let btn = $('#btn-send-msg');
                if ($(this).val()) {
                    btn.html('<i class="ki-outline ki-calendar fs-4 me-1"></i> Schedule Message');
                    btn.removeClass('btn-primary').addClass('btn-warning text-dark');
                } else {
                    btn.html('<i class="ki-outline ki-send fs-4 me-1"></i> Send Message');
                    btn.removeClass('btn-warning text-dark').addClass('btn-primary');
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/admin/whatsapp_messages/create.blade.php ENDPATH**/ ?>