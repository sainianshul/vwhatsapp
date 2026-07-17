<!--begin::Sidebar-->
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

    <!--begin::Logo-->
    <div class="app-sidebar-logo flex-shrink-0 d-none d-md-flex align-items-center px-8" id="kt_app_sidebar_logo">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-0 text-decoration-none">
            <img src="{{ asset('icon.png') }}" alt="Logo" class="h-30px me-3" />
            <h1 class="text-gray-900 fs-2 fw-bolder mb-0 ls-1">VWhatsApp</h1>
        </a>
    </div>
    <!--end::Logo-->

    <!--begin::Sidebar menu-->
    <div class="app-sidebar-menu flex-grow-1" style="min-height:0; overflow:hidden; display:flex; flex-direction:column;">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper" style="flex:1 1 0; overflow-y:auto; overflow-x:hidden; padding:1.25rem 0.75rem;">

            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold px-1" id="kt_app_sidebar_menu">

                {{-- ===================== --}}
                {{-- DASHBOARD --}}
                {{-- ===================== --}}
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-element-11 fs-2"></i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>


                {{-- ===================== --}}
                {{-- PEOPLE --}}
                {{-- ===================== --}}
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">People</span>
                    </div>
                </div>

                {{-- Users --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('users.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-people fs-2"></i>
                        </span>
                        <span class="menu-title">Users</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->fullUrlIs(route('users.index')) ? 'active' : '' }}"
                                href="{{ route('users.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">All Users</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('users.trash') ? 'active' : '' }}"
                                href="{{ route('users.trash') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Trash</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Login History --}}
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('login_history.index') ? 'active' : '' }}"
                        href="{{ route('login_history.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-security-user fs-2"></i>
                        </span>
                        <span class="menu-title">Login History</span>
                    </a>
                </div>

                {{-- ===================== --}}
                {{-- ACCOUNTS --}}
                {{-- ===================== --}}
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">WhatsApp Accounts</span>
                    </div>
                </div>

                {{-- Accounts --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('whatsapp_accounts.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-whatsapp fs-2"></i>
                        </span>
                        <span class="menu-title">Accounts</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('whatsapp_accounts.index') ? 'active' : '' }}"
                                href="{{ route('whatsapp_accounts.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">All Accounts</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('whatsapp_accounts.trash') ? 'active' : '' }}"
                                href="{{ route('whatsapp_accounts.trash') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Trash</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ===================== --}}
                {{-- MESSAGES --}}
                {{-- ===================== --}}
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Messages</span>
                    </div>
                </div>

                <!--begin::Menu Item-->
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('whatsapp_messages.create') ? 'active' : '' }}" href="{{ route('whatsapp_messages.create') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-send fs-2"></i>
                        </span>
                        <span class="menu-title">Send Message</span>
                    </a>
                </div>
                <!--end::Menu Item-->
                <!--begin::Menu Item-->
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('admin.bulk_campaigns.*') ? 'active' : '' }}" href="{{ route('admin.bulk_campaigns.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-rocket fs-2"></i>
                        </span>
                        <span class="menu-title">Bulk Campaigns</span>
                    </a>
                </div>
                <!--end::Menu Item-->

                <!--begin::Menu Item-->
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('admin.media_library.*') ? 'active' : '' }}" href="{{ route('admin.media_library.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-folder fs-2"></i>
                        </span>
                        <span class="menu-title">Media Library</span>
                    </a>
                </div>
                <!--end::Menu Item-->

                <!--begin::Menu Item-->
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('whatsapp_messages.index') ? 'active' : '' }}" href="{{ route('whatsapp_messages.index') }}">
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
                    <a class="menu-link {{ request()->routeIs('admin.developer_settings.*') ? 'active' : '' }}" href="{{ route('admin.developer_settings.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-code fs-2"></i>
                        </span>
                        <span class="menu-title">API Settings</span>
                    </a>
                </div>

                {{-- ===================== --}}
                {{-- SYSTEM --}}
                {{-- ===================== --}}
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">System</span>
                    </div>
                </div>

                {{-- Settings --}}
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

    @include('layouts.partials._sidebar_footer')

</div>
<!--end::Sidebar-->
