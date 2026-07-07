@extends('email.layout')

@section('title', 'Deposit Confirmed')

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
    <p class="text">Great news!</p>
    
    <p class="text">
        Your deposit has been successfully processed and credited to your account. Your updated balance is now available for trading.
    </p>

    <table class="data-table">
        <tr class="data-row">
            <td class="data-label">Amount</td>
            <td class="data-value">{{ $amount ?? '$0.00' }}</td>
        </tr>
        <tr class="data-row">
            <td class="data-label">Method</td>
            <td class="data-value">{{ $method ?? 'Direct Deposit' }}</td>
        </tr>
        <tr class="data-row">
            <td class="data-label">Status</td>
            <td class="data-value text-green">Completed</td>
        </tr>
        <tr class="data-row">
            <td class="data-label">Transaction ID</td>
            <td class="data-value" style="font-size: 11px;">{{ $trx ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="button-wrapper">
        <a href="{{ env('APP_URL') }}/dashboard" class="button">Start Trading</a>
    </div>

    <p class="text">
        If you have any questions regarding this transaction, please feel free to reach out to our support team.
    </p>
@endsection
