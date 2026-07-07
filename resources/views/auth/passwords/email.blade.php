<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta data-n-head="ssr" data-hid="og:image" property="og:image" content="{{asset('assets/img/favicon.svg')}}">
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}">
    <title>Reset Password | {{site()->name}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" />
    <script src="{{asset('new/vendor_components/jquery-3.3.1/jquery-3.3.1.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #060b18; min-height: 100vh; overflow-x: hidden; color: #e2e8f0; }
        .auth-bg { position: fixed; inset: 0; z-index: 0; overflow: hidden; }
        .auth-bg::before {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(ellipse at 20% 50%, rgba(59,130,246,0.08) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 20%, rgba(16,185,129,0.06) 0%, transparent 50%),
                        radial-gradient(ellipse at 50% 80%, rgba(139,92,246,0.05) 0%, transparent 50%);
            animation: bgShift 20s ease-in-out infinite alternate;
        }
        @keyframes bgShift { 0% { transform: translate(0,0) rotate(0deg); } 100% { transform: translate(-5%,-5%) rotate(3deg); } }
        .orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.4; animation: orbFloat 15s ease-in-out infinite; }
        .orb-1 { width: 400px; height: 400px; background: rgba(59,130,246,0.15); top: 10%; left: 5%; }
        .orb-2 { width: 300px; height: 300px; background: rgba(16,185,129,0.12); bottom: 10%; right: 10%; animation-delay: -5s; }
        .orb-3 { width: 250px; height: 250px; background: rgba(139,92,246,0.10); top: 50%; right: 30%; animation-delay: -10s; }
        @keyframes orbFloat { 0%,100% { transform: translate(0,0); } 25% { transform: translate(30px,-40px); } 50% { transform: translate(-20px,20px); } 75% { transform: translate(40px,30px); } }
        .grid-overlay { position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px); background-size: 60px 60px; pointer-events: none; }
        .auth-wrapper { position: relative; z-index: 2; display: flex; min-height: 100vh; align-items: center; justify-content: center; padding: 40px 20px; }
        .auth-card {
            width: 100%; max-width: 440px; background: rgba(0, 0, 0,0.6); backdrop-filter: blur(40px); -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255,255,255,0.08); border-radius: 28px; padding: 48px 40px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.05);
            animation: cardSlideUp 0.8s cubic-bezier(0.16,1,0.3,1);
        }
        @keyframes cardSlideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
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

        
        
        
        /* Mobile Card Adjustments */
        @media (max-width: 768px) {
            .auth-card { padding: 32px 20px 48px; }
        }

        .auth-card h1 { font-size: 1.6rem; font-weight: 700; text-align: center; margin-bottom: 8px; color: #f1f5f9; }
        .subtitle { text-align: center; color: rgba(255,255,255,0.5); font-size: 0.9rem; margin-bottom: 32px; }
        .form-group { margin-bottom: 24px; }
        .form-group label { display: block; font-size: 0.8rem; font-weight: 600; color: rgba(255,255,255,0.7); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 14px 16px; color: #ffffff !important; font-size: 0.95rem; transition: all 0.3s ease; outline: none; }
        .form-control:focus { background: rgba(255,255,255,0.06); border-color: rgba(153,0,0,0.5); box-shadow: 0 0 0 4px rgba(153,0,0,0.1); }
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover, 
        .form-control:-webkit-autofill:focus, 
        .form-control:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 1000px #060b18 inset !important;
            -webkit-text-fill-color: #ffffff !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        .btn-auth-primary { width: 100%; padding: 16px; background: linear-gradient(135deg, #990000, #660000); color: white; border: none; border-radius: 14px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 1px; }
        .btn-auth-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 30px -5px rgba(153,0,0,0.4); }
        .auth-footer { text-align: center; margin-top: 28px; font-size: 0.9rem; color: rgba(255,255,255,0.5); }
        .auth-footer a { color: #990000; text-decoration: none; font-weight: 600; }
        
        .preloader-wrapper { position: fixed; inset: 0; z-index: 9999; background: #060b18; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 24px; transition: opacity 0.5s ease; }
        .preloader-wrapper img { width: 200px; animation: logoPulse 2s ease-in-out infinite; }
        @keyframes logoPulse { 0%,100% { opacity: 1; } 50% { opacity: 0.5; } }
    </style>
</head>
<body>
    @include('marketing.partials.ambient')
    <div class="preloader-wrapper" id="preloader">
        <div class="logo-bg-premium" style="animation: logoPulse 2s ease-in-out infinite;">
            <x-ui.logo variant="light" size="lg" />
        </div>
    </div>

    <div class="auth-wrapper" id="auth-wrapper" style="display:none;">
        <div class="auth-card">
            <div class="brand-logo">
                <a href="{{url('/')}}" class="logo-bg-premium">
                    <x-ui.logo variant="light" size="lg" />
                </a>
            </div>

            <h1>Reset Password</h1>
            <p class="subtitle">Enter your email to receive a reset link</p>

            @if (session('status'))
                <div class="alert alert-success border-0" style="background: rgba(153, 0, 0, 0.1); color: #990000; border-radius: 12px;">
                    {{ session('status') }}
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        toastr.success("{{ session('status') }}", "Success");
                    });
                </script>
            @endif

            <form method="POST" action="{{ route('reset.post') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="your@email.com" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert" style="display:block;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn-auth-primary">
                    Send Reset Link
                </button>

                <div class="auth-footer">
                    Remembered? <a href="{{ route('login') }}">Back to Login</a>
                </div>
            </form>
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
    </script>
</body>
</html>
