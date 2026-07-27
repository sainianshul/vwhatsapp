<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Quick Navigation Routes
        const pages = [
            // Dashboard
            { name: 'Dashboard', route: '{{ route('dashboard') }}', icon: 'ki-outline ki-element-11', color: 'primary' },

            // Users
            { name: 'All Users', route: '{{ route('users.index') }}', icon: 'ki-outline ki-people', color: 'primary' },
            { name: 'Add User', route: '{{ route('users.create') }}', icon: 'ki-outline ki-user-square', color: 'success' },

            // WhatsApp Accounts
            { name: 'All Accounts', route: '{{ route('whatsapp_accounts.index') }}', icon: 'ki-outline ki-whatsapp', color: 'success' },
            { name: 'Trash Accounts', route: '{{ route('whatsapp_accounts.trash') }}', icon: 'ki-outline ki-trash', color: 'danger' },

            // Messages
            { name: 'Send Message', route: '{{ route('whatsapp_messages.create') }}', icon: 'ki-outline ki-send', color: 'primary' },
            { name: 'Message Logs', route: '{{ route('whatsapp_messages.index') }}', icon: 'ki-outline ki-document', color: 'info' },

            // Campaigns & Media
            { name: 'Bulk Campaigns', route: '{{ route('admin.bulk_campaigns.index') }}', icon: 'ki-outline ki-rocket', color: 'warning' },
            { name: 'Media Library', route: '{{ route('admin.media_library.index') }}', icon: 'ki-outline ki-folder', color: 'info' },

            // Support
            { name: 'Help & Support', route: '{{ route('tickets.index') }}', icon: 'ki-outline ki-questionnaire-tablet', color: 'primary' },
            { name: 'Create Ticket', route: '{{ route('tickets.create') }}', icon: 'ki-outline ki-plus-square', color: 'success' },

            // System
            { name: 'Login History', route: '{{ route('login_history.index') }}', icon: 'ki-outline ki-security-user', color: 'secondary' },
            { name: 'Developer Settings', route: '{{ route('admin.developer_settings.index') }}', icon: 'ki-outline ki-code', color: 'danger' }
        ];

        const input = document.querySelector('.search-input');
        const menu = document.querySelector('[data-kt-search-element="content"]');
        const main = document.querySelector('[data-kt-search-element="main"]');
        const empty = document.querySelector('[data-kt-search-element="empty"]');
        const results = document.querySelector('[data-kt-search-element="results"]');
        const resultsContainer = results.querySelector('.scroll-y');
        const resetBtn = document.querySelector('[data-kt-search-element="clear"]');

        if (!input || !menu) return;

        // Ensure menu acts as a proper dropdown
        menu.style.position = 'absolute';
        menu.style.top = '100%';
        menu.style.left = '0';
        menu.style.zIndex = '105';
        menu.style.backgroundColor = '#fff';
        menu.style.border = '1px solid #eff2f5';
        menu.style.boxShadow = '0px 0px 50px 0px rgba(82, 63, 105, 0.15)';
        menu.style.display = 'none';

        input.addEventListener('focus', function () {
            menu.style.display = 'block';
        });

        document.addEventListener('click', function (e) {
            const isClickInside = input.contains(e.target) || menu.contains(e.target);
            if (!isClickInside) {
                menu.style.display = 'none';
            }
        });

        resetBtn.addEventListener('click', function () {
            input.value = '';
            input.dispatchEvent(new Event('input'));
            input.focus();
        });

        input.addEventListener('input', function () {
            const val = this.value.trim().toLowerCase();

            if (val === '') {
                main.classList.remove('d-none');
                empty.classList.add('d-none');
                results.classList.add('d-none');
                resetBtn.classList.add('d-none');
                return;
            }

            resetBtn.classList.remove('d-none');

            const matches = pages.filter(p => p.name.toLowerCase().includes(val));

            if (matches.length === 0) {
                main.classList.add('d-none');
                results.classList.add('d-none');
                empty.classList.remove('d-none');
            } else {
                main.classList.add('d-none');
                empty.classList.add('d-none');
                results.classList.remove('d-none');

                resultsContainer.innerHTML = '';
                matches.forEach(m => {
                    const html = `
                        <a href="${m.route}" class="d-flex align-items-center p-3 rounded bg-state-light bg-state-opacity-50 mb-2 text-decoration-none transition-base hover-scale">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-${m.color}">
                                    <i class="${m.icon} fs-2 text-${m.color}"></i>
                                </span>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="fs-6 fw-bold text-gray-900 text-hover-${m.color}">${m.name}</span>
                                <span class="fs-8 fw-semibold text-muted">Jump to this page</span>
                            </div>
                            <i class="ki-outline ki-arrow-right fs-4 text-muted ms-auto"></i>
                        </a>
                    `;
                    resultsContainer.insertAdjacentHTML('beforeend', html);
                });
            }
        });
    });
</script>
