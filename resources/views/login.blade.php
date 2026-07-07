<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta data-n-head="ssr" data-hid="og:image" property="og:image" content="{{asset('assets/images/logo.svg')}}">
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}">
    <title>Secure Access | {{site()->name}}</title>
    <meta name="description" content="Securely log in to {{site()->name}} to access institutional-grade trading tools, mutual funds, and daily ROI investments.">
    <meta name="keywords" content="login, secure access, trading platform, crypto, mutual funds, {{site()->name}}">
    <meta property="og:title" content="Secure Access | {{site()->name}}">
    <meta property="og:description" content="Securely log in to {{site()->name}} to access institutional-grade trading tools, mutual funds, and daily ROI investments.">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" />
    <script src="{{asset('new/vendor_components/jquery-3.3.1/jquery-3.3.1.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #050505;
            min-height: 100vh;
            overflow-x: hidden;
            color: #e2e8f0;
        }



        /* === Grid Overlay === */
        .grid-overlay {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        /* === Main Layout === */
        .auth-wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            min-height: 100vh;
        }

        .auth-form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .auth-visual-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .auth-visual-side::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("{{asset('assets/img/admin_login_bg.png')}}") center/cover no-repeat;
            opacity: 0.5;
            filter: grayscale(0.2) contrast(1.1);
        }

        .auth-visual-side::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(5, 5, 5, 0.8), rgba(5, 5, 5, 0.4));
        }

        .visual-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 60px;
        }

        .visual-content h2 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #990000, #ffcccc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 16px;
        }

        .visual-content p {
            color: rgba(255,255,255,0.85);
            font-size: 1.1rem;
            line-height: 1.7;
            max-width: 400px;
            margin: 0 auto;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        /* === Glass Card Double-Bezel === */
        .auth-card-shell {
            width: 100%;
            max-width: 460px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 36px;
            padding: 10px;
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.4);
            animation: cardSlideUp 0.8s cubic-bezier(0.32, 0.72, 0, 1);
        }

        .auth-card {
            width: 100%;
            background: #050505;
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: calc(36px - 10px);
            padding: 48px 40px;
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.15);
        }

        @keyframes cardSlideUp {
            from { opacity: 0; transform: translateY(60px); filter: blur(10px); }
            to { opacity: 1; transform: translateY(0); filter: blur(0); }
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 36px;
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .logo-bg-premium {
            display: inline-flex;
            transition: transform 0.3s ease;
            height: 60px;
            align-items: center;
        }

        @media (max-width: 768px) {
            .logo-bg-premium {
                display: inline-flex;
                transition: transform 0.3s ease;
                height: 60px;
                align-items: center;
            }
            .auth-card-shell { border-radius: 24px; padding: 6px; }
            .auth-card { padding: 32px 20px 48px; border-radius: calc(24px - 6px); }
        }

        .auth-card h1 {
            font-size: 1.6rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
            color: #f1f5f9;
        }

        .auth-card .subtitle {
            text-align: center;
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
            margin-bottom: 32px;
        }

        /* === Form Controls === */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group .input-wrapper {
            position: relative;
        }

        .form-group .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.3);
            font-size: 1rem;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 14px 16px 14px 46px;
            color: #ffffff !important;
            font-size: 0.95rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.4s cubic-bezier(0.32, 0.72, 0, 1);
            outline: none;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(153, 0, 0, 0.5);
            box-shadow: 0 0 0 4px rgba(153, 0, 0, 0.1);
        }

        .form-control:focus + i,
        .form-control:focus ~ i {
            color: #990000;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.25);
        }

        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus,
        .form-control:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #050505 inset !important;
            -webkit-text-fill-color: #ffffff !important;
            transition: background-color 5000s ease-in-out 0s;
            border-color: rgba(255,255,255,0.08);
        }

        /* === Options Row === */
        .auth-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.15);
            border-radius: 5px;
            background: rgba(255,255,255,0.04);
            cursor: pointer;
            transition: all 0.2s;
        }

        .remember-me input[type="checkbox"]:checked {
            background: #990000;
            border-color: #990000;
        }

        .remember-me span {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
        }

        .forgot-link {
            font-size: 0.85rem;
            color: #990000;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #ffcccc;
        }

        /* === Submit Button === */
        .btn-auth-primary {
            width: 100%;
            padding: 8px 8px 8px 24px;
            background: #ffffff;
            color: #050505;
            border: none;
            border-radius: 9999px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.7s cubic-bezier(0.32, 0.72, 0, 1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .btn-auth-primary:active {
            transform: scale(0.98);
        }

        .btn-auth-primary .icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.7s cubic-bezier(0.32, 0.72, 0, 1);
        }

        .btn-auth-primary:hover .icon-wrapper {
            transform: translate(2px, -1px) scale(1.05);
        }

        /* === Divider === */
        .auth-divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            gap: 16px;
        }

        .auth-divider::before, .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.08);
        }

        .auth-divider span {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* === Google Button === */
        .btn-google {
            width: 100%;
            padding: 14px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            color: #e2e8f0;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .btn-google:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            transform: scale(0.98);
        }

        .btn-google svg { width: 20px; height: 20px; }

        /* === Footer Links === */
        .auth-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.5);
        }

        .auth-footer a {
            color: #990000;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .auth-footer a:hover { color: #ffcccc; }

        .terms-link {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.3);
            text-decoration: none;
            transition: color 0.2s;
        }
        .terms-link:hover { color: rgba(255,255,255,0.6); }

        /* === Preloader === */
        .preloader-wrapper {
            position: fixed; inset: 0; z-index: 9999;
            background: #060b18;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: 24px;
            transition: opacity 0.5s ease;
        }
        .preloader-wrapper .logo-bg-premium {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: logoPulse 2s ease-in-out infinite;
        }
        .preloader-spinner {
            width: 40px; height: 40px;
            border: 3px solid rgba(255,255,255,0.1);
            border-top-color: #990000;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes logoPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* === Mobile Responsive === */
        @media (max-width: 768px) {
            .auth-visual-side { display: none !important; }
            .auth-form-side { padding: 20px 16px; align-items: flex-start; width: 100%; }
            .auth-card-shell {
                margin-top: 20px;
            }
            .auth-card h1 { font-size: 1.4rem; }
            .brand-logo img { width: 160px; }
        }
    </style>
</head>
<body>
    @include('marketing.partials.ambient')
    <!-- Preloader -->
    <div class="preloader-wrapper" id="preloader">
        <div class="logo-bg-premium">
            <x-ui.logo variant="light" size="lg" />
        </div>
        <div class="preloader-spinner"></div>
    </div>

    <!-- Main Auth Layout -->
    <div class="auth-wrapper" id="auth-wrapper" style="display:none;">
        <!-- Form Side -->
        <div class="auth-form-side">
            <div class="auth-card-shell">
            <div class="auth-card">
                <div class="brand-logo">
                    <a href="{{url('/')}}" class="logo-bg-premium" aria-label="Homepage">
                        <x-ui.logo variant="light" size="lg" />
                    </a>
                </div>

                <h1>Welcome Back</h1>
                <p class="subtitle">Secure access to your trading platform</p>

                <form id="loginForm">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" class="form-control" id="email" placeholder="Enter your email" required autocomplete="email" aria-label="Email Address">
                            <i style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);" aria-hidden="true">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2 3.5A1.5 1.5 0 0 1 3.5 2h9A1.5 1.5 0 0 1 14 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 12.5v-9zm1.5-.5a.5.5 0 0 0-.5.5v.297l5.5 3.143L14 3.797V3.5a.5.5 0 0 0-.5-.5h-9zM14 4.903l-5.5 3.143L3 4.903V12.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V4.903z"/></svg>
                            </i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" class="form-control" id="password" placeholder="Enter your password" required autocomplete="current-password" aria-label="Password">
                            <i style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);" aria-hidden="true">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 9h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-5a1 1 0 0 1 1-1z"/></svg>
                            </i>
                        </div>
                    </div>

                    <div class="auth-options">
                        <label class="remember-me">
                            <input type="checkbox" id="rememberMe" aria-label="Remember me">
                            <span>Remember me</span>
                        </label>
                        <a href="{{route('reset')}}" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn-auth-primary" aria-label="Sign In">
                        <span>Sign In</span>
                        <div class="icon-wrapper" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </button>

                    <div style="text-align:right; margin-top: 8px;">
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#termsModal" class="terms-link">Terms of Service</a>
                    </div>
                </form>

                <div class="auth-divider"><span>or</span></div>

                <button id="googleBtn" class="btn-google" onclick="location.href='{{route('login.google')}}'">
                    <svg viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/></svg>
                    Continue with Google
                </button>

                <div class="auth-footer">
                    Don't have an account? <a href="{{route('register-starter')}}">Create Account</a>
                </div>
            </div>
            </div>
        </div>

        <!-- Visual Side (Desktop only) -->
        <div class="auth-visual-side d-none d-md-flex">
            <div class="visual-content">
                <div style="margin-bottom: 3rem; display: flex; justify-content: center; opacity: 0.9; filter: drop-shadow(0 0 10px rgba(255,255,255,0.1));">
                    <x-ui.logo variant="light" size="lg" />
                </div>
                <h2>Trade Smarter.<br>Grow Faster.</h2>
                <p>Access global markets with institutional-grade tools, real-time analytics, and an advanced trading engine.</p>
                
                <div style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05);">
                    <div style="color: rgba(255,255,255,0.3); letter-spacing: 4px; font-weight: 500; font-size: 0.85rem; text-transform: uppercase;">
                        World Class Trading Engine
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms Modal (preserving existing functionality) -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(0, 0, 0, 0.95); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; color: #e2e8f0;">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <h5 class="modal-title fw-bold">{{$lock->title ?? ''}}</h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="min-height: 35vh; padding: 24px;">
                    {!! $lock->message ?? ''!!}
                </div>
            </div>
        </div>
    </div>

    <script>
        function hidePreloader() {
            const preloader = document.getElementById('preloader');
            const wrapper = document.getElementById('auth-wrapper');
            if (preloader && preloader.style.display !== 'none') {
                preloader.style.opacity = '0';
                setTimeout(() => { 
                    preloader.style.display = 'none'; 
                    if (wrapper) wrapper.style.display = 'flex'; 
                }, 500);
            }
        }

        if (document.readyState === 'complete') {
            hidePreloader();
        } else {
            window.addEventListener('load', hidePreloader);
        }

        // Failsafe: Hide preloader after 5 seconds no matter what
        setTimeout(hidePreloader, 5000);

        const safeToastr = {
            success: (msg, title) => { if (typeof toastr !== 'undefined') toastr.success(msg, title); else console.log('Success:', msg); },
            error: (msg, title) => { if (typeof toastr !== 'undefined') toastr.error(msg, title); else console.error('Error:', msg); }
        };

        // Login Form Handler
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
            const preloader = document.getElementById('preloader');
            const wrapper = document.getElementById('auth-wrapper');

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (preloader) { preloader.style.display = 'flex'; preloader.style.opacity = '1'; }
            if (wrapper) wrapper.style.display = 'none';

            try {
                const response = await fetch("{{route('login.post')}}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ email, password })
                });
                const data = await response.json();

                if (data.errors) {
                    for (var key in data.errors) {
                        if (data.errors.hasOwnProperty(key)) {
                            safeToastr.error(data.errors[key][0], 'Error');
                        }
                    }
                    if (preloader) preloader.style.display = 'none';
                    if (wrapper) wrapper.style.display = 'flex';
                }

                if (data.error) {
                    safeToastr.error(data.error, 'Error');
                    if (preloader) preloader.style.display = 'none';
                    if (wrapper) wrapper.style.display = 'flex';
                    return;
                } else if (data.status == 'login') {
                    localStorage.setItem('tutorialSkipped', '');
                    localStorage.setItem('notificationShown', 'false');
                    localStorage.setItem('video', 'false');
                    setTimeout(() => { location.href = "{{ route('dashboard.index') }}" }, 2000);
                } else if (data.status == 'google') {
                    localStorage.setItem('tutorialSkipped', '');
                    localStorage.setItem('notificationShown', 'false');
                    localStorage.setItem('video', 'false');
                    setTimeout(() => { location.href = "{{route('google')}}" }, 2000);
                } else if (data.status == 'otp') {
                    localStorage.setItem('notificationShown', 'false');
                    localStorage.setItem('video', 'false');
                    localStorage.setItem('tutorialSkipped', '');
                    setTimeout(() => { location.href = "{{ route('otp') }}" }, 2000);
                } else {
                    // Default fallback
                    setTimeout(() => { location.href = "{{ route('dashboard.index') }}" }, 2000);
                }
            } catch (error) {
                console.error(error);
                safeToastr.error("Secure access is currently unavailable. Please try again shortly.", 'Error');
                if (preloader) preloader.style.display = 'none';
                if (wrapper) wrapper.style.display = 'flex';
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


