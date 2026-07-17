<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Sign In — VWhatsApp</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700&display=swap" />

    <link rel="preload" href="{{ asset('assets/plugins/global/fonts/keenicons/keenicons-outline.woff') }}" as="font" type="font/woff" crossorigin />

    <style>
        @font-face {
            font-family: keenicons-outline;
            font-display: swap;
            src: url('{{ asset("assets/plugins/global/fonts/keenicons/keenicons-outline.woff") }}') format("woff"),
                 url('{{ asset("assets/plugins/global/fonts/keenicons/keenicons-outline.ttf") }}') format("truetype");
        }
        body { background-color: #f4f6f9; }
        
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

        .text-primary { color: var(--bs-primary) !important; }
        .bg-primary { background-color: var(--bs-primary) !important; }
        .badge-light-primary { background-color: var(--bs-primary-light) !important; color: var(--bs-primary) !important; }
        .btn-primary { background-color: var(--bs-primary) !important; border-color: var(--bs-primary) !important; color: #fff !important; }
        .btn-primary:hover { background-color: var(--bs-primary-active) !important; border-color: var(--bs-primary-active) !important; }
        .btn-active-light-primary:hover { background-color: var(--bs-primary-light) !important; color: var(--bs-primary) !important; }
    </style>

    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('icon.png') }}" />

    <script>
        var defaultThemeMode = "light";
        var themeMode = localStorage.getItem("data-bs-theme") || defaultThemeMode;
        if (themeMode === "system") themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    </script>
</head>

<body id="kt_body" class="app-blank bgi-size-cover bgi-position-center">

    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">

            <div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">

                <div class="d-flex justify-content-between flex-column-auto mb-8 w-100" style="max-width: 420px;">
                    <a href="#" class="d-flex align-items-center text-decoration-none">
                        <img src="{{ asset('icon.png') }}" alt="Logo" class="h-35px me-3" />
                        <span class="text-gray-900 fw-bolder fs-2">VWhatsApp</span>
                    </a>
                    <span class="badge badge-light-primary fw-semibold fs-8 px-4 py-2">Admin Portal</span>
                </div>

                <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-3 w-100 p-10 border border-gray-200 shadow-sm" style="max-width: 420px;">

                    <div class="d-flex flex-center flex-column flex-column-fluid pb-5">
                        <h1 class="text-gray-900 fw-bolder mb-1 fs-2x">Welcome Back</h1>
                        <div class="text-muted fw-semibold fs-6">Please sign in to your account</div>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-danger d-flex align-items-start p-5 mb-7">
                            <i class="ki-outline ki-shield-cross fs-2hx text-danger me-3 mt-1 flex-shrink-0"></i>
                            <div class="d-flex flex-column">
                                <h5 class="mb-1 text-danger fw-semibold">Authentication Failed</h5>
                                <span class="text-gray-700 fs-7">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger d-flex align-items-start p-5 mb-7">
                            <i class="ki-outline ki-shield-cross fs-2hx text-danger me-3 mt-1 flex-shrink-0"></i>
                            <div class="d-flex flex-column">
                                <h5 class="mb-1 text-danger fw-semibold">Authentication Failed</h5>
                                @foreach ($errors->all() as $error)
                                    <span class="text-gray-700 fs-7">{{ $error }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.custom') }}" id="kt_login_form" autocomplete="off" novalidate>
                        @csrf

                        <div class="fv-row mb-5">
                            <label class="form-label text-gray-900 fw-medium fs-7 mb-2 required">
                                Email Address
                            </label>
                            <div class="input-group border border-gray-300 rounded">
                                <span class="input-group-text bg-transparent border-0">
                                    <i class="ki-outline ki-sms fs-4 text-gray-500"></i>
                                </span>
                                <input
                                    type="text"
                                    name="email"
                                    id="email"
                                    value="{{ old('email') }}"
                                    placeholder="Email"
                                    autocomplete="off"
                                    class="form-control form-control-transparent border-0 text-gray-900 @error('email') is-invalid @enderror"
                                />
                            </div>
                            @error('email')
                                <div class="fv-plugins-message-container mt-2">
                                    <div class="fv-help-block">
                                        <span>{{ $message }}</span>
                                    </div>
                                </div>
                            @enderror
                        </div>

                        <div class="fv-row mb-5" data-kt-password-meter="true">
                            <label class="form-label text-gray-900 fw-medium fs-7 mb-2 required">
                                Password
                            </label>
                            <div class="input-group border border-gray-300 rounded" id="kt_password_input">
                                <span class="input-group-text bg-transparent border-0">
                                    <i class="ki-outline ki-lock-2 fs-4 text-gray-500"></i>
                                </span>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    placeholder="Password"
                                    autocomplete="off"
                                    class="form-control form-control-transparent border-0 text-gray-900 @error('password') is-invalid @enderror"
                                />
                                <span class="btn btn-icon bg-transparent border-0" id="kt_password_toggle">
                                    <i class="ki-outline ki-eye fs-4 text-gray-500" id="eye_icon"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="fv-plugins-message-container mt-2">
                                    <div class="fv-help-block">
                                        <span>{{ $message }}</span>
                                    </div>
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex flex-stack flex-wrap gap-3 mb-8">
                            <label class="form-check form-check-custom form-check-sm">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="remember"
                                    id="remember"
                                    value="1"
                                    {{ old('remember') ? 'checked' : '' }}
                                />
                                <span class="form-check-label text-gray-600 fw-normal fs-7 ms-1">
                                    Keep me signed in
                                </span>
                            </label>
                        </div>

                        <div class="d-grid mb-8">
                            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                <span class="indicator-label text-uppercase fw-bold fs-8">
                                    Sign in to dashboard
                                </span>
                                <span class="indicator-progress">
                                    Signing in...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>

                    </form>



                </div>

                <div class="d-flex justify-content-center pt-8">
                    <span class="text-gray-400 fs-8 fw-normal">
                        &copy; {{ date('Y') }} Schotech. All rights reserved.
                    </span>
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('kt_password_toggle').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye_icon');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.className = isPassword ? 'ki-outline ki-eye-slash fs-4' : 'ki-outline ki-eye fs-4';
        });

        document.getElementById('kt_login_form').addEventListener('submit', function () {
            const btn = document.getElementById('kt_sign_in_submit');
            btn.setAttribute('data-kt-indicator', 'on');
            btn.disabled = true;
        });
    </script>

</body>
</html>
