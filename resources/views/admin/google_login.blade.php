<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Two-Factor Authentication | Admin Control</title>
    
    <!-- Premium Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Base Styling -->
    <link rel="stylesheet" href="{{ asset('css/sb-admin-2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mycss.css') }}">

    <style>
        :root {
            --bg-main: #000000;
        }
        body {
            background-color: #ffffff !important;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: #f1f5f9;
        }
        .auth-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            animation: fadeIn 0.6s ease-out;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: 24px;
            padding: 40px 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .logo-container {
            background: transparent;
            padding: 8px 16px;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            margin-bottom: 32px;
        }
        .logo-container img {
            height: 32px;
            width: auto;
            object-fit: contain;
        }
        .auth-header h2 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.75rem;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }
        .auth-header p {
            color: #94a3b8;
            font-size: 0.95rem;
            margin-bottom: 32px;
        }
        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 8px;
            display: block;
        }
        .otp-input {
            background: rgba(0, 0, 0, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: white !important;
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            text-align: center;
            letter-spacing: 0.5em;
            padding: 12px !important;
            border-radius: 12px !important;
            height: 64px !important;
            transition: all 0.3s ease;
        }
        .otp-input:focus {
            border-color: #0ea5e9 !important;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15) !important;
            background: rgba(0, 0, 0, 0.3) !important;
        }
        .btn-premium {
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
            border: none;
            color: white;
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            font-size: 1rem;
            margin-top: 24px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
            filter: brightness(1.1);
        }
        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            font-size: 0.88rem;
            margin-top: 24px;
            transition: color 0.2s ease;
        }
        .back-link:hover {
            color: #94a3b8;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="glass-card text-center">
            <div class="logo-container">
                <img src="{{ asset('assets/img/favicon.svg') }}" alt="{{ site()->name }}">
            </div>
            
            <div class="auth-header">
                <h2>Security Check</h2>
                <p>Please enter the 6-digit authentication code from your authenticator app.</p>
            </div>

            <form action="{{ route('google_loginslogin') }}" method="post" id="2faForm">
                @csrf
                @if(session()->has('error'))
                    <div class="alert alert-danger border-0 mb-4" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e; border-radius: 12px; font-size: 0.85rem;">
                        <i class="ri-error-warning-line me-2"></i> {{ session()->get('error') }}
                    </div>
                @endif

                <div class="form-group mb-4">
                    <label class="form-label">Authentication Code</label>
                    <input type="text" name="code" class="form-control otp-input" maxlength="6" placeholder="000000" autofocus required>
                </div>

                <button type="submit" class="btn btn-premium btn-block">
                    Verify Identity
                </button>
            </form>

            <a href="javascript:history.go(-1)" class="back-link">
                <i class="ri-arrow-left-line"></i> Back to Login
            </a>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script>
        // Automatic numeric input restriction
        $('.otp-input').on('keypress', function(e) {
            if (e.which < 48 || e.which > 57) e.preventDefault();
        });
    </script>
</body>
</html>

