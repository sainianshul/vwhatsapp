@include('layouts.partials._header')

<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-push-header="true"
    data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" class="app-default">
    <style>
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