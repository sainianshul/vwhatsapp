/**
 * VCanCares Admin Sidebar — Simple, Stable, Lightweight
 * Zero dependencies. Handles: Accordions, Mobile Drawer, User Dropdown, Theme Mode
 */
(function () {
    'use strict';

    var sidebar = document.getElementById('kt_app_sidebar');
    var mobileToggle = document.getElementById('kt_app_sidebar_mobile_toggle');
    var overlay = null;

    // ===== 1. ACCORDIONS =====
    function initAccordions() {
        if (!sidebar) return;
        sidebar.addEventListener('click', function (e) {
            var link = e.target.closest('.menu-accordion > .menu-link');
            if (!link) return;

            var href = link.getAttribute('href');
            if (!href || href === '#' || href === 'javascript:;') {
                e.preventDefault();
            }

            var item = link.parentElement;
            var sub = item.querySelector(':scope > .menu-sub');
            if (!sub) return;

            if (item.classList.contains('show')) {
                item.classList.remove('show', 'here');
            } else {
                // Close siblings
                var siblings = item.parentElement.children;
                for (var i = 0; i < siblings.length; i++) {
                    if (siblings[i] !== item && siblings[i].classList.contains('show')) {
                        siblings[i].classList.remove('show', 'here');
                    }
                }
                item.classList.add('show', 'here');
            }
        });
    }

    // ===== 2. MOBILE DRAWER =====
    function getOverlay() {
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'sidebar-overlay';
            overlay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.3);z-index:100;display:none;';
            document.body.appendChild(overlay);
            overlay.addEventListener('click', closeDrawer);
        }
        return overlay;
    }

    function openDrawer() {
        if (!sidebar) return;
        sidebar.classList.add('drawer-on');
        getOverlay().style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        if (!sidebar) return;
        sidebar.classList.remove('drawer-on');
        if (overlay) overlay.style.display = 'none';
        document.body.style.overflow = '';
    }

    function initMobileDrawer() {
        if (!mobileToggle) return;
        mobileToggle.addEventListener('click', function (e) {
            e.preventDefault();
            sidebar && sidebar.classList.contains('drawer-on') ? closeDrawer() : openDrawer();
        });

        // Auto-close on resize to desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992 && sidebar && sidebar.classList.contains('drawer-on')) {
                closeDrawer();
            }
        });
    }

    // ===== 3. USER DROPDOWN =====
    function initUserDropdown() {
        var footer = document.getElementById('kt_app_sidebar_footer');
        if (!footer) return;

        var trigger = footer.querySelector('[data-kt-menu-trigger]');
        var dropdown = footer.querySelector('.menu-sub-dropdown');
        if (!trigger || !dropdown) return;

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var isOpen = dropdown.classList.contains('show');
            closeAllDropdowns();
            if (!isOpen) {
                var rect = trigger.getBoundingClientRect();
                dropdown.style.cssText = 'display:block;position:fixed;z-index:107;animation:fadeIn .15s ease;';
                dropdown.style.bottom = (window.innerHeight - rect.top + 5) + 'px';
                dropdown.style.left = rect.left + 'px';
                dropdown.classList.add('show');
            }
        });

        document.addEventListener('click', function (e) {
            if (!footer.contains(e.target)) closeAllDropdowns();
        });
    }

    function closeAllDropdowns() {
        var open = document.querySelectorAll('.menu-sub-dropdown.show');
        for (var i = 0; i < open.length; i++) {
            open[i].classList.remove('show');
            open[i].style.display = '';
        }
    }

    // ===== 4. THEME MODE =====
    function initThemeMode() {
        var links = document.querySelectorAll('[data-kt-element="mode"]');
        var selects = document.querySelectorAll('[data-kt-element="mode-select"]');
        var current = localStorage.getItem('data-bs-theme') || 'light';

        function setTheme(val) {
            var resolved = val === 'system'
                ? (window.matchMedia('(prefers-color-scheme:dark)').matches ? 'dark' : 'light')
                : val;
            document.documentElement.setAttribute('data-bs-theme', resolved);
            localStorage.setItem('data-bs-theme', val);
            closeAllDropdowns();
        }

        // Link clicks
        for (var i = 0; i < links.length; i++) {
            if (links[i].getAttribute('data-kt-value') === current) links[i].classList.add('active');
            links[i].addEventListener('click', function (e) {
                e.preventDefault();
                var val = this.getAttribute('data-kt-value');
                if (!val) return;
                for (var j = 0; j < links.length; j++) links[j].classList.remove('active');
                this.classList.add('active');
                setTheme(val);
            });
        }

        // Select dropdowns
        for (var k = 0; k < selects.length; k++) {
            selects[k].value = current;
            selects[k].addEventListener('change', function (e) {
                setTheme(this.value);
            });
        }
    }

    // ===== INIT =====
    function init() {
        initAccordions();
        initMobileDrawer();
        initUserDropdown();
        initThemeMode();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
