<!--begin::Sidebar-->
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

    <!--begin::Logo-->
    <div class="app-sidebar-logo flex-shrink-0 d-none d-md-flex align-items-center px-8" id="kt_app_sidebar_logo">
        <a href="<?php echo e(route('dashboard')); ?>" class="d-flex align-items-center mb-0 text-decoration-none">
            <img src="<?php echo e(asset('icon.png')); ?>" alt="Logo" class="h-30px me-3" />
            <h1 class="text-gray-900 fs-2 fw-bolder mb-0 ls-1">VWhatsApp</h1>
        </a>
    </div>
    <!--end::Logo-->

    <!--begin::Sidebar menu-->
    <div class="app-sidebar-menu flex-grow-1" style="min-height:0; overflow:hidden; display:flex; flex-direction:column;">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper" style="flex:1 1 0; overflow-y:auto; overflow-x:hidden; padding:1.25rem 0.75rem;">

            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold px-1" id="kt_app_sidebar_menu">

                
                
                
                <div class="menu-item">
                    <a class="menu-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>"
                        href="<?php echo e(route('dashboard')); ?>">
                        <span class="menu-icon">
                            <i class="ki-outline ki-element-11 fs-2"></i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>


                
                
                
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">People</span>
                    </div>
                </div>

                
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion <?php echo e(request()->routeIs('users.*') ? 'here show' : ''); ?>">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-people fs-2"></i>
                        </span>
                        <span class="menu-title">Users</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link <?php echo e(request()->fullUrlIs(route('users.index')) ? 'active' : ''); ?>"
                                href="<?php echo e(route('users.index')); ?>">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">All Users</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link <?php echo e(request()->routeIs('users.trash') ? 'active' : ''); ?>"
                                href="<?php echo e(route('users.trash')); ?>">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Trash</span>
                            </a>
                        </div>
                    </div>
                </div>

                
                <div class="menu-item">
                    <a class="menu-link <?php echo e(request()->routeIs('login_history.index') ? 'active' : ''); ?>"
                        href="<?php echo e(route('login_history.index')); ?>">
                        <span class="menu-icon">
                            <i class="ki-outline ki-security-user fs-2"></i>
                        </span>
                        <span class="menu-title">Login History</span>
                    </a>
                </div>

                
                
                
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">WhatsApp Accounts</span>
                    </div>
                </div>

                
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion <?php echo e(request()->routeIs('whatsapp_accounts.*') ? 'here show' : ''); ?>">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-whatsapp fs-2"></i>
                        </span>
                        <span class="menu-title">Accounts</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link <?php echo e(request()->routeIs('whatsapp_accounts.index') ? 'active' : ''); ?>"
                                href="<?php echo e(route('whatsapp_accounts.index')); ?>">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">All Accounts</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link <?php echo e(request()->routeIs('whatsapp_accounts.trash') ? 'active' : ''); ?>"
                                href="<?php echo e(route('whatsapp_accounts.trash')); ?>">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Trash</span>
                            </a>
                        </div>
                    </div>
                </div>

                
                
                
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Messages</span>
                    </div>
                </div>

                <!--begin::Menu Item-->
                <div class="menu-item">
                    <a class="menu-link <?php echo e(request()->routeIs('whatsapp_messages.create') ? 'active' : ''); ?>" href="<?php echo e(route('whatsapp_messages.create')); ?>">
                        <span class="menu-icon">
                            <i class="ki-outline ki-send fs-2"></i>
                        </span>
                        <span class="menu-title">Send Message</span>
                    </a>
                </div>
                <!--end::Menu Item-->
                <!--begin::Menu Item-->
                <div class="menu-item">
                    <a class="menu-link <?php echo e(request()->routeIs('admin.bulk_campaigns.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.bulk_campaigns.index')); ?>">
                        <span class="menu-icon">
                            <i class="ki-outline ki-rocket fs-2"></i>
                        </span>
                        <span class="menu-title">Bulk Campaigns</span>
                    </a>
                </div>
                <!--end::Menu Item-->

                <!--begin::Menu Item-->
                <div class="menu-item">
                    <a class="menu-link <?php echo e(request()->routeIs('whatsapp_messages.index') ? 'active' : ''); ?>" href="<?php echo e(route('whatsapp_messages.index')); ?>">
                        <span class="menu-icon">
                            <i class="ki-outline ki-document fs-2"></i>
                        </span>
                        <span class="menu-title">Message Logs</span>
                    </a>
                </div>
                <!--end::Menu Item-->

                <!--begin::Menu Item-->
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Developer</span>
                    </div>
                </div>

                <div class="menu-item">
                    <a class="menu-link <?php echo e(request()->routeIs('admin.developer_settings.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.developer_settings.index')); ?>">
                        <span class="menu-icon">
                            <i class="ki-outline ki-code fs-2"></i>
                        </span>
                        <span class="menu-title">API Settings</span>
                    </a>
                </div>

                
                
                
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">System</span>
                    </div>
                </div>

                
                <div class="menu-item">
                    <a class="menu-link" href="#">
                        <span class="menu-icon">
                            <i class="ki-outline ki-gear fs-2"></i>
                        </span>
                        <span class="menu-title">Settings</span>
                    </a>
                </div>

            </div>
            <!--end::Menu-->
        </div>
    </div>
    <!--end::Sidebar menu-->

    <?php echo $__env->make('layouts.partials._sidebar_footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</div>
<!--end::Sidebar-->
<?php /**PATH /var/www/resources/views/layouts/partials/_sidebar.blade.php ENDPATH**/ ?>