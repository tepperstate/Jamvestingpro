<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}" />
    <title>@yield('title', 'Trading Platform')</title>
    <meta name="description" content="@yield('meta_description', 'High-tech crypto and stock investment platform offering secure mutual funds, retirement savings, and trading signals with daily ROI.')">
    <meta name="keywords" content="@yield('meta_keywords', 'crypto, investment, mutual funds, trading, stocks, roi, trading signals')">
    <meta property="og:title" content="@yield('title', 'Trading Platform')">
    <meta property="og:description" content="@yield('meta_description', 'High-tech crypto and stock investment platform offering secure mutual funds, retirement savings, and trading signals with daily ROI.')">
    <meta property="og:image" content="{{ asset('assets/img/favicon.svg') }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700;800&display=swap" rel="stylesheet">
    @notifyCss
    {!! ToastMagic::styles() !!}
    {!! laravelGeoGenius()->initIntlPhoneInput() !!}
    @livewireStyles
    <style>
        /* 
        @font-face {
            font-family: 'Circular Pro';
            src: url('{{ asset("fonts/gilroy-medium.woff2") }}') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Circular Pro';
            src: url('{{ asset("fonts/gilroy-bold.woff2") }}') format('woff2');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        */
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            color: #f8fafc;
            font-family: "Plus Jakarta Sans", "Inter", sans-serif !important;
            background-color: #000000;
        }
        .outfit { font-family: "Outfit", sans-serif !important; }
        .number-font { font-family: "JetBrains Mono", monospace !important; font-variant-numeric: tabular-nums; }
        
        /* Mobile Content Cut-off Fixes */
        @media (max-width: 768px) {
            .table-responsive {
                width: 100%;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            .table {
                min-width: max-content; /* Let table dictate width, container scrolls */
            }
            .card {
                max-width: 100vw;
                overflow: hidden;
            }
            .card-body {
                padding: 15px !important;
                overflow-x: auto;
            }
            /* Prevent long unbroken strings from breaking layout */
            td, th {
                white-space: normal;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }
            /* Flex layout fixes to prevent blowout */
            .d-flex > * {
                min-width: 0;
            }
            /* Premium Mobile Header Redesign */
            .glass-header {
                left: 0 !important;
                right: 0 !important;
                top: 0 !important;
                margin: 0 !important;
                border-radius: 0 0 28px 28px !important;
                border: none !important;
                border-bottom: 1px solid rgba(255,255,255,0.05) !important;
                background: rgba(8, 12, 18, 0.85) !important;
                backdrop-filter: blur(24px) !important;
                -webkit-backdrop-filter: blur(24px) !important;
                height: 72px !important;
                padding: 0 20px !important;
                box-shadow: 0 15px 35px rgba(0,0,0,0.4) !important;
            }
            .content-area {
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding-top: 90px !important; /* Space for the top-docked header */
            }
            .hamburger-toggle {
                border-radius: 50% !important;
                background: rgba(255,255,255,0.05) !important;
                border: 1px solid rgba(255,255,255,0.08) !important;
                box-shadow: inset 0 2px 10px rgba(255,255,255,0.02) !important;
            }
            .profile-avatar {
                border: 2px solid rgba(255, 51, 51, 0.5);
                box-shadow: 0 0 10px rgba(255, 51, 51, 0.2);
            }
        }
    </style>

    <!-- Core Icons & Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('new/vendor_components/bootstrap/dist/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/odometer.js/0.4.8/themes/odometer-theme-minimal.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/odometer.js/0.4.8/odometer.min.js"></script>

    <!-- Global Design System -->
    <link rel="preload" href="{{ asset('css/notification-system.css') }}?v=5{{ time() }}" as="style">
    <link rel="stylesheet" href="{{ asset('css/design-tokens.css') }}?v=1{{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/mycss.css') }}?v=4{{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/mobile-override.css') }}?v=4{{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/notification-system.css') }}?v=5{{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/notification-system-modals.css') }}?v=1{{ time() }}">

    <!-- Premium Mobile UI Assets (Only load on small screens for performance) -->
    <link rel="stylesheet" href="{{ asset('css/mobile-premium.css') }}?v=2{{ time() }}" media="screen and (max-width: 991px)">
    <script src="{{ asset('js/mobile-interactions.js') }}?v=2{{ time() }}" defer></script>

    <script>
        window.APP_URL = "{{ url('/') }}";
        document.documentElement.style.setProperty('--font-family-sans-serif', '"Circular Pro", "Inter", sans-serif');

        // Auto-redirect to mobile view if screen is small and not already forced
        if (window.innerWidth < 768 && !window.location.search.includes('mobile=1')) {
            const url = new URL(window.location.href);
            url.searchParams.set('mobile', '1');
            window.location.href = url.toString();
        }
    </script>

    <style>
        /* User-specific layout overrides to maintain compatibility while adopting the global Bento/Glass system */
        :root {
            --header-height: 80px;
            --sidebar-width: 280px;
            --sidebar-mini-width: 90px;
            --bg-deep: #000000; 
        }

        body.mini-sidebar {
            --sidebar-width: var(--sidebar-mini-width);
        }

        /* Sidebar Mini Transitions */
        .sidebar, .content-area, .glass-header {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        .upgrade-btn-premium {
            background: linear-gradient(135deg, #ff3333, #6366f1, #0284c7);
            background-size: 200% auto;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        }
        .upgrade-btn-premium:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 25px rgba(255, 51, 51, 0.5) !important;
            background-position: right center;
            filter: brightness(1.1);
        }

        .main-container {
            display: flex;
            padding-top: 0; /* Handled by margins */
            min-height: 100vh;
        }

        /* Shift content area to allow space for the floating island sidebar */
        .content-area {
            flex: 1;
            margin-left: calc(var(--sidebar-width) + 1rem);
            margin-right: 1rem;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }

        .hamburger-toggle {
            display: none;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
            z-index: 1100;
            transition: 0.3s;
        }
        .hamburger-toggle:active {
            background: rgba(255,255,255,0.1);
            transform: scale(0.95);
        }

        /* Sidebar Styling Harmonization */
        .sidebar {
            width: var(--sidebar-width) !important;
            margin: 1rem !important;
            height: calc(100vh - 2rem) !important;
            border-radius: var(--radius-2xl) !important;
            background: #000000 !important;
            backdrop-filter: blur(var(--glass-blur)) !important;
            border: 1px solid var(--glass-border) !important;
            position: fixed !important;
            left: 0;
            top: 0;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            z-index: 1030;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5) !important;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-scroller {
            flex: 1 !important;
            overflow-y: auto !important;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
        }
        .sidebar-scroller::-webkit-scrollbar {
            width: 3px;
        }
        .sidebar-scroller::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroller::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .sidebar-scroller::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-section {
            padding: 1.25rem 1.5rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            transition: color 0.2s;
        }

        .nav-section:hover {
            color: var(--text-secondary);
        }

        .nav-section i.section-icon {
            margin-right: 10px;
            font-size: 1.1rem;
            color: var(--accent-primary);
            opacity: 0.8;
        }

        .nav-section i.chevron {
            font-size: 0.8rem;
            transition: transform 0.3s ease;
            margin-left: auto;
        }

        .nav-section.collapsed i.chevron {
            transform: rotate(-90deg);
        }

        .nav-group {
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.3s ease;
            max-height: 1000px;
            opacity: 1;
        }

        .nav-group.collapsed {
            max-height: 0 !important;
            opacity: 0;
            pointer-events: none;
        }

        .nav-link {
            margin: 4px 16px !important;
            border-radius: var(--radius-lg) !important;
            padding: 12px 20px !important;
            color: var(--text-secondary) !important;
            border-left: none !important; 
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link i {
            font-size: 1.2rem;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .nav-link:hover i {
            opacity: 1;
        }

        .nav-link.active {
            background: linear-gradient(90deg, var(--accent-primary-soft) 0%, transparent 100%) !important;
            color: var(--accent-primary) !important;
            border-left: 3px solid var(--accent-primary) !important;
            font-weight: 600;
            box-shadow: inset 0 0 20px rgba(153, 0, 0, 0.03);
        }
        .nav-link.active i {
            opacity: 1;
            color: var(--accent-primary);
            filter: drop-shadow(0 0 4px rgba(153, 0, 0, 0.4));
        }

        .nav-lock-badge {
            margin-left: auto;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            padding: 2px 6px;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .nav-link:hover .nav-lock-badge {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1040;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        @media (max-width: 992px) {
            .hamburger-toggle { 
                display: flex !important;
                margin-right: 0 !important;
                flex-shrink: 0 !important;
            }
            .sidebar { 
                transform: translateX(calc(-100% - 2rem)); 
                width: 260px !important; /* Reduced size */
                margin: 0 !important; /* End to end height */
                height: 100vh !important;
                border-radius: 0 !important; /* Flush to edges */
                z-index: 1050 !important; /* Above overlay and header */
                background: #000000 !important; /* Solid to prevent overlap visibility */
            }
            .sidebar.active { transform: translateX(0); }
            
            .nav-link {
                padding: 10px 15px !important;
                font-size: 0.8rem !important; /* Smaller fonts */
                margin: 2px 10px !important;
            }
            .nav-section {
                padding: 0.75rem 1rem 0.25rem;
                font-size: 0.65rem;
            }

            .main-container {
                margin-top: var(--header-height) !important;
                min-height: calc(100vh - var(--header-height)) !important;
            }
            
            /* Reduced padding to remove margins and make it end-to-end */
            .content-area { 
                margin-left: 0 !important; 
                width: 100% !important; 
                padding: 0.5rem 0.5rem 12px 0.5rem !important; 
            }
            .glass-header { 
                left: 0 !important;
                top: 0 !important;
                margin: 0 !important; 
                width: 100% !important;
                border-radius: 0 !important;
                border-left: none !important;
                border-right: none !important;
                border-top: none !important;
                justify-content: space-between !important;
                padding: 0 0.75rem !important;
                gap: 8px !important;
                height: var(--header-height) !important;
            }
            .header-actions {
                flex: 1 !important;
                justify-content: flex-end !important;
                min-width: 0 !important;
                gap: 8px !important;
            }
            .user-balance-badge {
                padding: 3px 8px !important;
                gap: 6px !important;
                background: rgba(255, 51, 51, 0.08) !important;
                border-color: rgba(255, 51, 51, 0.15) !important;
            }
            .user-balance-badge span:first-child {
                display: none !important;
            }
            .user-balance-badge .bal {
                font-size: 0.9rem !important;
            }
            .profile-avatar {
                width: 32px !important;
                height: 32px !important;
            }
            .profile-dropdown {
                margin-left: 10px !important;
            }
        }

        .main-container {
            display: flex;
            margin-top: calc(var(--header-height) + 1rem);
            min-height: calc(100vh - var(--header-height) - 1rem);
            position: relative;
            z-index: 2;
        }

        /* Profile & Actions */
        .user-balance-badge {
            background: rgba(255, 51, 51, 0.05);
            padding: 8px 16px;
            border-radius: var(--radius-lg);
            border: 1px solid rgba(255, 51, 51, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .nav-link {
            color: var(--text-secondary) !important;
            padding: 0.8rem 1.2rem;
            border-radius: var(--radius-lg);
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid var(--accent-primary);
            object-fit: cover;
        }


        /* --- Custom UI Elements --- */
        .btn-premium {
            background: linear-gradient(135deg, var(--accent-primary), #B4952B);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .btn-premium:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 25px rgba(153, 0, 0, 0.4);
            color: white;
            filter: brightness(1.1);
        }
        
        .btn-premium:active {
            transform: scale(0.97);
        }

        .user-balance-badge {
            background: rgba(255, 255, 255, 0.03);
            padding: 5px 12px;
            border-radius: 10px;
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-balance-badge .bal {
            font-size: 1rem !important;
        }

        .demo-pill {
            background: #f59e0b;
            color: #000;
            font-size: 0.6rem;
            font-weight: 800;
            padding: 2px 5px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .profile-dropdown {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: 20px;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--accent-primary);
            object-fit: cover;
        }

        /* Global Atmospheric Elements */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(140px);
            z-index: 1;
            opacity: 0.15;
            animation: float 30s infinite ease-in-out alternate;
            pointer-events: none;
        }
        .bg-orb-1 { width: 50vw; height: 50vh; background: rgba(255, 255, 255, 0.03); top: -10%; left: -10%; }
        .bg-orb-2 { width: 60vw; height: 60vh; background: rgba(255, 255, 255, 0.02); bottom: -20%; right: -10%; animation-delay: -5s; }

        .bg-grid {
            /* Removed the aggressive grid lines in favor of a sleek, solid deep black/dark-gray background */
            position: fixed;
            inset: 0;
            background: #050505;
            z-index: 0;
            pointer-events: none;
        }

        @keyframes float {
            0% { transform: translate(0, 0); }
            100% { transform: translate(2%, 3%); }
        }

        .preloader {
            position: fixed;
            inset: 0;
            background: var(--bg-deep);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s ease;
        }
        .preloader::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('{{ asset("assets/img/preloader-bg.png") }}') no-repeat center center / cover;
            opacity: 0.5;
            z-index: 0;
        }

        .loader-pulse {
            position: relative;
            z-index: 1;
            width: 60px;
            height: 60px;
            border: 4px solid var(--glass-border);
            border-top-color: var(--accent-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* --- Pending Progress Loader --- */
        .pending-progress {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            min-width: 90px;
            gap: 3px;
        }
        .pending-progress .progress-track {
            width: 100%;
            height: 5px;
            background: rgba(255,255,255,0.08);
            border-radius: 3px;
            overflow: hidden;
            position: relative;
        }
        .pending-progress .progress-fill {
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(90deg, #f59e0b, #ef4444);
            animation: pendingFill 3s ease-out forwards, pendingPulse 4s ease-in-out 3s infinite;
            width: 0%;
        }
        .pending-progress .progress-label {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #f59e0b;
            text-transform: uppercase;
        }
        .pending-progress .progress-pct {
            font-size: 9px;
            font-weight: 700;
            color: rgba(255,255,255,0.5);
            font-family: 'Inter', monospace;
        }
        @keyframes pendingFill {
            0% { width: 0%; }
            30% { width: 55%; }
            60% { width: 78%; }
            80% { width: 88%; }
            100% { width: 93%; }
        }
        @keyframes pendingPulse {
            0% { width: 93%; }
            25% { width: 96%; }
            50% { width: 94%; }
            75% { width: 98%; }
            100% { width: 93%; }
        }

        /* --- Table Dark Override (Pure Black) --- */
        .table-dark, .table-dark th, .table-dark td {
            background-color: rgba(0, 0, 0, 0.3) !important;
            border-color: rgba(148, 163, 184, 0.04) !important;
        }
        .table-dark thead th {
            background-color: rgba(0, 0, 0, 0.15) !important;
            border-bottom-color: rgba(148, 163, 184, 0.06) !important;
        }
        .table-dark.table-hover tbody tr:hover {
            background-color: rgba(255, 51, 51, 0.02) !important;
        }

        /* --- Sidebar Promo Card --- */
        .sidebar-promo-card {
            background: linear-gradient(135deg, rgba(255, 51, 51, 0.06), rgba(99, 102, 241, 0.04));
            border: 1px solid rgba(255, 51, 51, 0.08);
            border-radius: 20px;
            padding: 20px;
            margin: 20px 16px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .sidebar-promo-card:hover {
            background: linear-gradient(135deg, rgba(255, 51, 51, 0.1), rgba(99, 102, 241, 0.06));
            border-color: rgba(255, 51, 51, 0.2);
            transform: translateY(-3px);
        }
        .sidebar-promo-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 12px;
            background: white; /* Contrast for the vector */
        }
        .sidebar-promo-tag {
            background: var(--accent-primary);
            color: #000;
            font-size: 0.6rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 8px;
        }
        .sidebar-promo-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }
        .sidebar-promo-desc {
            font-size: 0.7rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        /* Mini Sidebar Specifics */
        body.mini-sidebar .sidebar .nav-section span,
        body.mini-sidebar .sidebar .nav-section i.chevron,
        body.mini-sidebar .sidebar .nav-link .nav-text,
        body.mini-sidebar .sidebar .sidebar-promo-card,
        body.mini-sidebar .sidebar .sidebar-header span,
        body.mini-sidebar .sidebar .mt-auto {
            display: none !important;
        }

        body.mini-sidebar .sidebar .sidebar-header {
            justify-content: center !important;
            padding: 0 !important;
        }

        body.mini-sidebar .sidebar .sidebar-logo-container {
            display: none !important;
        }

        body.mini-sidebar .sidebar .sidebar-mini-toggle {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 12px;
            color: var(--accent-primary) !important;
            margin: 0 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.mini-sidebar .sidebar .sidebar-mini-toggle:hover {
            background: rgba(153, 0, 0, 0.1) !important;
            border-color: rgba(153, 0, 0, 0.3) !important;
            transform: scale(1.05);
        }

        body.mini-sidebar .sidebar .nav-link {
            justify-content: center;
            margin: 4px 10px !important;
            padding: 12px 0 !important;
        }

        body.mini-sidebar .sidebar .nav-text {
            display: none !important;
        }

        body.mini-sidebar .sidebar #menu-institutional a {
            justify-content: center !important;
            margin: 4px 10px !important;
            padding: 12px 0 !important;
        }

        body.mini-sidebar .sidebar #menu-institutional a i {
            margin-right: 0 !important;
            font-size: 1.4rem;
        }

        /* Dropdown Spacing & Spills Resolution */
        .dropdown-menu .dropdown-item {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 10px 16px !important;
            margin: 2px 0 !important;
            border-radius: var(--radius-md) !important;
            color: var(--text-secondary) !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
            border-left: none !important;
            background: transparent !important;
            box-shadow: none !important;
        }
        .dropdown-menu .dropdown-item:hover {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.05) !important;
        }
        .dropdown-menu .dropdown-item i {
            font-size: 1rem !important;
            opacity: 0.7 !important;
            width: 16px !important;
            text-align: center !important;
        }
        .dropdown-menu .dropdown-item:hover i {
            opacity: 1 !important;
        }
        .dropdown-menu .dropdown-item.text-danger:hover {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
        }

        body.mini-sidebar .sidebar .nav-link i {
            font-size: 1.4rem;
            margin: 0;
        }

        body.mini-sidebar .sidebar .nav-section {
            justify-content: center;
            padding: 1.5rem 0 0.5rem 0;
        }
        
        body.mini-sidebar .sidebar .nav-section i.section-icon {
            margin: 0;
            font-size: 1.3rem;
        }

        body.mini-sidebar .content-area {
            margin-left: calc(var(--sidebar-mini-width) + 1rem) !important;
            padding: 1.5rem 1.5rem;
        }

        /* Toggle Icon States */
        .toggle-icon-mini { display: none !important; }
        body.mini-sidebar .toggle-icon-full { display: none !important; }
        body.mini-sidebar .toggle-icon-mini { display: flex !important; }

        /* Header transition */
        body.mini-sidebar .glass-header {
            left: var(--sidebar-mini-width) !important;
        }

        /* --- Legacy Sub-Nav Dark Override --- */
        .legacy-subnav .navbar-light.bg-light,
        .legacy-subnav .navbar-light,
        .legacy-subnav .my_card {
            background: rgba(0, 0, 0, 0.4) !important;
            border: 1px solid var(--glass-border) !important;
            border-radius: 12px !important;
            backdrop-filter: blur(10px);
        }
        .legacy-subnav .btn-outline-primary {
            color: var(--text-secondary) !important;
            border-color: var(--glass-border) !important;
            background: transparent !important;
            border-radius: 8px !important;
            font-size: 11px !important;
            transition: 0.2s;
        }
        .legacy-subnav .btn-outline-primary:hover,
        .legacy-subnav .btn-outline-primary.active {
            background: rgba(59, 130, 246, 0.15) !important;
            border-color: var(--accent-primary) !important;
            color: var(--accent-primary) !important;
        }

        /* --- Global Form Inputs (Modals handled by notification-system-modals.css) --- */

        .form-control, .form-select, .mobile-input {
            background-color: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
            border-radius: 12px !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            transition: all 0.4s cubic-bezier(0.32, 0.72, 0, 1) !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.06) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.05) !important;
            outline: none !important;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3) !important;
        }
        select option {
            background-color: #000000 !important; /* Forces dark background sitewide */
            color: #ffffff !important;
        }
        
        /* Eliminate any lingering white cards/backgrounds */
        .bg-white, .card.bg-white {
            background-color: rgba(255, 255, 255, 0.02) !important;
            color: #ffffff !important;
        }
        
        .text-dark { color: #f8fafc !important; }
        .card { background-color: transparent !important; border: none !important; }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-deep" style="background: var(--bg-deep);">

    <!-- Global Background Atmosphere -->
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-grid"></div>

    @include('marketing.partials.ambient')
    <script>
        if (localStorage.getItem('sidebar_mini') === 'true' && window.innerWidth > 992) {
            document.body.classList.add('mini-sidebar');
        }
    </script>

    <div class="preloader" id="app-preloader">
        <div class="loader-pulse"></div>
    </div>

    <!-- Glass Header -->
    <header class="glass-header" style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; height: var(--header-height); margin: 1rem; border-radius: var(--radius-xl); background: var(--bg-deep); backdrop-filter: blur(var(--glass-blur)); border: 1px solid var(--glass-border); display: flex; align-items: center; justify-content: space-between; padding: 0 1rem; z-index: 1000; transition: all 0.3s ease;">
        <div class="d-lg-none" style="flex-shrink: 0;">
            <button class="hamburger-toggle d-flex" onclick="toggleSidebar()">
                <i class="ri-menu-4-line"></i>
            </button>
        </div>

        <!-- Mobile Logo -->
        <div class="d-lg-none flex-grow-1 d-flex justify-content-start align-items-center px-2" style="min-width: 0;">
            <a href="{{ route('dashboard.index') }}" style="display: inline-flex; max-width: 100%; width: 100%; align-items: center;">
                <x-ui.logo variant="light" size="lg" class="mobile-logo" />
            </a>
        </div>

        <div class="header-actions" style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; width: 100%;">
            
            <!-- LEFT PART: Quick Actions -->
            <div class="d-flex align-items-center gap-2">
            
            <!-- Quick Actions -->
            <div class="d-none d-xl-flex align-items-center gap-2 me-2">
                <a href="{{ route('deposits') }}" class="btn btn-sm d-flex align-items-center gap-2 satin-border" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.15); border-radius: 10px; color: #10b981; padding: 6px 14px; font-size: 0.75rem; font-weight: 700; transition: 0.3s; text-decoration: none;">
                    <i class="ri-add-circle-line" style="font-size: 14px;"></i>
                    <span>Deposit</span>
                </a>
                <a href="{{ route('withdraw') }}" class="btn btn-sm d-flex align-items-center gap-2 satin-border" style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.15); border-radius: 10px; color: #ef4444; padding: 6px 14px; font-size: 0.75rem; font-weight: 700; transition: 0.3s; text-decoration: none;">
                    <i class="ri-arrow-up-circle-line" style="font-size: 14px;"></i>
                    <span>Withdraw</span>
                </a>
                <a href="{{ route('user.swap') }}" class="btn btn-sm d-flex align-items-center gap-2 satin-border" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.15); border-radius: 10px; color: #0ea5e9; padding: 6px 14px; font-size: 0.75rem; font-weight: 700; transition: 0.3s; text-decoration: none;">
                    <i class="ri-repeat-2-line" style="font-size: 14px;"></i>
                    <span>Swap</span>
                </a>
            </div>

            <!-- Portfolio Button -->
            <a href="{{ route('dashboard.portfolio') }}" class="btn btn-sm d-none d-md-flex align-items-center gap-2 satin-border me-1" style="background: rgba(255,255,255,0.03); border-radius: 10px; color: #fff; padding: 6px 14px; font-size: 0.75rem; font-weight: 700; transition: 0.3s; text-decoration: none;">
                <i class="ri-pie-chart-2-line text-primary" style="font-size: 14px;"></i>
                <span class="d-none d-lg-inline">Portfolio</span>
            </a>
            </div>

            <!-- RIGHT PART: Locale, Balance, Profile -->
            <div class="d-flex align-items-center gap-3">
            
                <!-- Locale Switcher -->
                <div class="dropdown d-inline-block" style="display: inline-block !important; margin-right: 8px;">
                    <button class="btn btn-sm d-flex align-items-center satin-border" data-toggle="dropdown" style="background: rgba(255,255,255,0.03); border-radius: 8px; color: #cbd5e1; padding: 4px 8px; transition: 0.3s; display: flex !important;">
                        @php 
                        $localeFlags = [
                            'en'=>'<img src="https://flagcdn.com/w20/us.png" width="16" alt="US">',
                            'es'=>'<img src="https://flagcdn.com/w20/es.png" width="16" alt="ES">',
                            'fr'=>'<img src="https://flagcdn.com/w20/fr.png" width="16" alt="FR">',
                            'zh'=>'<img src="https://flagcdn.com/w20/cn.png" width="16" alt="CN">',
                            'ar'=>'<img src="https://flagcdn.com/w20/sa.png" width="16" alt="SA">'
                        ]; 
                        @endphp
                        <span style="display: flex; align-items: center; justify-content: center; width: 16px; height: 16px;">{!! $localeFlags[app()->getLocale()] ?? '<i class="ri-global-line"></i>' !!}</span>
                        <i class="ri-arrow-down-s-line text-muted d-none d-sm-inline" style="font-size:12px; margin-left: 4px;"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" style="background: rgba(15,20,30,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; min-width: 140px; padding: 6px;">
                        <a class="dropdown-item" href="{{ url('/locale/en') }}" style="color:var(--text-secondary); border-radius:8px; padding:8px 12px; font-size:0.85rem;"><img src="https://flagcdn.com/w20/us.png" width="16" alt="US" class="mr-2"> English</a>
                        <a class="dropdown-item" href="{{ url('/locale/es') }}" style="color:var(--text-secondary); border-radius:8px; padding:8px 12px; font-size:0.85rem;"><img src="https://flagcdn.com/w20/es.png" width="16" alt="ES" class="mr-2"> Español</a>
                        <a class="dropdown-item" href="{{ url('/locale/fr') }}" style="color:var(--text-secondary); border-radius:8px; padding:8px 12px; font-size:0.85rem;"><img src="https://flagcdn.com/w20/fr.png" width="16" alt="FR" class="mr-2"> Français</a>
                        <a class="dropdown-item" href="{{ url('/locale/zh') }}" style="color:var(--text-secondary); border-radius:8px; padding:8px 12px; font-size:0.85rem;"><img src="https://flagcdn.com/w20/cn.png" width="16" alt="CN" class="mr-2"> 中文</a>
                        <a class="dropdown-item" href="{{ url('/locale/ar') }}" style="color:var(--text-secondary); border-radius:8px; padding:8px 12px; font-size:0.85rem;"><img src="https://flagcdn.com/w20/sa.png" width="16" alt="SA" class="mr-2"> العربية</a>
                    </div>
                </div>

                <!-- Balance Display -->
                @if(!Route::is('hip.pro.dashboard'))
                <div class="user-balance-badge d-none d-md-flex align-items-center gap-3" style="background: rgba(255, 51, 51, 0.04); border-color: rgba(255, 51, 51, 0.15);">
                    <div style="display: flex; flex-direction: column; align-items: flex-start;">
                        <span class="d-none d-sm-inline" style="font-size: 0.65rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px;">{{ auth()->user()?->is_demo ? 'Demo Portfolio' : 'Live Portfolio' }}</span>
                        <span class="outfit font-weight-bold bal" style="font-size: 1.1rem; color: {{ auth()->user()?->is_demo ? 'var(--accent-warning)' : '#10b981' }};">${{ number_format(auth()->user()?->is_demo ? (auth()->user()?->balance->demo ?? 0) : (auth()->user()?->balance->amount ?? 0), 2) }}</span>
                    </div>
                    
                    <!-- Deposit Button Mini -->
                    <a href="{{route('deposit')}}" class="btn btn-sm text-white" style="background: #10b981; border-radius: 6px; padding: 4px 10px; font-size: 0.75rem;">
                        <i class="ri-add-line"></i> <span class="d-none d-sm-inline">Deposit</span>
                    </a>
                </div>
                @endif
                    <!-- Demo Mode Toggle -->
                    <div class="d-flex align-items-center ml-1 border-left pl-2" style="border-color: rgba(255,255,255,0.1) !important;">
                        <a href="{{ route('toggleDemo') }}" class="demo-switch-btn d-flex align-items-center gap-1" style="text-decoration: none;">
                            <div class="custom-control custom-switch" style="pointer-events: none; padding-left: 1.5rem; margin-right: 0;">
                                <input type="checkbox" class="custom-control-input" {{ auth()->user()?->is_demo ? '' : 'checked' }}>
                                <label class="custom-control-label" style="margin-bottom: 0;"></label>
                            </div>
                            <span class="x-small font-weight-bold {{ auth()->user()?->is_demo ? 'text-warning' : 'text-success' }}" style="font-size: 10px; line-height: 1;">
                                {{ auth()->user()?->is_demo ? 'DEMO' : 'LIVE' }}
                            </span>
                        </a>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="profile-dropdown dropdown" role="button" tabindex="0">
                        <div data-toggle="dropdown" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <div style="text-align: right; line-height: 1.1;" class="d-none d-md-block me-2">
                                <div class="outfit font-weight-bold text-white" style="font-size: 0.9rem;">{{ auth()->user()?->first_name }} {{ auth()->user()?->last_name }}</div>
                                <div style="font-size: 0.65rem; color: var(--text-secondary); opacity: 0.7; margin-bottom: 4px;">{{ auth()->user()?->email }}</div>
                                <div>
                                    <span class="badge" style="font-size: 0.6rem; padding: 3px 6px; background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                                        {{ auth()->user()->package->name ?? (auth()->user()->package_plan ?? 'Standard Plan') }}
                                    </span>
                                </div>
                            </div>
                            <img src="{{ auth()->user()->image ? asset('storage/image/'.auth()->user()->image) : 'https://ui-avatars.com/api/?name='.auth()->user()?->first_name }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->first_name ?? 'User') }}&background=10b981&color=fff';" class="profile-avatar" alt="Avatar" style="transform: scale(1.1); transform-origin: center; border: 2px solid #10b981 !important;">
                        </div>
                        <div class="dropdown-menu dropdown-menu-right" style="min-width: 240px; margin-top: 15px; padding: 12px; background: rgba(15, 20, 30, 0.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.5);">
                            <a class="dropdown-item" href="{{ route('profile') }}"><i class="fa fa-user"></i> My Profile</a>
                            <a class="dropdown-item" href="{{ route('user.api_keys') }}"><i class="fa fa-key"></i> API Keys</a>
                            <a class="dropdown-item" href="{{ route('dashboard.wallets') }}"><i class="fa fa-wallet"></i> Wallet Hub</a>
                            <a class="dropdown-item" href="{{ route('mail.inbox') }}"><i class="fa fa-envelope"></i> Messages</a>
                            <div class="dropdown-divider" style="background: rgba(255,255,255,0.08);"></div>
                            <a href="{{ route('user.upgrade') }}" class="dropdown-item upgrade-btn-premium" style="color: white !important; border-radius: 12px; text-align: center; font-weight: 800; margin: 8px 0; padding: 12px 16px !important; box-shadow: 0 4px 15px rgba(255, 51, 51, 0.3); border: none; display: block; text-transform: uppercase; letter-spacing: 0.5px; font-size: 13px;">
                                <i class="ri-vip-crown-2-fill" style="margin-right: 6px;"></i> Upgrade Account
                            </a>
                            <div class="dropdown-divider" style="background: rgba(255,255,255,0.08);"></div>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="fa fa-sign-out"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
</header>


    <div class="main-container">
        @include('layouts.user.partials.sidebar')

        <!-- Main Content Area -->
        <main class="content-area">
            @yield('content')
        </main>
    </div>

    {{-- @include('mobile.components.bottom-nav') --}}

    <!-- Scripts -->
    <script src="{{ asset('new/vendor_components/jquery-3.3.1/jquery-3.3.1.js') }}"></script>
    <script src="{{ asset('new/vendor_components/popper/dist/popper.min.js') }}"></script>
    <script src="{{ asset('new/vendor_components/bootstrap/dist/js/bootstrap.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('js/notification-system.js') }}?v=4{{ time() }}"></script>
    <script src="{{ asset('js/notification-system-modals.js') }}?v=1{{ time() }}"></script>
    <script>
        $(document).ready(function() {
            function pollNotifications() {
                $.ajax({
                    url: '{{ route("notifications.poll") }}',
                    method: 'GET',
                    success: function(data) {
                        if (data && data.length > 0) {
                            let idsToMark = [];
                            data.forEach(function(noti) {
                                let type = noti.type || 'info';
                                let msg = (noti.message || '').toLowerCase();
                                let title = (noti.title || '').toLowerCase();
                                
                                if (msg.includes('success') || title.includes('success')) type = 'success';
                                else if (msg.includes('fail') || title.includes('error')) type = 'error';
                                else if (msg.includes('reward') || title.includes('reward') || msg.includes('bonus')) type = 'achievement';
                                
                                JVNotify.toast(type, noti.message, noti.title || 'Notification');
                                idsToMark.push(noti.id);
                            });

                            if (idsToMark.length > 0) {
                                $.ajax({
                                    url: '{{ route("notifications.mark_popped") }}',
                                    method: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        ids: idsToMark
                                    }
                                });
                            }
                        }
                    }
                });
            }

            // Poll every 10 seconds
            setInterval(pollNotifications, 10000);
            // Initial poll
            setTimeout(pollNotifications, 1000);
        });
    </script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.alert = function(message) {
            Swal.fire({
                text: message,
                icon: 'info',
                background: 'var(--glass-bg, #10121b)',
                color: '#fff',
                confirmButtonColor: 'var(--accent-primary, #ff3333)',
                backdrop: 'rgba(0,0,0,0.8)'
            });
        };

        document.addEventListener('click', function(e) {
            let el = e.target.closest('[onclick*="confirm("]');
            if (el && !el.dataset.swalConfirmed) {
                e.preventDefault();
                e.stopImmediatePropagation();
                let match = el.getAttribute('onclick').match(/confirm\(['"](.*?)['"]\)/);
                let msg = match ? match[1] : 'Are you sure?';

                Swal.fire({
                    title: 'Action Required',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    background: 'var(--glass-bg, #10121b)',
                    color: '#fff',
                    confirmButtonColor: 'var(--accent-primary, #ff3333)',
                    cancelButtonColor: '#ef4444',
                    backdrop: 'rgba(0,0,0,0.8)',
                    confirmButtonText: 'Yes, proceed'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let originalOnclick = el.getAttribute('onclick');
                        el.removeAttribute('onclick');
                        if (el.tagName === 'A' && el.href && el.href !== 'javascript:void(0)' && el.href !== '#') {
                            window.location.href = el.href;
                        } else if (el.tagName === 'BUTTON' && el.form) {
                            el.form.submit();
                        } else {
                            el.dataset.swalConfirmed = true;
                            el.click();
                        }
                        setTimeout(() => el.setAttribute('onclick', originalOnclick), 100);
                    }
                });
                return false;
            }
        }, true);
    </script>

    <script>
        $(document).ready(function() {
            $('#app-preloader').fadeOut(300);
        });
        
        // Failsafe fallback
        setTimeout(function() {
            $('#app-preloader').fadeOut(300);
        }, 1000);

        // Global Session Feedback (Toastr)
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        @if(session('status'))
            toastr.info("{{ session('status') }}");
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif

        // Fallback: Force hide preloader after 3 seconds in case window.onload is delayed
        setTimeout(function() {
            if ($('#app-preloader').is(':visible')) {
                console.warn("Preloader fallback triggered.");
                $('#app-preloader').fadeOut(500);
            }
        }, 3000);

        // Global Balance Updater
        function updateGlobalBalance() {
            fetch("{{ route('user.balance') }}")
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    const balance = {{ auth()->user()?->is_demo == 1 ? 'data.demo' : 'data.data' }};
                    $('.bal').text('$' + balance);
                })
                .catch(err => {
                    console.error("Balance update failed:", err);
                });
        }

        $(document).ready(function() {
            updateGlobalBalance();
            setInterval(updateGlobalBalance, 10000);
        });

        // Toggle Sidebar on mobile
        function toggleSidebar() {
            $('#sidebar').toggleClass('active');
            $('#sidebar-overlay').toggleClass('active');
            $('body').toggleClass('sidebar-open');
        }

        function toggleSidebarMini() {
            $('body').toggleClass('mini-sidebar');
            const isMini = $('body').hasClass('mini-sidebar');
            localStorage.setItem('sidebar_mini', isMini);
        }

        // Sidebar Collapsible Logic
        $(document).on('click', '.nav-section', function() {
            const section = $(this);
            const targetId = section.data('target');
            const group = $('#' + targetId);
            
            section.toggleClass('collapsed');
            group.toggleClass('collapsed');

            // Save state to localStorage for persistence
            const states = JSON.parse(localStorage.getItem('sidebar_states') || '{}');
            states[targetId] = group.hasClass('collapsed');
            localStorage.setItem('sidebar_states', JSON.stringify(states));
        });

        // Initialize Sidebar States
        $(document).ready(function() {
            const states = JSON.parse(localStorage.getItem('sidebar_states') || '{}');

            $('.nav-group').each(function() {
                const group = $(this);
                const id = group.attr('id');
                
                if (group.find('.nav-link.active').length > 0) {
                    group.removeClass('collapsed');
                    $('[data-target="' + id + '"]').removeClass('collapsed');
                } else if (states[id] === true) {
                    group.addClass('collapsed');
                    $('[data-target="' + id + '"]').addClass('collapsed');
                }
            });

            // Initialize Lucide Icons globally
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Initialize AOS
        $(document).ready(function() {
            AOS.init({
                duration: 800,
                once: true,
                easing: 'ease-out-quad'
            });
        });
    </script>
    @if(session('upgrade_success'))
    <!-- Upgrade Success via JVModal -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                JVModal.create({
                    id: 'upgradeSuccessModal',
                    title: 'TIER ACTIVATED',
                    titleGradient: true,
                    icon: '<i class="ri-vip-crown-2-fill text-primary" style="font-size: 5rem; text-shadow: 0 0 30px rgba(59, 130, 246, 0.5);"></i>',
                    description: "{{ session('status') }}",
                    features: ['High Leverage', 'VIP Stocks', 'Institutional Liquidity'],
                    ctaText: 'COMMENCE TRADING',
                    ctaClass: 'jv-modal-cta jv-modal-cta-blue',
                    autoShow: true,
                    celebrate: true
                });
            }, 500);
        });
    </script>
    @endif
    <!-- Motion One -->
    <script src="https://cdn.jsdelivr.net/npm/motion@11.11.13/dist/motion.js"></script>
    <script src="{{ asset('js/drip-accrual.js') }}" defer></script>
    
    @stack('js')
    @if(auth()->check() && !\App\Models\UserOnboardingResponse::where('user_id', auth()->id())->exists())
    <!-- Global Questionnaire Prompt via JVModal -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (!sessionStorage.getItem('questionnaire_prompted_v3')) {
                setTimeout(function() {
                    JVModal.create({
                        id: 'questionnairePromptModal',
                        title: 'Investor Profile Needed',
                        icon: '<i class="ri-survey-line text-primary mb-0" style="font-size: 2.5rem;"></i>',
                        iconColor: '#6366f1',
                        description: 'Please complete your Investor Profile Questionnaire. This helps us personalize your trading experience, unlock specific assets, and verify your risk tolerance.',
                        ctaText: 'COMPLETE QUESTIONNAIRE',
                        ctaUrl: "{{ route('question') }}",
                        ghostText: "I'll do this later",
                        autoShow: true,
                        static: true
                    });
                    sessionStorage.setItem('questionnaire_prompted_v3', 'true');
                }, 2500);
            }
        });
    </script>
    @else
    <!-- Manual trigger modal for users who already completed it -->
    @if(auth()->check())
    <script>
        // Provide a global function to show it if they click a button
        window.showRetakeQuestionnaireModal = function() {
            JVModal.create({
                id: 'questionnaireRetakeModal',
                title: 'Profile Configured',
                icon: '<i class="ri-checkbox-circle-fill text-success mb-0" style="font-size: 2.5rem;"></i>',
                iconColor: '#10b981',
                description: 'You have already completed your Investor Profile. If your financial situation or risk tolerance has changed, you can retake the questionnaire.',
                ctaText: 'RETAKE QUESTIONNAIRE',
                ctaUrl: "{{ route('question') }}",
                ctaClass: 'jv-modal-cta jv-modal-cta-gold',
                ghostText: 'Cancel',
                autoShow: true
            });
        };
    </script>
    @endif
    @endif

    @if(auth()->check() && auth()->user()->upgrade_code_check == 'on')
    <!-- Account Upgrade Notice via JVModal -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                JVModal.create({
                    id: 'accountUpgradeRequiredModal',
                    title: 'PREMIUM UPGRADE',
                    titleGradient: true,
                    icon: '<svg class="jv-star-rotate jv-star-glow" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="#D4AF37" stroke="none"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"></path></svg><div class="jv-sparkle jv-sparkle-1"></div><div class="jv-sparkle jv-sparkle-2"></div><div class="jv-sparkle jv-sparkle-3"></div><div class="jv-sparkle jv-sparkle-4"></div>',
                    description: 'Your account has been flagged for a premium upgrade. Elevate your trading experience with advanced features.',
                    features: [
                        'Higher trading limits',
                        'Advanced analytics',
                        'Priority support',
                        'Exclusive market signals'
                    ],
                    ctaText: 'View Upgrade Plans →',
                    ctaUrl: "{{ route('user.upgrade') }}",
                    ctaClass: 'jv-modal-cta jv-modal-cta-gold',
                    ghostText: 'Maybe later',
                    autoShow: true,
                    static: true
                });
            }, 800);
        });
    </script>
    @endif
    <script>
    // Magnetic Button Effect for Premium Buttons
    document.addEventListener("mousemove", (e) => {
        document.querySelectorAll('.btn-premium, .btn-action').forEach(btn => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            // Only apply if mouse is close
            const dist = Math.sqrt(x*x + y*y);
            if (dist < 60) {
                btn.style.transform = `translate(${x * 0.15}px, ${y * 0.15}px) scale(1.05)`;
            } else {
                btn.style.transform = 'translate(0px, 0px)';
            }
        });
    });

    // Initialize Odometers on .number-font
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll('.number-font').forEach(el => {
            // Check if it looks like a price/balance (starts with $)
            let text = el.innerText.trim();
            if (text.startsWith('$')) {
                let val = text.replace(/[^0-9.-]+/g,"");
                el.innerHTML = '$<span class="odometer">' + val + '</span>';
                let od = new Odometer({
                    el: el.querySelector('.odometer'),
                    value: val,
                    format: '(,ddd).dd',
                    theme: 'minimal'
                });
                // Assign to instance for later updates
                el.odometerInstance = od;
            }
        });
    });
    </script>
    <script src="{{ asset('js/live-market.js') }}" defer></script>
    
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
    
    <!-- Async Modals System (SweetAlert2 loaded above) (Minified) -->
    <script src="{{ asset('js/async-modals.js') }}?v={{ time() }}"></script>
    
    <!-- Service Worker Initialization for PWA Offline-First Capability -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
    @stack('modals')

    <!-- Google Translate Auto-Sync with Laravel Locale -->
    <div id="google_translate_element" style="display:none;"></div>
    @php
        $currentLocale = app()->getLocale();
        $gMap = ['en' => 'en', 'es' => 'es', 'fr' => 'fr', 'zh' => 'zh-CN', 'ar' => 'ar'];
        $gLang = $gMap[$currentLocale] ?? 'en';
    @endphp
    <script type="text/javascript">
        function googleTranslateElementInit() {
          new google.translate.TranslateElement({pageLanguage: 'en', autoDisplay: false}, 'google_translate_element');
        }
        
        function setGoogleTranslate(lang) {
            let domain = window.location.hostname;
            // Clear old cookies safely
            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; domain=." + domain + "; path=/;";
            
            if (lang !== 'en') {
                let gLang = lang;
                if(lang === 'zh') gLang = 'zh-CN';
                document.cookie = "googtrans=/en/" + gLang + "; path=/";
                if (domain !== 'localhost' && domain !== '127.0.0.1') {
                    document.cookie = "googtrans=/en/" + gLang + "; domain=." + domain + "; path=/";
                }
            }
        }

        // Auto-Sync Google Translate with Laravel Locale
        document.addEventListener("DOMContentLoaded", function() {
            let targetLang = "{{ $gLang }}";
            let currentCookieMatch = document.cookie.match(/(^|;) ?googtrans=([^;]*)(;|$)/);
            let currentCookieVal = currentCookieMatch ? currentCookieMatch[2] : null;
            
            let expectedCookieVal = (targetLang === 'en') ? null : ("/en/" + targetLang);
            
            // If the cookie does not match the Laravel locale, update it
            if (currentCookieVal !== expectedCookieVal) {
                setGoogleTranslate(targetLang === 'zh-CN' ? 'zh' : targetLang);
                
                // If the user hasn't explicitly triggered a translation via the google widget yet, 
                // we reload to apply the new cookie immediately, but only if we actually changed the translation state.
                // To avoid loops, we use sessionStorage to track forced reloads.
                if (!sessionStorage.getItem('gt_reloaded')) {
                    sessionStorage.setItem('gt_reloaded', 'true');
                    window.location.reload();
                } else {
                    sessionStorage.removeItem('gt_reloaded');
                }
            } else {
                sessionStorage.removeItem('gt_reloaded');
            }
        });
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <style>
        /* Hide Google Translate UI to maintain premium look */
        .skiptranslate iframe, .goog-te-banner-frame { display: none !important; }
        body { top: 0px !important; }
        #goog-gt-tt { display: none !important; }
        .goog-text-highlight { background-color: transparent !important; box-shadow: none !important; }
    </style>

    <!-- Sitewide Mobile Navigation (Hidden on Desktop) -->
    @include('mobile.components.bottom-nav')
</body>
</html>





