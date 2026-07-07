@extends('email.layout')

@section('title', 'Trade Execution Receipt')

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
    <p class="text">Your trade has been executed.</p>
    
    <div style="background-color: #1e222d; border-radius: 8px; padding: 20px; margin-bottom: 30px; border: 1px solid #2a2e39;">
        <div style="font-size: 20px; font-weight: 700; color: #ffffff; margin-bottom: 5px;">{{ $side ?? 'BUY' }} {{ $symbol ?? 'AAPL' }}</div>
        <div style="font-size: 14px; color: #a3a8b3;">Executed at {{ now()->format('M d, Y H:i:s') }}</div>
    </div>

    <table class="data-table">
        <tr class="data-row">
            <td class="data-label">Quantity</td>
            <td class="data-value">{{ $quantity ?? '0.00' }}</td>
        </tr>
        <tr class="data-row">
            <td class="data-label">Execution Price</td>
            <td class="data-value">{{ $price ?? '$0.00' }}</td>
        </tr>
        <tr class="data-row">
            <td class="data-label">Total Value</td>
            <td class="data-value text-highlight">{{ $total ?? '$0.00' }}</td>
        </tr>
        <tr class="data-row">
            <td class="data-label">Fees</td>
            <td class="data-value">{{ $fees ?? '$0.00' }}</td>
        </tr>
    </table>

    <div class="button-wrapper">
        <a href="{{ env('APP_URL') }}/portfolio" class="button">View Portfolio</a>
    </div>

    <p class="text" style="font-size: 12px; text-align: center;">
        This is an automated trade confirmation. Detailed trade records are available in your account history.
    </p>
@endsection
