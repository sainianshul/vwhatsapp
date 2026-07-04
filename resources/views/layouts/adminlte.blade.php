<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SocialBot | @yield('title', 'Dashboard')</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome / Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- AdminLTE 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">

    <!-- Premium Custom UI Styles -->
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #ec4899;
            --bg-body: #f3f4f6;
            --sidebar-bg: #0f172a;
            --sidebar-hover: rgba(255, 255, 255, 0.08);
            --card-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-body); 
            color: #334155;
        }

        h1, h2, h3, h4, h5, h6, .brand-text {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
        }

        /* App Header */
        .app-header { 
            background: var(--glass-bg); 
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            z-index: 1040;
        }

        /* Sidebar */
        .app-sidebar { 
            background: var(--sidebar-bg); 
            box-shadow: 4px 0 24px rgba(0,0,0,0.1);
            border-right: none;
        }
        .sidebar-brand {
            background: var(--sidebar-bg);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding: 1.5rem 1rem;
        }
        .brand-link { color: #fff !important; text-decoration: none; }
        .brand-text { font-size: 1.5rem; letter-spacing: -0.5px; background: linear-gradient(135deg, #a855f7, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800; }
        
        .sidebar-wrapper { background: var(--sidebar-bg); }
        .nav-sidebar .nav-item { margin-bottom: 4px; }
        .nav-sidebar .nav-link { 
            color: #94a3b8; 
            border-radius: 10px; 
            margin: 0 12px; 
            padding: 12px 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            font-weight: 500;
        }
        .nav-sidebar .nav-link i { font-size: 1.1rem; transition: transform 0.3s; }
        .nav-sidebar .nav-link:hover { 
            background-color: var(--sidebar-hover); 
            color: #fff; 
            transform: translateX(4px);
        }
        .nav-sidebar .nav-link:hover i { transform: scale(1.2); color: #a855f7; }
        .nav-sidebar .nav-link.active { 
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff; 
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
        }
        .nav-sidebar .nav-link.active i { color: #fff; }

        /* Premium Cards */
        .card { 
            border: 1px solid rgba(255,255,255,0.8); 
            border-radius: 20px; 
            box-shadow: var(--card-shadow); 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
            overflow: hidden;
        }
        .card:hover { 
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.12); 
            transform: translateY(-2px);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
        }

        /* Buttons */
        .btn { 
            border-radius: 10px; 
            font-weight: 600; 
            padding: 10px 20px;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }
        .btn-primary { 
            background: linear-gradient(135deg, var(--primary), var(--secondary)); 
            border: none;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
            background: linear-gradient(135deg, var(--primary-hover), var(--secondary)); 
        }
        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        .btn-outline-primary:hover {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            letter-spacing: 0.5px;
        }

        /* Page Title Area */
        .app-content-header {
            padding: 2rem 1rem 1rem;
        }
        .app-content-header h3 {
            font-size: 2rem;
            color: #1e293b;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up {
            animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg">
    <div class="app-wrapper">
        <!-- Header -->
        <nav class="app-header navbar navbar-expand">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item"> 
                        <a class="nav-link text-dark" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list fs-4"></i>
                        </a> 
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle text-dark fw-medium" data-bs-toggle="dropdown">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2 shadow-sm" style="width:35px;height:35px;font-weight:bold;">
                                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'User' }}</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end border-0 shadow-lg rounded-4 mt-2">
                            <li class="user-footer bg-white rounded-4 p-3 text-center"> 
                                <a href="{{ route('signout') }}" class="btn btn-outline-danger rounded-pill px-4 w-100">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sign out
                                </a> 
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- /.Header -->

        <!-- Sidebar -->
        <aside class="app-sidebar" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="{{ route('dashboard') }}" class="brand-link">
                    <span class="brand-text ms-2"><i class="bi bi-radar me-2 text-white"></i>SocialBot</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-4">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-grid-1x2-fill"></i>
                                <p>Overview</p>
                            </a>
                        </li>
                        
                        <li class="nav-header text-uppercase text-secondary fw-bold mt-4 mb-2 ms-4" style="font-size: 0.75rem; letter-spacing: 1px;">Intelligence</li>
                        
                        <li class="nav-item">
                            <a href="{{ route('target.index') }}" class="nav-link {{ request()->routeIs('target.index', 'target.show') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>Target Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('target.search.page') }}" class="nav-link {{ request()->routeIs('target.search.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-person-bounding-box"></i>
                                <p>Discover Targets</p>
                            </a>
                        </li>
                        </li>
                        
                        <li class="nav-header text-uppercase text-secondary fw-bold mt-4 mb-2 ms-4" style="font-size: 0.75rem; letter-spacing: 1px;">Bot Operations</li>
                        
                        <li class="nav-item">
                            <a href="{{ route('comments.bank') }}" class="nav-link {{ request()->routeIs('comments.bank') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-chat-right-quote-fill"></i>
                                <p>Comment Bank</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('comments.accounts') }}" class="nav-link {{ request()->routeIs('comments.accounts') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-robot"></i>
                                <p>Bot Accounts</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('comments.history') }}" class="nav-link {{ request()->routeIs('comments.history') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-clock-history"></i>
                                <p>Comment History</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- /.Sidebar -->

        <!-- Main Content -->
        <main class="app-main">
            <div class="app-content-header fade-in-up">
                <div class="container-fluid px-4">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <h3 class="mb-1">@yield('page_title', 'Dashboard')</h3>
                            <p class="text-muted mb-0">@yield('page_subtitle', '')</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content fade-in-up" style="animation-delay: 0.1s;">
                <div class="container-fluid px-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center" role="alert" style="background: #ecfdf5; color: #065f46;">
                            <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </div>
        </main>
        <!-- /.Main Content -->

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE 4 JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>
    
    @yield('scripts')
</body>
</html>
