@extends('email.layout')

@section('title', 'Security Verification')

@section('header-icon')
    @if(file_exists(public_path('asset/icons/email_icon_security.png')))
        @if(isset($message))
            <img src="{{ $message->embed(public_path('asset/icons/email_icon_security.png')) }}" alt="Security" style="width: 64px; height: 64px; margin: 0 auto 20px auto; border-radius: 50%;">
        @else
            <img src="{{ asset('asset/icons/email_icon_security.png') }}" alt="Security" style="width: 64px; height: 64px; margin: 0 auto 20px auto; border-radius: 50%;">
        @endif
    @endif
@endsection

@section('content')
    <p class="text">Hello,</p>
    
    <p class="text">
        We received a request to verify your account or authorize a sensitive action. Please use the verification code below to proceed.
    </p>

    <div style="background-color: #1e222d; border-radius: 8px; padding: 20px; text-align: center; margin: 30px 0; border: 1px solid #2a2e39;">
        <span style="font-size: 36px; font-weight: 700; color: #00d166; letter-spacing: 5px;">{{ $otp ?? '123456' }}</span>
    </div>

    <p class="text">
        This OTP is valid for the next 15 minutes. 
    </p>

    <p class="text" style="color: #ed4e50; font-size: 14px;">
        If you did not request this OTP, please ignore this email or contact support immediately as your account credentials may be compromised.
    </p>
@endsection
