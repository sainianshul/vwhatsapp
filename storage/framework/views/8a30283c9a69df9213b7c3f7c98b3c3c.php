<!--begin::Footer-->
<div class="app-sidebar-footer d-flex align-items-center px-4 pb-4" id="kt_app_sidebar_footer">
    <div class="w-100">
        <!--begin::User info-->
        <div class="d-flex align-items-center cursor-pointer p-3 rounded sidebar-footer-user w-100"
            data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-overflow="true"
            data-kt-menu-placement="top-start">
            <div class="d-flex flex-center symbol symbol-circle symbol-40px shadow-sm">
                <span class="symbol-label bg-light-primary text-primary fw-bold fs-6">
                    <?php echo e(strtoupper(substr(auth()->user()->name ?? 'A', 0, 1))); ?>

                </span>
            </div>
            <div class="d-flex flex-column align-items-start justify-content-center ms-3">
                <span class="fs-8 text-uppercase fw-bold text-muted" style="letter-spacing: 0.5px;">Profile</span>
                <span class="fs-6 fw-bold text-gray-800"><?php echo e(auth()->user()->name ?? 'Admin'); ?></span>
            </div>
            <i class="ki-outline ki-right fs-4 ms-auto text-gray-500"></i>
        </div>
        <!--end::User info-->

        <!--begin::User account menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-state-bg fw-semibold py-4 fs-6 w-275px shadow-sm"
            data-kt-menu="true" style="border: 1px solid rgba(0,0,0,0.08);">

            <!--begin::Menu item-->
            <div class="menu-item px-3 mb-2">
                <div class="menu-content d-flex align-items-center px-3">
                    <div class="d-flex flex-center symbol symbol-circle symbol-40px me-3">
                        <span class="symbol-label bg-light-primary text-primary fw-bold fs-5">
                            <?php echo e(strtoupper(substr(auth()->user()->name ?? 'A', 0, 1))); ?>

                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <div class="fw-bold d-flex align-items-center fs-6 text-gray-800">
                            <?php echo e(auth()->user()->name ?? 'Admin'); ?>

                        </div>
                        <span class="fw-normal fs-8 text-muted"><?php echo e(auth()->user()->email ?? ''); ?></span>
                    </div>
                </div>
            </div>
            <!--end::Menu item-->

            <div class="separator my-2" style="border-color: rgba(0,0,0,0.05);"></div>

            <!--begin::Profile-->
            <div class="menu-item px-3 my-1">
                <a href="#" class="menu-link px-3 py-2 rounded d-flex align-items-center text-gray-800" style="text-decoration: none; transition: background-color 0.2s ease;">
                    <span class="menu-icon me-2"><i class="ki-outline ki-user fs-3"></i></span>
                    <span class="menu-title fw-bold">My Profile</span>
                </a>
            </div>
            <!--end::Profile-->

            <div class="separator my-2" style="border-color: rgba(0,0,0,0.05);"></div>

            <!--begin::Theme mode (Premium Nested Dropdown)-->
            <div class="menu-item px-3 my-2 premium-dropdown-parent">
                <a href="#" class="menu-link px-3 py-2 rounded d-flex justify-content-between align-items-center text-gray-800" style="text-decoration: none;">
                    <span class="menu-title fw-bold">Mode</span>
                    <span class="menu-icon"><i class="ki-outline ki-night-day fs-3"></i></span>
                </a>
                
                <!-- Sub Menu -->
                <div class="premium-dropdown-sub rounded">
                    <div class="menu-item px-1 my-1">
                        <a href="#" class="menu-link px-3 py-2 rounded d-flex align-items-center text-gray-800" data-kt-element="mode" data-kt-value="light" style="text-decoration: none;">
                            <span class="menu-icon me-2"><i class="ki-outline ki-night-day fs-3"></i></span>
                            <span class="menu-title fw-bold">Light</span>
                        </a>
                    </div>
                    <div class="menu-item px-1 my-1">
                        <a href="#" class="menu-link px-3 py-2 rounded d-flex align-items-center text-gray-800" data-kt-element="mode" data-kt-value="dark" style="text-decoration: none;">
                            <span class="menu-icon me-2"><i class="ki-outline ki-moon fs-3"></i></span>
                            <span class="menu-title fw-bold">Dark</span>
                        </a>
                    </div>
                    <div class="menu-item px-1 my-1">
                        <a href="#" class="menu-link px-3 py-2 rounded d-flex align-items-center text-gray-800" data-kt-element="mode" data-kt-value="system" style="text-decoration: none;">
                            <span class="menu-icon me-2"><i class="ki-outline ki-screen fs-3"></i></span>
                            <span class="menu-title fw-bold">System</span>
                        </a>
                    </div>
                </div>
            </div>
            <!--end::Theme mode-->

            <div class="separator my-2" style="border-color: rgba(0,0,0,0.05);"></div>

            <!--begin::Sign out-->
            <div class="menu-item px-3 mb-1">
                <form method="POST" action="<?php echo e(route('signout')); ?>" id="logout-form">
                    <?php echo csrf_field(); ?>
                    <a href="#" class="menu-link px-3 py-2 text-gray-800"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="text-decoration: none;">
                        <span class="menu-icon"><i class="ki-outline ki-exit-right fs-3"></i></span>
                        <span class="menu-title fw-bold">Sign Out</span>
                    </a>
                </form>
            </div>
            <!--end::Sign out-->
        </div>
        <!--end::User account menu-->
    </div>
</div>
<!--end::Footer--><?php /**PATH /var/www/resources/views/layouts/partials/_sidebar_footer.blade.php ENDPATH**/ ?>