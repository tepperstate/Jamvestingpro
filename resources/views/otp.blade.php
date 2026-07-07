<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta data-n-head="ssr" data-hid="og:image" property="og:image" content="{{asset('assets/img/favicon.svg')}}">
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}">
    <title>Verify Access | {{site()->name}}</title>
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

        body {
            font-family: 'Inter', sans-serif;
            background: #060b18;
            min-height: 100vh;
            overflow-x: hidden;
            color: #e2e8f0;
        }

        /* === Animated Background === */
        .auth-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }
        .auth-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 20% 50%, rgba(59, 130, 246, 0.08) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 20%, rgba(255, 51, 51, 0.06) 0%, transparent 50%),
                        radial-gradient(ellipse at 50% 80%, rgba(139, 92, 246, 0.05) 0%, transparent 50%);
            animation: bgShift 20s ease-in-out infinite alternate;
        }
        @keyframes bgShift {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-5%, -5%) rotate(3deg); }
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: orbFloat 15s ease-in-out infinite;
        }
        .orb-1 { width: 400px; height: 400px; background: rgba(59, 130, 246, 0.15); top: 10%; left: 5%; animation-delay: 0s; }
        .orb-2 { width: 300px; height: 300px; background: rgba(255, 51, 51, 0.12); bottom: 10%; right: 10%; animation-delay: -5s; }
        .orb-3 { width: 250px; height: 250px; background: rgba(139, 92, 246, 0.10); top: 50%; right: 30%; animation-delay: -10s; }
        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0); }
            25% { transform: translate(30px, -40px); }
            50% { transform: translate(-20px, 20px); }
            75% { transform: translate(40px, 30px); }
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
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* === Glass Card === */
        .auth-card {
            width: 100%;
            max-width: 440px;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            padding: 48px 40px;
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.4),
                        inset 0 1px 0 rgba(255, 255, 255, 0.05);
            animation: cardSlideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            text-align: center;
        }

        @keyframes cardSlideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-logo {
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


        

        .auth-card h1 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #f1f5f9;
        }

        .auth-card .subtitle {
            color: rgba(255,255,255,0.5);
            font-size: 0.95rem;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 16px;
            color: #f1f5f9;
            font-size: 1.1rem;
            font-family: 'Inter', sans-serif;
            text-align: center;
            letter-spacing: 4px;
            transition: all 0.3s ease;
            outline: none;
            margin-bottom: 24px;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(153, 0, 0, 0.5);
            box-shadow: 0 0 0 4px rgba(153, 0, 0, 0.1);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:focus {
            -webkit-text-fill-color: #f1f5f9 !important;
            -webkit-box-shadow: 0 0 0px 1000px rgba(0, 0, 0, 0.9) inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .btn-auth-primary {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #990000, #660000);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 24px;
        }

        .btn-auth-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px -5px rgba(153, 0, 0, 0.4);
        }

        .resend-section {
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.08);
            margin-top: 24px;
        }

        .resend-text {
            color: rgba(255,255,255,0.4);
            font-size: 0.85rem;
            margin-bottom: 16px;
        }

        .btn-resend {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #f1f5f9;
            padding: 10px 24px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }

        .btn-resend:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
            transform: translateY(-1px);
        }

        /* === Preloader === */
        .preloader-wrapper {
            position: fixed; inset: 0; z-index: 9999;
            background: #060b18;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: 24px;
            transition: opacity 0.5s ease;
        }
        .preloader-wrapper .logo-bg-premium {
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
    <div class="auth-wrapper" id="auth-wrapper">
        <div class="auth-card">
            <div class="brand-logo">
                <a href="{{url('/')}}" class="logo-bg-premium">
                    <x-ui.logo variant="light" size="lg" />
                </a>
            </div>

            <h1>Verification Required</h1>
            @if(auth()->check() && auth()->user()->is_2fa_enabled)
                <p class="subtitle">Please enter the 6-digit code from your <strong>Google Authenticator</strong> app.</p>
            @else
                <p class="subtitle">Please enter the security code sent to your registered email address.</p>
            @endif

            <form id="otpForm">
                @csrf
                <input type="text" name="otp" id="otp_value" class="form-control" placeholder="••••••" maxlength="6" required autofocus autocomplete="one-time-code">
                
                <button type="submit" class="btn-auth-primary" id="verifyBtn">Verify Identity</button>

                <div id="display_msg" style="margin-top: -12px; margin-bottom: 20px;"></div>
            </form>

            <div class="resend-section">
                <p class="resend-text">Didn't receive the code? Check your spam folder or try again.</p>
                <div id="resendBtn" class="btn-resend">Resend Code</div>
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

        // AJAX Logic
        const otpForm = document.getElementById("otpForm");
        const resendBtn = document.getElementById("resendBtn");
        const displayMsg = document.getElementById('display_msg');
        const preloader = document.getElementById('preloader');
        const verifyBtn = document.getElementById('verifyBtn');

        if (otpForm) {
            otpForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
                const otp_value = document.getElementById("otp_value").value;

                if (preloader) { preloader.style.display = 'flex'; preloader.style.opacity = '1'; }

                try {
                    const response = await fetch("{{url('login/otp')}}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ code: otp_value })
                    });
                    const data = await response.json();

                    if (data.errors) {
                        for (var key in data.errors) {
                            if (data.errors.hasOwnProperty(key)) { safeToastr.error(data.errors[key][0], 'Error'); }
                        }
                    }
                    if (data.error) {
                        safeToastr.error(data.error, 'Error');
                        if (displayMsg) {
                            displayMsg.innerText = data.error;
                            displayMsg.style.color = '#ef4444';
                        }
                    }
                    if (data.google) {
                        safeToastr.success("Verification successful!", "Success");
                        setTimeout(() => { location.href = "{{route('google')}}" }, 2000);
                        return;
                    }
                    if (data.status) {
                        safeToastr.success("Identity verified!", "Success");
                        setTimeout(() => { location.href = "{{url('/dashboard')}}" }, 2000);
                        return;
                    }
                } catch (error) {
                    console.error(error);
                    safeToastr.error("Server connection lost. Please try again.", 'Error');
                } finally {
                    if (preloader) {
                        preloader.style.opacity = '0';
                        setTimeout(() => { preloader.style.display = 'none'; }, 500);
                    }
                }
            });
        }

        if (resendBtn) {
            resendBtn.addEventListener('click', async (e) => {
                const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
                if (preloader) { preloader.style.display = 'flex'; preloader.style.opacity = '1'; }

                try {
                    const response = await fetch("{{route('resend_otp')}}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                    });
                    const data = await response.json();

                    if (data.error) {
                        safeToastr.error(data.error, 'Error');
                    }
                    if (data.status) {
                        safeToastr.success(data.status, 'Code Sent');
                        if (displayMsg) {
                            displayMsg.innerText = data.status;
                            displayMsg.style.color = '#ff3333';
                        }
                    }
                } catch (error) {
                    console.error(error);
                    safeToastr.error("Could not resend code. Try again later.", 'Error');
                } finally {
                    if (preloader) {
                        preloader.style.opacity = '0';
                        setTimeout(() => { preloader.style.display = 'none'; }, 500);
                    }
                }
            });
        }
    </script>
</body>
</html>
