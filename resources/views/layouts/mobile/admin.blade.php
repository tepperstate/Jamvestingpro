<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADMIN MOBILE - {{ site()->name }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Core CSS -->
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/mycss.css') }}">

    <style>
        :root {
            --bottom-nav-height: 75px;
            --safe-area-bottom: env(safe-area-inset-bottom);
            --accent-primary: #990000;
            --accent-primary-soft: rgba(153, 0, 0, 0.1);
            --gold: #990000;
        }

        body.admin-mobile {
            background-color: transparent;
            margin-bottom: calc(var(--bottom-nav-height) + var(--safe-area-bottom));
            overflow-x: hidden;
            color: white;
        }

        .mobile-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 15px 20px;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: var(--bottom-nav-height);
            background: rgba(0, 0, 0, 0.98);
            backdrop-filter: blur(20px);
            border-top: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding-bottom: var(--safe-area-bottom);
            z-index: 2000;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 10px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .nav-item i {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .nav-item.active {
            color: var(--accent-primary);
        }

        .mobile-content {
            padding: 20px;
        }
    </style>
</head>
<body class="admin-mobile" style="background: transparent;">
    @include('marketing.partials.ambient')

    <header class="mobile-header">
        <div class="d-flex align-items-center gap-2">
            <h6 class="outfit font-weight-bold text-white mb-0">ADMIN CP</h6>
            <span class="badge badge-primary-glass x-small" style="font-size: 8px;">MOBILE v2.0</span>
        </div>
        <div class="actions">
            <a href="{{ route('admin.logout') }}" class="text-danger"><i class="ri-shut-down-line h4"></i></a>
        </div>
    </header>

    <main class="mobile-content" style="position: relative; z-index: 2;">
        @yield('content')
    </main>

    <nav class="bottom-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ri-dashboard-fill"></i>
            <span>MODS</span>
        </a>
        <a href="{{ route('admin.user') }}" class="nav-item {{ request()->routeIs('admin.user') ? 'active' : '' }}">
            <i class="ri-user-settings-fill"></i>
            <span>USERS</span>
        </a>
        <a href="{{ route('all_trade') }}" class="nav-item {{ request()->routeIs('all_trade') ? 'active' : '' }}">
            <i class="ri-history-fill"></i>
            <span>TRADES</span>
        </a>
        <a href="{{ route('admin.deposit') }}" class="nav-item {{ request()->routeIs('admin.deposit') ? 'active' : '' }}">
            <i class="ri-safe-2-fill"></i>
            <span>DEP/WID</span>
        </a>
        <a href="{{ route('site.index') }}" class="nav-item {{ request()->routeIs('site.index') ? 'active' : '' }}">
            <i class="ri-settings-4-fill"></i>
            <span>HUB</span>
        </a>
    </nav>

    <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
    
    <!-- Global Image Error Handler for Broken Asset Logos -->
    <script>
    document.addEventListener('error', function(e) {
        if (e.target.tagName && e.target.tagName.toLowerCase() === 'img') {
            if (!e.target.dataset.fallbackApplied) {
                e.target.dataset.fallbackApplied = "true";
                
                // Try to extract name from alt text
                let name = e.target.alt ? e.target.alt.replace(/[^a-zA-Z0-9]/g, '').substring(0, 2) : 'XX';
                if (name.trim() === '') name = 'XX';
                
                // Use a generic placeholder or UI Avatars
                e.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=2d3748&color=fff&rounded=true&bold=true&font-size=0.4';
            }
        }
    }, true);
    </script>
</body>
</html>
