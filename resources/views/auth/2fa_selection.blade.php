<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta data-n-head="ssr" data-hid="og:image" property="og:image" content="{{asset('assets/img/favicon.svg')}}">
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}">
    <title>Security Choice | {{site()->name}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #020b1a; /* Institutional Blue */
            min-height: 100vh;
            overflow-x: hidden;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* === Glass Card === */
        .selection-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 500px;
            background: rgba(4, 13, 31, 0.7);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 32px;
            padding: 48px 40px;
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .brand-logo {
            margin-bottom: 40px;
            display: flex;
            justify-content: center;
        }
        .logo-bg-premium {
            display: inline-flex;
            transition: transform 0.3s ease;
            height: 60px;
            align-items: center;
        }

        h1 { font-size: 1.8rem; font-weight: 800; margin-bottom: 12px; }
        .subtitle { color: rgba(255,255,255,0.5); font-size: 0.95rem; margin-bottom: 40px; }

        /* === Choice Options === */
        .choices-grid {
            display: grid;
            gap: 20px;
        }

        .choice-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            text-align: left;
            text-decoration: none;
            color: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .choice-item:hover {
            background: rgba(255,255,255,0.07);
            border-color: #990000;
            transform: translateY(-4px);
            box-shadow: 0 10px 30px -10px rgba(153, 0, 0, 0.3);
        }

        .icon-box {
            width: 56px;
            height: 56px;
            background: rgba(153, 0, 0, 0.1);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #990000;
            font-size: 1.5rem;
        }

        .choice-info h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 4px; }
        .choice-info p { font-size: 0.85rem; color: rgba(255,255,255,0.5); margin: 0; }

        .btn-logout {
            margin-top: 32px;
            display: inline-block;
            color: rgba(255,255,255,0.4);
            font-size: 0.9rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        .btn-logout:hover { color: #fff; }

    </style>
</head>
<body style="background: #060b18; color: #e2e8f0; font-family: 'Inter', sans-serif;">
    @include('marketing.partials.ambient')

    <div class="selection-card">
        <div class="brand-logo">
            <div class="logo-bg-premium">
                <x-ui.logo variant="light" size="lg" />
            </div>
        </div>

        <h1>Verify Identity</h1>
        <p class="subtitle">Please choose your preferred verification method to access your account.</p>

        <div class="choices-grid">
            <a href="{{route('google')}}" class="choice-item">
                <div class="icon-box">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 11V7a4 4 0 00-8 0v4m8 0a4 4 0 018 0v4m-8-4v4m-8 4h16a2 2 0 002-2v-8a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <div class="choice-info">
                    <h3>Google Authenticator</h3>
                    <p>Use the 6-digit code from your app.</p>
                </div>
            </a>

            <a href="{{route('otp')}}" class="choice-item">
                <div class="icon-box" style="background: rgba(153, 0, 0, 0.1); color: #990000;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div class="choice-info">
                    <h3>Email OTP</h3>
                    <p>Receive a security code via email.</p>
                </div>
            </a>
        </div>

        <a href="{{route('login')}}" class="btn-logout">Back to Login</a>
    </div>
</body>
</html>
