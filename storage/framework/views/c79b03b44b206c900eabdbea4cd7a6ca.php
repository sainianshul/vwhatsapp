<?php echo $__env->make('layouts.partials._header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-push-header="true"
    data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" class="app-default">
    <style>
        /* Change global primary theme color to WhatsApp Green */
        :root, [data-bs-theme="light"], [data-bs-theme="dark"] {
            --bs-primary: #128C7E !important;
            --bs-primary-rgb: 18, 140, 126 !important;
            --bs-primary-active: #075E54 !important;
            --bs-primary-light: #E7FCE8 !important;
            --bs-primary-inverse: #ffffff !important;
            
            --kt-primary: #128C7E !important;
            --kt-primary-active: #075E54 !important;
            --kt-primary-light: #E7FCE8 !important;
            --kt-primary-inverse: #ffffff !important;
        }

        /* Force primary text color to override hardcoded SCSS utility classes */
        .text-primary { color: var(--bs-primary) !important; }
        .text-hover-primary:hover { color: var(--bs-primary) !important; }
        a.text-primary:hover, a.text-primary:focus { color: var(--bs-primary-active) !important; }
        
        .bg-primary { background-color: var(--bs-primary) !important; }
        .btn-primary { background-color: var(--bs-primary) !important; border-color: var(--bs-primary) !important; }
        .btn-primary:hover { background-color: var(--bs-primary-active) !important; border-color: var(--bs-primary-active) !important; }

        /* Ensure DataTables pagination uses the primary green color */
        .pagination .page-item.active .page-link {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #ffffff !important;
        }
        
        .pagination .page-item .page-link:hover {
            color: var(--bs-primary) !important;
            background-color: var(--bs-primary-light) !important;
        }
        /* Force a clean, neutral light-gray background instead of Metronic's default purplish tint ONLY in light mode */
        html[data-bs-theme="light"] body, 
        html[data-bs-theme="light"] #kt_app_body,
        html[data-bs-theme="light"] #kt_app_root, 
        html[data-bs-theme="light"] #kt_app_page, 
        html[data-bs-theme="light"] #kt_app_wrapper, 
        html[data-bs-theme="light"] .app-default,
        html[data-bs-theme="light"] .app-root,
        html[data-bs-theme="light"] .app-page,
        html[data-bs-theme="light"] .app-wrapper,
        html[data-bs-theme="light"] #kt_app_main,
        html[data-bs-theme="light"] .app-main,
        html[data-bs-theme="light"] #kt_app_content,
        html[data-bs-theme="light"] .app-content {
            background-color: #ffffff !important;
            background: #ffffff !important;
        }

        /* Ensure cards remain pure white for contrast ONLY in light mode */
        html[data-bs-theme="light"] .card:not([class*="bg-"]) {
            background-color: #ffffff !important;
            background: #ffffff !important;
        }

        /* Increase sidebar text font size and make it black/darker */
        #kt_app_sidebar .menu-item .menu-title {
            font-size: 1.05rem !important;
            color: #000000 !important;
            font-weight: 500 !important;
        }
        
        #kt_app_sidebar .menu-item .menu-heading {
            font-size: 0.9rem !important;
            color: #000000 !important;
            font-weight: 600 !important;
        }
        
        /* Make sidebar icons darker too for consistency */
        #kt_app_sidebar .menu-item .menu-icon i {
            color: #000000 !important;
        }
    </style>

    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">

        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

            
            <?php echo $__env->make('layouts.partials._navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

                
                <?php echo $__env->make('layouts.partials._sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-xxl">
                                <?php echo $__env->yieldContent('content'); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Wrapper-->

        </div>
        <!--end::Page-->

    </div>
    <!--end::App-->

    
    <?php echo $__env->make('layouts.partials._scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>

</html><?php /**PATH /var/www/resources/views/layouts/app.blade.php ENDPATH**/ ?>