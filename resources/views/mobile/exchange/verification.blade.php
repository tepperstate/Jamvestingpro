@extends('layouts.user.app')
@section('title', 'Account Verification')
@section('content')
<style>
    /* Mobile Glassmorphism Premium */
    .mobile-glass-container {
        min-height: 100vh;
        background: #0a0b0e;
        padding: 20px 15px 80px;
        color: #ffffff;
        font-family: 'Outfit', sans-serif;
    }
    .mobile-glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 215, 0, 0.15); /* Gold Accent */
        border-radius: 24px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 20px rgba(255, 215, 0, 0.05);
        margin-bottom: 20px;
        text-align: center;
    }
    .gold-accent { color: #FFD700; }
    .btn-gold {
        background: linear-gradient(135deg, #FFD700 0%, #990000 100%);
        color: #000;
        border: none;
        border-radius: 16px;
        padding: 15px;
        font-weight: 800;
        width: 100%;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
        transition: transform 0.2s;
    }
    .btn-gold:active { transform: scale(0.95); }
    .btn-outline-gold {
        background: transparent;
        color: #FFD700;
        border: 1px solid #FFD700;
        border-radius: 16px;
        padding: 15px;
        font-weight: 800;
        width: 100%;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
        display: block;
        text-decoration: none;
    }
    .header-title { font-size: 24px; font-weight: 800; margin-bottom: 5px; }
</style>

<div class="mobile-glass-container">
    <div class="d-flex align-items-center mb-4">
        <h2 class="header-title mb-0">Verification</h2>
    </div>

    <div class="mobile-glass-card">
        <div style="width: 80px; height: 80px; background: rgba(255,215,0,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <i class="ri-mail-send-line" style="font-size: 40px; color: #FFD700;"></i>
        </div>
        <h3 style="font-weight: 700; font-size: 22px; margin-bottom: 10px;">Verify Your Email</h3>
        <p style="color: rgba(255,255,255,0.7); font-size: 14px; margin-bottom: 15px;">
            An email verification is required to access our Trading Services. An email was sent to:
        </p>
        <div style="background: rgba(0,0,0,0.3); border-radius: 12px; padding: 12px; margin-bottom: 20px; font-weight: 600; color: #FFD700;">
            {{ auth()->user() ? auth()->user()->email : 'your registered email' }}
        </div>
        <p style="color: rgba(255,255,255,0.5); font-size: 13px; margin-bottom: 25px;">
            Check your inbox or spam folder to verify your account.
        </p>

        <form action="{{route('resend')}}" method="post">
            @csrf
            <button type="submit" name="emailverification" class="btn-gold">
                Resend Link
            </button>
        </form>

        <a href="{{route('dashboard.index')}}" class="btn-outline-gold text-center">
            I've Verified
        </a>
    </div>

    @if(session('status'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if(typeof toastr !== 'undefined') {
                    toastr.success("{{session('status')}}", 'Successful');
                } else {
                    alert("{{session('status')}}");
                }
            });
        </script>
    @endif
</div>
@endsection
