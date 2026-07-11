<!--begin::Sidebar-->
<div id="kt_app_sidebar" class="app-sidebar flex-column">

    <!--begin::Logo-->
    <div class="app-sidebar-logo flex-shrink-0 d-none d-md-flex align-items-center px-8" id="kt_app_sidebar_logo">
        <a href="{{ route('dashboard') }}" class="text-decoration-none">
            <h1 class="text-gray-900 fs-2 fw-bolder mb-0 ls-1">Social Manager</h1>
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
                {{-- INTELLIGENCE --}}
                {{-- ===================== --}}
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Intelligence</span>
                    </div>
                </div>

                {{-- Profiles --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('subjects.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-profile-user fs-2"></i>
                        </span>
                        <span class="menu-title">Profiles</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->fullUrlIs(route('subjects.index')) ? 'active' : '' }}"
                                href="{{ route('subjects.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">All Profiles</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('subjects.create') ? 'active' : '' }}"
                                href="{{ route('subjects.create') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Add Profile</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('subjects.trash') ? 'active' : '' }}"
                                href="{{ route('subjects.trash') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Trash</span>
                            </a>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->role !== \App\Models\User::ROLE_USER)
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
                            <a class="menu-link {{ request()->fullUrlIs(route('users.index', ['status' => 'active'])) ? 'active' : '' }}"
                                href="{{ route('users.index', ['status' => 'active']) }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Active</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->fullUrlIs(route('users.index', ['status' => 'blocked'])) ? 'active' : '' }}"
                                href="{{ route('users.index', ['status' => 'blocked']) }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Blocked</span>
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
                {{-- AUTOMATION --}}
                {{-- ===================== --}}
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Automation</span>
                    </div>
                </div>

                {{-- Bots --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('bots.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-android fs-2"></i>
                        </span>
                        <span class="menu-title">Bots Manager</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->fullUrlIs(route('bots.index')) ? 'active' : '' }}"
                                href="{{ route('bots.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">All Bots</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->fullUrlIs(route('bots.index', ['platform' => 'facebook'])) ? 'active' : '' }}"
                                href="{{ route('bots.index', ['platform' => 'facebook']) }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Facebook</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->fullUrlIs(route('bots.index', ['platform' => 'instagram'])) ? 'active' : '' }}"
                                href="{{ route('bots.index', ['platform' => 'instagram']) }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Instagram</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('bots.trash') ? 'active' : '' }}"
                                href="{{ route('bots.trash') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Trash</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Comments Templates --}}
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('automation-templates.*') ? 'active' : '' }}"
                        href="{{ route('automation-templates.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-magic fs-2"></i>
                        </span>
                        <span class="menu-title">Comments Templates</span>
                    </a>
                </div>

                {{-- Command Center --}}
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('command-center.*') ? 'active' : '' }}"
                        href="{{ route('command-center.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-status fs-2"></i>
                        </span>
                        <span class="menu-title">Command Center</span>
                    </a>
                </div>
                @endif

                {{-- ===================== --}}
                {{-- OPERATIONS --}}
                {{-- ===================== --}}
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Operations</span>
                    </div>
                </div>



                {{-- Care Requests --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('admin.requests.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-clipboard fs-2"></i>
                        </span>
                        <span class="menu-title">Care Requests</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.requests.index') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">All Requests</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.requests.today') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Today Requests</span>
                            </a>
                        </div>

                    </div>
                </div>

                {{-- Bookings --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('admin.bookings.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-calendar-tick fs-2"></i>
                        </span>
                        <span class="menu-title">Bookings</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.bookings.index') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">All Bookings</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.bookings.active') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Active Bookings</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.bookings.cancelled') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Cancelled Bookings</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Bids --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('admin.bids.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-price-tag fs-2"></i>
                        </span>
                        <span class="menu-title">Bids</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.bids.index') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">All Bids</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.bids.today') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Today Bids</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.bids.active') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Active Bids</span>
                            </a>
                        </div>

                    </div>
                </div>

                {{-- Services --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('admin.services.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-heart fs-2"></i>
                        </span>
                        <span class="menu-title">Services</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.services.care-types.*') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Care Types</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ===================== --}}
                {{-- FINANCE --}}
                {{-- ===================== --}}
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Finance</span>
                    </div>
                </div>

                {{-- Payments --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('admin.payments.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-dollar fs-2"></i>
                        </span>
                        <span class="menu-title">Payments</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.payments.transactions') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Transactions</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.payments.payouts') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Nurse Payouts</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.payments.refunds') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Refunds</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ===================== --}}
                {{-- INSIGHTS --}}
                {{-- ===================== --}}
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Insights</span>
                    </div>
                </div>

                {{-- Reports --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('admin.reports.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-chart-line fs-2"></i>
                        </span>
                        <span class="menu-title">Reports</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.reports.revenue') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Revenue</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.reports.nurse-activity') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Nurse Activity</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.reports.requests') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Request Reports</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ===================== --}}
                {{-- SUPPORT --}}
                {{-- ===================== --}}
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Support</span>
                    </div>
                </div>

                <!-- Support Tickets -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('admin.support.index') || request()->routeIs('admin.support.show') ? 'active' : '' }}" href="#">
                        <span class="menu-icon">
                            <i class="ki-outline ki-message-text-2 fs-2"></i>
                        </span>
                        <span class="menu-title">Tickets</span>
                    </a>
                </div>

                <!-- FAQ -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('admin.support.faqs.*') ? 'active' : '' }}" href="#">
                        <span class="menu-icon">
                            <i class="ki-outline ki-message-question fs-2"></i>
                        </span>
                        <span class="menu-title">FAQ</span>
                    </a>
                </div>

                <!-- Support Categories -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('admin.support.categories.*') ? 'active' : '' }}" href="#">
                        <span class="menu-icon">
                            <i class="ki-outline ki-category fs-2"></i>
                        </span>
                        <span class="menu-title">Support Categories</span>
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

                {{-- System --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('admin.system.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-setting-2 fs-2"></i>
                        </span>
                        <span class="menu-title">System</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.system.error-logs') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Error Logs</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.system.communication-logs.*') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Communication Logs</span>
                            </a>
                        </div>
                        {{-- <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.system.failed-jobs') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Failed Jobs</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.system.queue') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Queue Monitor</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.system.backups') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Backups</span>
                            </a>
                        </div> --}}
                        <div class="menu-item">
                            <a class="menu-link" href="{{ url('/api/documentation') }}" target="_blank">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">API</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Settings --}}
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->routeIs('admin.settings.*') ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-gear fs-2"></i>
                        </span>
                        <span class="menu-title">Settings</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">General</span>
                            </a>
                        </div>
                        {{-- <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.settings.app') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">App Config</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.settings.roles') ? 'active' : '' }}"
                                href="#">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Roles &amp; Permissions</span>
                            </a>
                        </div> --}}
                    </div>
                </div>

            </div>
            <!--end::Menu-->
        </div>
    </div>
    <!--end::Sidebar menu-->

    @include('layouts.partials._sidebar_footer')

</div>
<!--end::Sidebar-->
