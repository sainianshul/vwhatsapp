<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VWhatsApp - Enterprise WhatsApp Automation</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('icon.png') }}" />
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAFAFA;
            color: #181C32;
        }
        
        .text-primary-custom { color: #128C7E !important; }
        .bg-primary-custom { background-color: #128C7E !important; }
        .btn-primary-custom { 
            background-color: #128C7E !important; 
            border-color: #128C7E !important; 
            color: #fff !important; 
            transition: all 0.3s ease;
        }
        .btn-primary-custom:hover { 
            background-color: #075E54 !important; 
            box-shadow: 0 10px 20px rgba(18, 140, 126, 0.2); 
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section {
            padding: 160px 0 100px;
            background: radial-gradient(circle at center top, rgba(18,140,126,0.08) 0%, rgba(255,255,255,0) 70%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background: rgba(18, 140, 126, 0.1);
            color: #128C7E;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(18, 140, 126, 0.2);
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            letter-spacing: -1.5px;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: #181C32;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #5E6278;
            max-width: 650px;
            margin: 0 auto 2.5rem;
            line-height: 1.6;
        }

        /* Mockup */
        .mockup-container {
            position: relative;
            margin: 60px auto 0;
            max-width: 900px;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255,255,255,0.4);
            overflow: hidden;
            transform: perspective(1000px) rotateX(2deg);
            transition: transform 0.5s ease;
        }
        
        .mockup-container:hover {
            transform: perspective(1000px) rotateX(0deg) translateY(-5px);
        }

        .mockup-container img {
            width: 100%;
            display: block;
        }

        /* Trust Stats */
        .stats-section {
            padding: 60px 0;
            border-top: 1px solid #EFF2F5;
            border-bottom: 1px solid #EFF2F5;
            background: #fff;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #128C7E;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            color: #7E8299;
            font-weight: 500;
        }

        /* Features */
        .features-section {
            padding: 120px 0;
            background-color: #FAFAFA;
        }

        .feature-card {
            background: #fff;
            border: 1px solid #EFF2F5;
            border-radius: 1.5rem;
            padding: 3rem 2rem;
            height: 100%;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            box-shadow: 0 15px 35px rgba(0,0,0,0.03);
            transform: translateY(-5px);
            border-color: rgba(18, 140, 126, 0.2);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: rgba(18, 140, 126, 0.1);
            color: #128C7E;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }

        /* CTA */
        .cta-section {
            padding: 100px 0;
            background: #181C32;
            position: relative;
            overflow: hidden;
        }
        
        .cta-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at center, rgba(18,140,126,0.15) 0%, transparent 60%);
        }

    </style>
</head>
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed">
    
    <!-- Navbar -->
    @include('layouts.partials._front_navbar')

    <!-- Hero Section -->
    <div class="hero-section text-center">
        <div class="container">
            <div class="hero-badge">
                <i class="ki-outline ki-verify fs-4 me-2"></i> Leading WhatsApp CRM
            </div>
            <h1 class="hero-title">
                Automate & Scale Your <br>
                <span class="text-primary-custom">WhatsApp Operations</span>
            </h1>
            <p class="hero-subtitle">
                The ultimate platform for businesses to manage multiple accounts, run bulk campaigns, and engage customers seamlessly.
            </p>
            <div class="d-flex justify-content-center gap-4">
                <a href="#features" class="btn btn-light btn-lg px-8 fw-bold border border-gray-300 text-gray-700">Explore Features</a>
            </div>
            
            <div class="mockup-container">
                <img src="{{ asset('images/mockup.png') }}" alt="VWhatsApp Dashboard Mockup">
            </div>
        </div>
    </div>

    <!-- Trust Stats -->
    <div class="stats-section">
        <div class="container">
            <div class="row g-10">
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">99.9%</div>
                        <div class="stat-label">Uptime Reliability</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">10M+</div>
                        <div class="stat-label">Messages Processed</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Dedicated Support</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="features-section">
        <div class="container">
            <div class="text-center mb-17">
                <h2 class="fs-2hx fw-bolder text-gray-900 mb-4">Everything You Need to Succeed</h2>
                <div class="fs-5 text-gray-500 fw-medium">Powerful tools engineered for growth and engagement.</div>
            </div>
            
            <div class="row g-10">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="ki-outline ki-abstract-26 fs-1 text-primary-custom"></i>
                        </div>
                        <h3 class="fs-3 fw-bold text-gray-900 mb-4">Multi-Account CRM</h3>
                        <p class="fs-6 text-gray-500 mb-0">Connect and manage unlimited WhatsApp numbers from a single, unified inbox. Never miss a customer conversation again.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="ki-outline ki-rocket fs-1 text-primary-custom"></i>
                        </div>
                        <h3 class="fs-3 fw-bold text-gray-900 mb-4">Bulk Broadcasts</h3>
                        <p class="fs-6 text-gray-500 mb-0">Reach thousands of customers instantly. Schedule personalized campaigns with media, documents, and interactive buttons.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="ki-outline ki-setting-2 fs-1 text-primary-custom"></i>
                        </div>
                        <h3 class="fs-3 fw-bold text-gray-900 mb-4">Developer API</h3>
                        <p class="fs-6 text-gray-500 mb-0">Integrate WhatsApp messaging into your own software, website, or ERP seamlessly using our robust RESTful endpoints.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="cta-section text-center">
        <div class="cta-bg"></div>
        <div class="container position-relative z-index-1">
            <h2 class="fs-1 text-white fw-bolder mb-6">Ready to transform your communication?</h2>
            <p class="text-gray-400 fs-5 mb-10 max-w-600px mx-auto">
                Join thousands of modern businesses scaling their engagement through intelligent WhatsApp automation.
            </p>
            <a href="{{ route('login') }}" class="btn btn-primary-custom btn-lg px-10 py-4 fw-bolder">Log in to Dashboard</a>
        </div>
    </div>

    <!-- Enhanced Footer -->
    @include('layouts.partials._front_footer')

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
</body>
</html>
