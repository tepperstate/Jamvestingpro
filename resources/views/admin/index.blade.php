<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}"> 

    <title>Admin System - Secure Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --glass-bg: rgba(13, 20, 33, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --accent-primary: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.5);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --radius-xl: 32px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            width: 100%;
        }

        .bg-vortex {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{asset('assets/img/admin_login_bg.png')}}"), linear-gradient(135deg, #000000 0%, #000000 100%);
            background-size: cover;
            background-position: center;
            filter: brightness(0.6) saturate(1.2);
            z-index: -2;
            transform: scale(1.05);
            animation: kenBurns 40s ease-out infinite alternate;
        }

        @keyframes kenBurns {
            from { transform: scale(1.05); }
            to { transform: scale(1.2) rotate(1deg); }
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(0, 0, 0, 0.2) 0%, rgba(2, 6, 23, 0.95) 80%);
            z-index: -1;
        }

        .login-orb {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--accent-primary) 0%, transparent 70%);
            filter: blur(140px);
            opacity: 0.1;
            border-radius: 50%;
            z-index: -1;
            animation: drift 20s infinite alternate;
        }

        @keyframes drift {
            0% { transform: translate(-20%, -20%) scale(1); }
            100% { transform: translate(20%, 20%) scale(1.2); }
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            width: 90%;
            max-width: 480px;
            padding: 3.5rem;
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.6),
                        inset 0 0 0 1px rgba(255,255,255,0.05);
            animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            z-index: 10;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .terminal-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .logo-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
        }

        .logo-wrapper::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 140%;
            height: 140%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
            z-index: -1;
        }

        .terminal-header .logo-container {
            display: inline-flex;
            width: 200px;
            height: 40px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            padding: 2px 6px;
            filter: drop-shadow(0 0 20px var(--accent-glow));
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .terminal-header .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        @media (max-width: 768px) {
            .terminal-header .logo-container {
                width: 180px;
                height: 36px;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2px 6px;
                background-color: #ffffff;
                border-radius: 12px;
            }
            .terminal-header .logo-container img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
        }

        .terminal-header h1 {
            color: var(--text-main);
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -1px;
            margin-bottom: 0.75rem;
        }

        .terminal-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: color 0.3s ease;
            pointer-events: none;
        }

        .input-field {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 14px 16px 14px 46px;
            color: var(--text-main);
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .input-field:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.1);
        }

        .input-field:focus {
            outline: none;
            background: rgba(255,255,255,0.08);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .input-field:focus + i,
        .input-wrapper:focus-within i {
            color: var(--primary);
        }

        .input-field::placeholder {
            color: rgba(148, 163, 184, 0.3);
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            transition: color 0.3s ease;
            z-index: 10;
        }

        .toggle-password:hover {
            color: var(--text-main);
        }

        .btn-authenticate {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-authenticate:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 45px -5px rgba(37, 99, 235, 0.7);
            filter: brightness(1.1);
        }

        .btn-authenticate:active {
            transform: translateY(-1px);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 1.15rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-3px, 0, 0); }
            40%, 60% { transform: translate3d(3px, 0, 0); }
        }

        .loader {
            display: none;
            width: 20px;
            height: 20px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s infinite linear;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-authenticate.is-loading .btn-text { display: none; }
        .btn-authenticate.is-loading .loader { display: block; }
        .btn-authenticate.is-loading i { display: none; }

        .system-meta {
            margin-top: 4rem;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.05);
            padding-top: 2rem;
        }

        .system-meta p {
            color: var(--text-muted);
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .system-meta strong {
            color: var(--text-main);
            font-weight: 600;
        }

        @media (max-width: 640px) {
            .glass-card {
                padding: 2.5rem 2rem;
            }
            .terminal-header h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-vortex"></div>
    <div class="bg-overlay"></div>
    <div class="login-orb"></div>

    <main class="glass-card">
        <header class="terminal-header">
            <div class="logo-wrapper">
                <div class="logo-container">
                    <img src="{{ asset('storage/image/logo_dark.svg') }}" alt="{{site()->name ?? 'Platform'}}">
                </div>
            </div>
            <h1>Secure Portal</h1>
            <p>Identity verification required for secure access</p>
        </header>

        @if(session()->has('not'))
            <div class="error-msg">
                <i class="fas fa-shield-virus"></i>
                <span>{{session()->get('not')}}</span>
            </div>
        @endif

        <form action="{{route('admin.login')}}" method="post" id="loginForm">
            @csrf
            
            <div class="form-group">
                <label for="email">Administrator Email</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" class="input-field" placeholder="admin@market-gateway.io" required autocomplete="email">
                    <i class="fas fa-satellite-dish"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Secure Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="input-field" placeholder="••••••••••••" required autocomplete="current-password">
                    <i class="fas fa-microchip"></i>
                </div>
            </div>

            <button type="submit" class="btn-authenticate" id="submitBtn">
                <i class="fas fa-unlock-keyhole"></i>
                <span class="btn-text">Log In to Dashboard</span>
                <div class="loader"></div>
            </button>
        </form>

        <footer class="system-meta">
            <p>Access IP: <strong>{{request()->ip()}}</strong></p>
            <p class="mt-1" style="opacity: 0.5;">Secure Encryption: <strong>Institutional Grade Security</strong></p>
        </footer>
    </main>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('is-loading');
            btn.disabled = true;
        });
    </script>
</body>
</html>
