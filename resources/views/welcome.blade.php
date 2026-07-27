<!DOCTYPE html>
<html lang="en">
<head>
    <title>VWhatsApp - Premium WhatsApp Automation SaaS</title>
    <meta charset="utf-8" />
    <meta name="description" content="Scale your business with powerful WhatsApp automation, bulk messaging, and multi-account management." />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ asset('icon.png') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            background-color: #f5f8fa;
            font-family: 'Inter', sans-serif;
        }
        .hero-section {
            background-color: #ffffff;
            padding: 150px 0 120px;
            border-bottom: 1px solid #eff2f5;
        }
        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: #181c32; /* text-gray-900 */
        }
        .hero-subtitle {
            font-size: 1.25rem;
            color: #7e8299; /* text-muted */
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }
        .feature-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 40px 30px;
            border: 1px solid #eff2f5;
            transition: all 0.3s ease;
            height: 100%;
        }
        .feature-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-color: var(--bs-success);
            transform: translateY(-5px);
        }
        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        .navbar-brand img {
            height: 40px;
        }
        .btn-custom {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed">
    <!-- Navbar -->
    @include('layouts.partials._front_navbar')

    <!-- Hero Section -->
    <div class="hero-section text-center position-relative">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-8 mt-15 mt-lg-0">
                    <span class="badge badge-light-success py-2 px-4 mb-5 fs-6 fw-bold">#1 WhatsApp SaaS Platform</span>
                    <h1 class="hero-title">Powerful WhatsApp<br/>Automation</h1>
                    <p class="hero-subtitle px-lg-15">
                        Scale your business with bulk messaging, seamless multi-account management, and robust developer APIs. Connect with your customers instantly, reliably, and efficiently.
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-4">
                        <a href="{{ route('login') }}" class="btn btn-success btn-custom shadow-sm">Get Started Now</a>
                        <a href="#features" class="btn btn-light btn-custom text-gray-800 fw-bold border border-gray-300">Explore Features</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-20 bg-light">
        <div class="container">
            <div class="text-center mb-15">
                <h2 class="fs-2hx fw-bold text-gray-900 mb-3">Enterprise-Grade Features</h2>
                <div class="fs-5 text-gray-600 fw-semibold">Everything you need to manage your WhatsApp campaigns.</div>
            </div>
            
            <div class="row g-10">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-light-success text-success">
                            <i class="ki-outline ki-whatsapp fs-1"></i>
                        </div>
                        <h3 class="fs-3 fw-bold text-gray-900 mb-4">Multi-Account Management</h3>
                        <p class="fs-6 text-gray-600">Connect multiple WhatsApp accounts in one dashboard. Switch between accounts seamlessly and monitor their connection status in real-time.</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-light-success text-success">
                            <i class="ki-outline ki-rocket fs-1"></i>
                        </div>
                        <h3 class="fs-3 fw-bold text-gray-900 mb-4">Bulk Messaging Campaigns</h3>
                        <p class="fs-6 text-gray-600">Upload CSV files to send personalized bulk messages. Schedule campaigns, track success rates, and manage high-volume messaging effortlessly.</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-light-success text-success">
                            <i class="ki-outline ki-picture fs-1"></i>
                        </div>
                        <h3 class="fs-3 fw-bold text-gray-900 mb-4">Rich Media Support</h3>
                        <p class="fs-6 text-gray-600">Send images, videos, PDFs, and audio messages. Use our robust Media Library to organize your assets and attach them directly to your campaigns.</p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-light-success text-success">
                            <i class="ki-outline ki-code fs-1"></i>
                        </div>
                        <h3 class="fs-3 fw-bold text-gray-900 mb-4">Developer API</h3>
                        <p class="fs-6 text-gray-600">Integrate WhatsApp sending capabilities into your own applications using our well-documented REST APIs and secure token management.</p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-light-success text-success">
                            <i class="ki-outline ki-chart-simple fs-1"></i>
                        </div>
                        <h3 class="fs-3 fw-bold text-gray-900 mb-4">Analytics & Logs</h3>
                        <p class="fs-6 text-gray-600">Track every message sent. View detailed message logs, campaign statistics, delivery rates, and account activity from a single pane of glass.</p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-light-success text-success">
                            <i class="ki-outline ki-shield-tick fs-1"></i>
                        </div>
                        <h3 class="fs-3 fw-bold text-gray-900 mb-4">Secure & Reliable</h3>
                        <p class="fs-6 text-gray-600">Built on top of robust architecture. Your data is isolated, secure, and encrypted. Dedicated support ticketing system included for all tenants.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-15 bg-white text-center border-top border-gray-200">
        <div class="container">
            <h2 class="fs-2hx text-gray-900 fw-bold mb-5">Ready to automate your WhatsApp communication?</h2>
            <a href="{{ route('login') }}" class="btn btn-success btn-lg px-10 py-4 fw-bolder shadow-sm">Start Your Journey Today</a>
        </div>
    </div>

    <!-- Enhanced Footer -->
    @include('layouts.partials._front_footer')

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
</body>
</html>
