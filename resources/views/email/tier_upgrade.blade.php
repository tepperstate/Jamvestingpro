@extends('email.layout')

@section('title', 'Account Tier Upgraded!')

@section('header-icon')
    @if(file_exists(public_path('asset/icons/email_icon_success.png')))
        @if(isset($message))
            <img src="{{ $message->embed(public_path('asset/icons/email_icon_success.png')) }}" alt="Success" style="width: 64px; height: 64px; margin: 0 auto 20px auto; border-radius: 50%;">
        @else
            <img src="{{ asset('asset/icons/email_icon_success.png') }}" alt="Success" style="width: 64px; height: 64px; margin: 0 auto 20px auto; border-radius: 50%;">
        @endif
    @endif
@endsection

@section('content')
    <p class="text">Congratulations!</p>
    
    <p class="text">
        Based on your trading activity and account balance, you have been upgraded to <span class="text-green" style="font-weight: 700;">{{ $tier ?? 'Gold' }} Tier</span>.
    </p>

    <div style="background-color: #1e222d; border-radius: 8px; padding: 25px; margin: 30px 0; border: 1px dashed #00d166;">
        <p class="text" style="color: #ffffff; margin-bottom: 15px; font-weight: 600;">Your New Benefits:</p>
        <ul style="color: #a3a8b3; font-size: 14px; line-height: 1.6; padding-left: 20px;">
            <li>{{ $benefit1 ?? 'Reduced trading commissions' }}</li>
            <li>{{ $benefit2 ?? 'Priority customer support' }}</li>
            <li>{{ $benefit3 ?? 'Exclusive market research & insights' }}</li>
            <li>{{ $benefit4 ?? 'Higher withdrawal limits' }}</li>
        </ul>
    </div>

    <div class="button-wrapper">
        <a href="{{ env('APP_URL') }}/dashboard" class="button">Explore New Features</a>
    </div>

    <p class="text">
        Thank you for being a valued part of our growing community. We're excited to support your continued success.
    </p>
@endsection
