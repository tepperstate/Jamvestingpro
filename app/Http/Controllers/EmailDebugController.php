<?php

namespace App\Http\Controllers;

class EmailDebugController extends Controller
{
    public function index()
    {
        $emails = [
            'welcome' => 'Welcome Email',
            'otp' => 'OTP / 2FA Verification',
            'deposit' => 'Deposit Confirmation',
            'withdrawal' => 'Withdrawal Processed',
            'trade' => 'Trade Execution Receipt',
            'tier' => 'Account Tier Upgrade',
        ];

        return '<h1>Email Preview Suite</h1><ul>'.
            collect($emails)->map(fn ($label, $key) => "<li><a href='/debug/emails/{$key}' target='_blank'>{$label}</a></li>")->implode('').
            '</ul>';
    }

    public function show($type)
    {
        switch ($type) {
            case 'welcome':
                return view('email.welcome');
            case 'otp':
                return view('email.otp', ['otp' => '882941']);
            case 'deposit':
                return view('email.deposit', [
                    'amount' => '$5,000.00',
                    'method' => 'Bank Wire / SEPA',
                    'trx' => 'TRX992848110',
                ]);
            case 'withdrawal':
                return view('email.withdrawal', [
                    'amount' => '$1,200.00',
                    'destination' => 'Wallet (0x71C...3a4)',
                ]);
            case 'trade':
                return view('email.trade_receipt', [
                    'side' => 'BUY',
                    'symbol' => 'TSLA',
                    'quantity' => '10.00',
                    'price' => '$185.42',
                    'total' => '$1,854.20',
                    'fees' => '$1.50',
                ]);
            case 'tier':
                return view('email.tier_upgrade', [
                    'tier' => 'Platinum',
                    'benefit1' => '0% Trading Commissions',
                    'benefit2' => 'Personal Account Manager',
                    'benefit3' => 'Advanced Trading API Access',
                    'benefit4' => 'Unlimited Withdrawals',
                ]);
            default:
                abort(404);
        }
    }
}
