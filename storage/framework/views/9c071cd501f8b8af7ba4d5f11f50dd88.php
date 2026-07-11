<?php $__env->startSection('title', 'User Profile'); ?>

<?php $__env->startSection('content'); ?>

    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'User Profile','description' => 'View and manage user details']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'User Profile','description' => 'View and manage user details']); ?>
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
                    ['label' => 'People'],
                    ['label' => 'Users', 'url' => route('users.index')],
                    ['label' => $user->name],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['label' => 'People'],
                    ['label' => 'Users', 'url' => route('users.index')],
                    ['label' => $user->name],
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
                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-sm btn-light fw-semibold">
                    <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Back
                </a>
                <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-light-primary border border-primary fw-bold shadow-sm">
                    <i class="ki-outline ki-pencil fs-4 me-1"></i>Edit User
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            <?php if (isset($component)) { $__componentOriginal9d684e94d8294933a712f81f8101e557 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d684e94d8294933a712f81f8101e557 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert-success','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert-success'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d684e94d8294933a712f81f8101e557)): ?>
<?php $attributes = $__attributesOriginal9d684e94d8294933a712f81f8101e557; ?>
<?php unset($__attributesOriginal9d684e94d8294933a712f81f8101e557); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d684e94d8294933a712f81f8101e557)): ?>
<?php $component = $__componentOriginal9d684e94d8294933a712f81f8101e557; ?>
<?php unset($__componentOriginal9d684e94d8294933a712f81f8101e557); ?>
<?php endif; ?>

            <!--begin::Navbar-->
            <div class="card card-bordered border-gray-300 mb-5 mb-xl-10">
                <div class="card-body pt-9 pb-0">
                    <!--begin::Details-->
                    <div class="d-flex flex-wrap flex-sm-nowrap">
                        <!--begin: Pic-->
                        <div class="me-7 mb-4">
                            <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->profile_photo): ?>
                                    <img src="<?php echo e(Storage::url($user->profile_photo)); ?>" alt="image" class="border border-gray-300" />
                                <?php else: ?>
                                    <span class="symbol-label bg-light-primary border border-primary fs-2x fw-bold text-primary">
                                        <?php echo e(mb_strtoupper(mb_substr($user->name, 0, 2))); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->status === \App\Models\User::STATUS_ACTIVE): ?>
                                    <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                                <?php elseif($user->status === \App\Models\User::STATUS_BLOCKED): ?>
                                    <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-danger rounded-circle border border-4 border-body h-20px w-20px"></div>
                                <?php else: ?>
                                    <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-secondary rounded-circle border border-4 border-body h-20px w-20px"></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <!--end::Pic-->

                        <!--begin::Info-->
                        <div class="flex-grow-1">
                            <!--begin::Title-->
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <!--begin::User-->
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-2">
                                        <h1 class="text-gray-900 fs-1 fw-bold me-2"><?php echo e($user->name); ?></h1>
                                        <span class="badge badge-light-primary border border-primary fw-semibold px-3 py-1 me-2">
                                            <i class="ki-outline ki-check fs-7 text-primary me-1"></i>
                                            Active
                                        </span>

                                    </div>

                                    <div class="d-flex flex-wrap fw-medium fs-7 mb-4 pe-2 gap-5">
                                        <span class="d-flex align-items-center text-gray-600">
                                            <i class="ki-outline ki-phone fs-4 me-2 text-gray-500"></i>
                                            <?php echo e($user->phone); ?>

                                        </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->email): ?>
                                            <span class="d-flex align-items-center text-gray-600">
                                                <i class="ki-outline ki-sms fs-4 me-2 text-gray-500"></i>
                                                <?php echo e($user->email); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <span class="d-flex align-items-center text-gray-600 border border-gray-400 border-dashed rounded px-3 py-1">
                                            <i class="ki-outline ki-fingerprint-scan fs-5 me-2 text-primary"></i>
                                            <span class="fw-normal">Last Login:&nbsp;</span> 
                                            <span class="text-gray-800 fw-medium"><?php echo e($user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never'); ?></span>
                                        </span>
                                    </div>
                                </div>
                                <!--end::User-->

                                <!--begin::Actions-->
                                <div class="d-flex my-4">
                                    <!-- Quick Status Change Form -->
                                    <form action="<?php echo e(route('users.update', $user)); ?>" method="POST" class="d-flex align-items-center gap-2">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="email" value="<?php echo e($user->email); ?>">
                                        <input type="hidden" name="phone" value="<?php echo e($user->phone); ?>">
                                        <select name="status" class="form-select form-select-sm bg-transparent border-gray-300 text-gray-900 w-125px">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\User::getStatusList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>" <?php echo e($user->status == $value ? 'selected' : ''); ?>>
                                                    <?php echo e($label); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-dark fw-semibold">Save</button>
                                    </form>
                                </div>
                                <!--end::Actions-->
                            </div>
                            <!--end::Title-->

                            <!--begin::Stats-->
                            <div class="d-flex flex-wrap flex-stack mt-4">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-column flex-grow-1 pe-8">
                                    <div class="d-flex flex-wrap">
                                        <div class="border border-gray-300 rounded min-w-125px py-2 px-4 me-4 mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="ki-outline ki-document fs-4 text-primary me-2"></i>
                                                <div class="fs-4 fw-bold text-gray-900" id="stat-total-requests">
                                                    <span class="spinner-border spinner-border-sm text-primary align-middle" role="status"></span>
                                                </div>
                                            </div>
                                            <div class="fw-medium fs-8 text-gray-600 text-uppercase">Total Requests</div>
                                        </div>

                                        <div class="border border-gray-300 rounded min-w-125px py-2 px-4 me-4 mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="ki-outline ki-check-square fs-4 text-success me-2"></i>
                                                <div class="fs-4 fw-bold text-gray-900" id="stat-completed">
                                                    <span class="spinner-border spinner-border-sm text-success align-middle" role="status"></span>
                                                </div>
                                            </div>
                                            <div class="fw-medium fs-8 text-gray-600 text-uppercase">Completed</div>
                                        </div>

                                        <div class="border border-gray-300 rounded min-w-125px py-2 px-4 me-4 mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="ki-outline ki-time fs-4 text-warning me-2"></i>
                                                <div class="fs-5 fw-bold text-gray-900">
                                                    <?php echo e($user->created_at ? $user->created_at->format('d M, Y') : 'N/A'); ?>

                                                </div>
                                            </div>
                                            <div class="fw-medium fs-8 text-gray-600 text-uppercase">
                                                <?php echo e($user->created_at ? $user->created_at->diffForHumans() : 'Joined Date'); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Stats-->
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Details-->

                    <!--begin::Navs-->
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-6 fw-semibold mt-6" role="tablist">
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4 active" data-bs-toggle="tab" href="#kt_tab_requests" role="tab">
                                <i class="ki-outline ki-document fs-5 me-2"></i>Request List
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#kt_tab_bookings" role="tab"
                               onclick="loadTabContent('#', 'kt_tab_bookings')">
                                <i class="ki-outline ki-calendar-tick fs-5 me-2"></i>Bookings
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#kt_tab_activity" role="tab">
                                <i class="ki-outline ki-chart-line fs-5 me-2"></i>Activity Logs
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#kt_tab_login_history" role="tab"
                               onclick="loadTabContent('#', 'kt_tab_login_history')">
                                <i class="ki-outline ki-fingerprint-scan fs-5 me-2"></i>Login History
                            </a>
                        </li>
                        <li class="nav-item mt-2" role="presentation">
                            <a class="nav-link text-gray-600 text-active-dark ms-0 me-10 py-4" data-bs-toggle="tab" href="#kt_tab_send_sms" role="tab">
                                <i class="ki-outline ki-sms fs-5 me-2"></i>Send SMS
                            </a>
                        </li>
                    </ul>
                    <!--begin::Navs-->
                </div>
            </div>
            <!--end::Navbar-->

            <!--begin::Tab Content-->
            <div class="tab-content" id="myTabContent">
                
                <!-- Request List Tab -->
                <div class="tab-pane fade show active" id="kt_tab_requests" role="tabpanel">
                    <?php echo $__env->make('users.tabs.requests', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <!-- Bookings Tab -->
                <div class="tab-pane fade" id="kt_tab_bookings" role="tabpanel">
                    <div class="d-flex justify-content-center align-items-center py-10" id="loader_kt_tab_bookings">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>

                <!-- Activity Tab -->
                <div class="tab-pane fade" id="kt_tab_activity" role="tabpanel">
                    <div class="card card-bordered border-gray-300">
                        <div class="card-header border-bottom border-gray-300 pt-6">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-900 m-0">Activity Logs</h3>
                            </div>
                        </div>
                        <div class="card-body py-4">
                            <div class="d-flex flex-column align-items-center justify-content-center py-10">
                                <img src="<?php echo e(asset('media/illustrations/empty.svg')); ?>" onerror="this.onerror=null; this.src='https://preview.keenthemes.com/metronic8/demo1/assets/media/illustrations/sketchy-1/2.png'" alt="No data" class="w-150px mb-5" />
                                <h4 class="text-gray-900 fw-bold mb-1">No Activity Recorded</h4>
                                <p class="text-gray-600 fs-6">System activities related to this user will appear here.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login History Tab -->
                <div class="tab-pane fade" id="kt_tab_login_history" role="tabpanel">
                    <div class="d-flex justify-content-center align-items-center py-10" id="loader_kt_tab_login_history">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>

                <!-- Send SMS Tab -->
                <div class="tab-pane fade" id="kt_tab_send_sms" role="tabpanel">
                    <div class="card card-bordered border-gray-300">
                        <div class="card-header border-bottom border-gray-300 pt-6">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-900 m-0">Direct SMS</h3>
                            </div>
                        </div>
                        <div class="card-body py-6">
                            <form action="#" method="POST" class="form">
                                <?php echo csrf_field(); ?>
                                <div class="mb-5">
                                    <label class="form-label text-gray-900 fw-semibold">Recipient Phone Number</label>
                                    <div class="position-relative">
                                        <i class="ki-outline ki-phone fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-600"></i>
                                        <input type="text" class="form-control bg-light border-gray-300 text-gray-900 ps-12" value="<?php echo e($user->phone); ?>" readonly disabled />
                                    </div>
                                </div>
                                <div class="mb-5">
                                    <label class="required form-label text-gray-900 fw-semibold">Message</label>
                                    <textarea name="message" class="form-control bg-transparent border-gray-300 text-gray-900" rows="4" placeholder="Type your message here..."></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-dark fw-semibold" onclick="event.preventDefault(); toastr.success('SMS Sent Successfully! (Dummy)');">
                                        <i class="ki-outline ki-send fs-4 me-1"></i>Send SMS
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Tab Content-->

            <?php if (isset($component)) { $__componentOriginald04b9949d0dada8faa8863322f9b06a8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04b9949d0dada8faa8863322f9b06a8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.comments','data' => ['type' => ''.e(\App\Models\Comment::TYPE_USER).'','modelId' => $user->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('comments'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => ''.e(\App\Models\Comment::TYPE_USER).'','model-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->id)]); ?>
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
    <!--end::Content-->

