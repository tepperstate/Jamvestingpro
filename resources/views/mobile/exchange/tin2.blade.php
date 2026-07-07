<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{site()->name}}</title>
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <style>
        body { margin: 0; padding: 0; background: #0a0b0e; color: #fff; font-family: 'Outfit', sans-serif; -webkit-font-smoothing: antialiased; }
        .glass-bg {
            min-height: 100vh;
            background: radial-gradient(circle at top right, rgba(255,215,0,0.1), transparent), radial-gradient(circle at bottom left, rgba(255,215,0,0.05), transparent), #0a0b0e;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .mobile-glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 24px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            padding: 30px 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5), 0 0 20px rgba(255,215,0,0.05);
        }
        .success-icon {
            width: 70px; height: 70px;
            background: rgba(255,215,0,0.1);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            color: #FFD700;
            font-size: 35px;
        }
        h2 { margin: 0 0 15px; font-size: 22px; font-weight: 800; color: #fff; }
        p { font-size: 14px; color: rgba(255,255,255,0.7); line-height: 1.5; margin-bottom: 20px; }
        .code-display {
            background: rgba(0,0,0,0.5);
            border: 1px dashed #FFD700;
            color: #FFD700;
            padding: 15px;
            border-radius: 12px;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 5px;
            margin-bottom: 25px;
        }
        .btn-gold {
            background: linear-gradient(135deg, #FFD700 0%, #990000 100%);
            color: #000;
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-weight: 800;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: block;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="glass-bg">
        <div class="mobile-glass-card">
            <div class="success-icon">
                <i class="ri-shield-check-fill"></i>
            </div>
            <h2>Clearance Generated</h2>
            @php $code = rand(111111,999999) @endphp
            <p>Your regulatory request is successful. Below is your unique Compliance ID (TIN):</p>
            
            <div class="code-display">{{$code}}</div>
            
            <p style="font-size: 12px; opacity: 0.6; margin-top:-10px;">Please use this identifier to authorize final liquidation.</p>

            <a href="{{route('tin3')}}" class="btn-gold">Complete Verification</a>
        </div>
    </div>
    
    <script src="{{asset('assets/js/jquery-3.4.1.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @if(session('status'))
    <script>
        toastr.success("{{session('status')}}", 'Successful');
    </script>
    @endif
</body>
</html>
