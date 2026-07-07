@extends('email.layout')

@section('title', 'Welcome to ' . (site()->name ?? 'Our Platform'))

@section('header-icon')
    <!-- Placeholder for the AI-generated success icon, we can use an img tag pointing to a CDN or embedded base64, for now a simple inline SVGs can work as a fallback, but the user requested AI images. We'll use a local path if needed to test, but we can't embed the generated image from our brain directly without moving it to the public folder. For now, we'll assume the logo is sufficient for the welcome email header. -->
    @if(file_exists(public_path('asset/icons/email_icon_success.png')))
        @if(isset($message))
            <img src="{{ $message->embed(public_path('asset/icons/email_icon_success.png')) }}" alt="Success" style="width: 64px; height: 64px; margin: 0 auto 20px auto; border-radius: 50%;">
        @else
            <img src="{{ asset('asset/icons/email_icon_success.png') }}" alt="Success" style="width: 64px; height: 64px; margin: 0 auto 20px auto; border-radius: 50%;">
        @endif
    @endif
@endsection

@section('content')
    <p class="text">Hi there,</p>
    
    <p class="text">
        Thank you for choosing <span class="text-highlight">{{ site()->name ?? 'our platform' }}</span> as your preferred trading partner. We are delighted to inform you that your trading account has been successfully established and verified.
    </p>

    <p class="text">
        To fully activate your account and begin trading, we kindly request that you make an initial deposit. Please ensure your login credentials are stored securely and not shared with anyone to safeguard your account's integrity.
    </p>

    <p class="text">
        As a valued member, you now have access to an extensive array of U.S. tradable assets, including stocks, ETFs, crypto, options, and more. Our user-friendly platform is designed to provide you with a seamless and efficient trading experience.
    </p>

    <div class="button-wrapper">
        <a href="{{ env('APP_URL') }}/dashboard" class="button">Go to Dashboard</a>
    </div>

    <hr style="border: 0; border-top: 1px solid #2a2e39; margin: 30px 0;">

    <p class="text" style="color: #ffffff; font-weight: 600;">Here are some of the Pluses when trading with us:</p>
    
    <ul style="color: #a3a8b3; font-size: 15px; line-height: 1.8; margin-bottom: 30px; padding-left: 20px;">
        <li>Zero Deposit Fees & attractive spreads</li>
        <li>Regulated and licensed in multiple jurisdictions</li>
        <li>A comprehensive Trading Academy</li>
        <li>FREE market alerts and notifications</li>
        <li>Advanced risk management tools</li>
    </ul>

    <p class="text">
        We are committed to your success and look forward to supporting you on your trading journey.
    </p>
@endsection
