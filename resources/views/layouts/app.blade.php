@include('layouts.partials._header')

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

        /* Clean, sharp form inputs (Google-like) */
        html[data-bs-theme="light"] .form-control,
        html[data-bs-theme="light"] .form-control-solid,
        html[data-bs-theme="light"] .form-select,
        html[data-bs-theme="light"] .form-select-solid {
            background-color: #ffffff !important;
            border: 1px solid #dadce0 !important; /* Google gray border */
            color: #202124 !important;
            border-radius: 4px !important; /* Sharper corners */
            transition: border-color 0.2s ease;
        }
        
        html[data-bs-theme="light"] .form-control:focus,
        html[data-bs-theme="light"] .form-control-solid:focus,
        html[data-bs-theme="light"] .form-select:focus,
        html[data-bs-theme="light"] .form-select-solid:focus {
            border-color: var(--bs-primary) !important;
            border-width: 2px !important;
            padding-left: calc(0.75rem - 1px) !important; /* Adjust padding for 2px border so it doesn't jump */
            padding-right: calc(0.75rem - 1px) !important;
            box-shadow: none !important;
        }

        /* Sharp, thin, dark labels */
        html[data-bs-theme="light"] .form-label,
        html[data-bs-theme="light"] label {
            color: #202124 !important;
            font-weight: 500 !important; /* Sharper than 600/700 */
            font-size: 0.9rem !important;
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

            {{-- Top Navbar --}}
            @include('layouts.partials._navbar')

            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

                {{-- Sidebar --}}
                @include('layouts.partials._sidebar')

                {{-- Main Content Area --}}
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-xxl">
                                @yield('content')
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

    {{-- Scripts --}}
    @include('layouts.partials._scripts')

</body>

</html>