@extends('email.layout')

@section('title', 'Withdrawal Processed')

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
    <p class="text">Hello,</p>
    
    <p class="text">
        This is to confirm that your withdrawal request has been successfully processed. The funds have been sent to your designated account.
    </p>

    <table class="data-table">
        <tr class="data-row">
            <td class="data-label">Withdrawal Amount</td>
            <td class="data-value">{{ $amount ?? '$0.00' }}</td>
        </tr>
        <tr class="data-row">
            <td class="data-label">Destination</td>
            <td class="data-value">{{ $destination ?? 'External Wallet/Bank' }}</td>
        </tr>
        <tr class="data-row">
            <td class="data-label">Status</td>
            <td class="data-value text-green">Processed</td>
        </tr>
        <tr class="data-row">
            <td class="data-label">Date</td>
            <td class="data-value">{{ now()->format('M d, Y H:i') }}</td>
        </tr>
    </table>

    <p class="text">
        Most withdrawals reach their destination within 1-3 business days, depending on your bank or network congestion. 
    </p>

    <p class="text">
        If you didn't authorize this withdrawal, please contact us immediately.
    </p>
@endsection
