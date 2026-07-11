<div id="kt_app_header" class="app-header">
    <!--begin::Header container-->
    <div class="app-container container-fluid d-flex align-items-stretch flex-stack" id="kt_app_header_container">
        <!--begin::Sidebar toggle-->
        <div class="d-flex align-items-center d-block d-lg-none ms-n3" title="Show sidebar menu">
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px me-2" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-outline ki-abstract-14 fs-2"></i>
            </div>
            <a href="{{ route('dashboard') }}">
                <span class="text-gray-900 fw-bolder fs-4">Vcan<span class="text-primary">cares</span></span>
            </a>
        </div>
        <!--end::Sidebar toggle-->

        <!--begin::Toolbar wrapper-->
        <div class="app-navbar flex-lg-grow-1" id="kt_app_header_navbar">
            <div class="app-navbar-item d-flex align-items-stretch flex-lg-grow-1">
                <!--begin::Search-->
                <div id="kt_header_search" class="header-search d-flex align-items-center w-lg-200px"
                    data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter"
                    data-kt-search-layout="menu" data-kt-search-responsive="true" data-kt-menu-trigger="auto"
                    data-kt-menu-permanent="true" data-kt-menu-placement="bottom-start">
                    <!--begin::Tablet and mobile search toggle-->
                    <div data-kt-search-element="toggle"
                        class="search-toggle-mobile d-flex d-lg-none align-items-center">
                        <div class="d-flex">
                            <i class="ki-outline ki-magnifier fs-1"></i>
                        </div>
                    </div>
                    <!--end::Tablet and mobile search toggle-->
                    <!--begin::Form-->
                    <form data-kt-search-element="form" class="d-none d-lg-block w-100 position-relative mb-5 mb-lg-0"
                        autocomplete="off">
                        <input type="hidden" />
                        <i
                            class="ki-outline ki-magnifier search-icon fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-5"></i>
                        <input type="text" class="search-input form-control form-control rounded-1 ps-13" name="search"
                            value="" placeholder="Search..." data-kt-search-element="input" />
                        <span class="search-spinner position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-5"
                            data-kt-search-element="spinner">
                            <span class="spinner-border h-15px w-15px align-middle text-gray-400"></span>
                        </span>
                        <span
                            class="search-reset btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-4"
                            data-kt-search-element="clear">
                            <i class="ki-outline ki-cross fs-2 fs-lg-1 me-0"></i>
                        </span>
                    </form>
                    <!--end::Form-->
                    <!--begin::Menu-->
                    <div data-kt-search-element="content"
                        class="menu menu-sub menu-sub-dropdown py-7 px-7 overflow-hidden w-300px w-md-350px">
                        <div data-kt-search-element="wrapper">
                            <div data-kt-search-element="results" class="d-none">
                                <div class="scroll-y mh-200px mh-lg-350px">
                                </div>
                            </div>
                            <div class="" data-kt-search-element="main">
                                <div class="text-center py-10">
                                    <i class="ki-outline ki-magnifier fs-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted fs-7">Start typing to search...</p>
                                </div>
                            </div>
                            <div data-kt-search-element="empty" class="text-center d-none">
                                <div class="pt-10 pb-10">
                                    <i class="ki-outline ki-search-list fs-4x opacity-50"></i>
                                </div>
                                <div class="pb-15 fw-semibold">
                                    <h3 class="text-gray-600 fs-5 mb-2">No result found</h3>
                                    <div class="text-muted fs-7">Please try again with a different query</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Search-->
            </div>

            <!--begin::Notifications-->
            <div class="app-navbar-item ms-1 ms-md-3">
                <div class="btn btn-icon btn-custom btn-color-gray-600 btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px position-relative"
                    id="kt_drawer_chat_toggle"
                    data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                    <i class="ki-outline ki-notification-on fs-1"></i>
                </div>
                <!--begin::Dropdown menu-->
                <div class="dropdown-menu dropdown-menu-end p-0 m-0 w-350px border-0 shadow-sm rounded">
                    <!-- Header -->
                    <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}'); background-color: #1e1e2d; padding: 1.5rem 1.5rem 1rem;">
                        <h3 class="text-white fw-semibold mb-0 mt-2">Notifications <span class="fs-8 opacity-75 ms-3" id="notification-header-count">0 reports</span></h3>
                    </div>
                    
                    <!-- Content -->
                    <div class="scroll-y mh-325px my-3 px-8" id="notification-list-container">
                        <div class="text-center py-5 text-muted">
                            Loading...
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center border-top py-3">
                        <a href="javascript:void(0)" id="mark-all-read-btn" class="btn btn-color-primary btn-active-light-primary btn-sm fw-bold">Mark All as Read</a>
                    </div>
                </div>
                <!--end::Dropdown menu-->
            </div>
            <!--end::Notifications-->
        </div>
        <!--end::Navbar-->
    </div>
    <!--end::Header container-->
</div>

@include('layouts.partials._quick-search')