<?php $__env->stopSection(); ?>

<?php $__env->startPush('datatables_css'); ?>
    <?php echo $__env->make('layouts.partials._datatable-cdn-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('datatables_js'); ?>
    <?php echo $__env->make('layouts.partials._datatable-cdn-js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch Stats
        fetch('#')
            .then(response => response.json())
            .then(data => {
                document.getElementById('stat-total-requests').innerHTML = data.total_requests;
                document.getElementById('stat-completed').innerHTML = data.completed;
            })
            .catch(error => {
                document.getElementById('stat-total-requests').innerHTML = '-';
                document.getElementById('stat-completed').innerHTML = '-';
            });
    });

    function loadTabContent(url, tabId) {
        const tabPane = document.getElementById(tabId);
        
        // If content already loaded (loader is gone), don't reload
        if (!tabPane.querySelector('#loader_' + tabId)) {
            return;
        }

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                tabPane.innerHTML = html;
                
                // Execute any scripts that came with the HTML
                const scripts = tabPane.getElementsByTagName('script');
                for (let i = 0; i < scripts.length; i++) {
                    const newScript = document.createElement('script');
                    newScript.text = scripts[i].text;
                    document.body.appendChild(newScript).parentNode.removeChild(newScript);
                }
            })
            .catch(error => {
                tabPane.innerHTML = '<div class="alert alert-danger m-5">Failed to load content. Please try again.</div>';
                console.error('Error loading tab content:', error);
            });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/users/show.blade.php ENDPATH**/ ?>