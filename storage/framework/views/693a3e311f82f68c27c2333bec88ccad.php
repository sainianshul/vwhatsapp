<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>

    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Hello, <?php echo e(auth()->user()->name); ?> 👋
                </h1>
                <span class="text-muted fs-7 fw-semibold mt-1">Here is what's happening with your WhatsApp campaigns today.</span>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="<?php echo e(route('whatsapp_messages.create')); ?>" class="btn btn-sm fw-bold btn-primary hover-elevate-up">
                    <i class="ki-outline ki-send fs-3"></i> Quick Send
                </a>
                <a href="<?php echo e(route('admin.bulk_campaigns.create')); ?>" class="btn btn-sm fw-bold btn-light-primary hover-elevate-up">
                    <i class="ki-outline ki-rocket fs-3"></i> New Campaign
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                
                
                <div class="col-md-3">
                    <div class="card card-flush h-md-100 border border-primary bg-light-primary hover-elevate-up">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-primary me-2 lh-1 ls-n2"><?php echo e($stats['accounts']); ?></span>
                                <span class="text-primary opacity-75 pt-1 fw-semibold fs-6">Total Accounts</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-whatsapp fs-3x text-primary"></i>
                            <div class="ms-auto">
                                <span class="badge badge-primary fs-base">
                                    <?php echo e($stats['connected']); ?> Connected
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="col-md-3">
                    <div class="card card-flush h-md-100 border border-success bg-light-success hover-elevate-up">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-success me-2 lh-1 ls-n2"><?php echo e(number_format($stats['messages_sent'])); ?></span>
                                <span class="text-success opacity-75 pt-1 fw-semibold fs-6">Messages Sent</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-directbox-default fs-3x text-success"></i>
                        </div>
                    </div>
                </div>

                
                <div class="col-md-3">
                    <div class="card card-flush h-md-100 border border-info bg-light-info hover-elevate-up">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-info me-2 lh-1 ls-n2"><?php echo e($stats['campaigns']); ?></span>
                                <span class="text-info opacity-75 pt-1 fw-semibold fs-6">Bulk Campaigns</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-rocket fs-3x text-info"></i>
                        </div>
                    </div>
                </div>

                
                <div class="col-md-3">
                    <div class="card card-flush h-md-100 border border-gray-300 hover-elevate-up">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">Online</span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Gateway Status</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex align-items-center">
                            <i class="ki-outline ki-check-circle fs-3x text-success"></i>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="row g-5 g-xl-10">
                <div class="col-xl-12">
                    <div class="card card-flush">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Getting Started</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Follow these simple steps to start sending messages</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-5">
                                    <div class="border border-dashed border-primary rounded px-7 py-5">
                                        <div class="d-flex flex-center bg-light-primary rounded-circle w-50px h-50px mb-4">
                                            <span class="fs-2 fw-bold text-primary">1</span>
                                        </div>
                                        <h4 class="fw-bold mb-3">Link Device</h4>
                                        <p class="text-gray-600">Scan the QR code to securely connect your WhatsApp account to the platform.</p>
                                        <a href="<?php echo e(route('whatsapp_accounts.create')); ?>" class="btn btn-sm btn-light-primary fw-bold">Link Now</a>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-5">
                                    <div class="border border-dashed border-success rounded px-7 py-5">
                                        <div class="d-flex flex-center bg-light-success rounded-circle w-50px h-50px mb-4">
                                            <span class="fs-2 fw-bold text-success">2</span>
                                        </div>
                                        <h4 class="fw-bold mb-3">Create Campaign</h4>
                                        <p class="text-gray-600">Upload your Excel file and compose a personalized message for your audience.</p>
                                        <a href="<?php echo e(route('admin.bulk_campaigns.create')); ?>" class="btn btn-sm btn-light-success fw-bold">Create Campaign</a>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-5">
                                    <div class="border border-dashed border-info rounded px-7 py-5">
                                        <div class="d-flex flex-center bg-light-info rounded-circle w-50px h-50px mb-4">
                                            <span class="fs-2 fw-bold text-info">3</span>
                                        </div>
                                        <h4 class="fw-bold mb-3">Track Progress</h4>
                                        <p class="text-gray-600">Monitor real-time delivery reports and engage with your successful sends.</p>
                                        <a href="<?php echo e(route('admin.bulk_campaigns.index')); ?>" class="btn btn-sm btn-light-info fw-bold">View Reports</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--end::Content-->

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/dashboard.blade.php ENDPATH**/ ?>