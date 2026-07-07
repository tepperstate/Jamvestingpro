<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'slug' => 'welcome',
                'name' => 'Welcome Email',
                'subject' => 'Welcome to {{ site()->name }}',
                'content' => view('email.welcome')->render(),
                'variables' => [
                    'site()->name' => 'Name of the platform',
                    'site()->email' => 'Support email address',
                    'env("APP_URL")' => 'Website URL',
                ],
            ],
            [
                'slug' => 'otp',
                'name' => 'OTP Verification',
                'subject' => 'Security Verification Code',
                'content' => view('email.otp', ['otp' => '{{ $otp }}'])->render(),
                'variables' => [
                    'otp' => 'The 6-digit verification code',
                ],
            ],
            [
                'slug' => 'deposit',
                'name' => 'Deposit Confirmation',
                'subject' => 'Deposit Confirmed',
                'content' => view('email.deposit', ['amount' => '{{ $amount }}', 'method' => '{{ $method }}', 'trx' => '{{ $trx }}'])->render(),
                'variables' => [
                    'amount' => 'Deposited amount',
                    'method' => 'Payment method',
                    'trx' => 'Transaction ID',
                ],
            ],
            [
                'slug' => 'withdrawal',
                'name' => 'Withdrawal Processed',
                'subject' => 'Withdrawal Processed',
                'content' => view('email.withdrawal', ['amount' => '{{ $amount }}', 'destination' => '{{ $destination }}'])->render(),
                'variables' => [
                    'amount' => 'Withdrawn amount',
                    'destination' => 'Destination address/bank',
                ],
            ],
            [
                'slug' => 'trade',
                'name' => 'Trade Execution Receipt',
                'subject' => 'Trade Execution Receipt',
                'content' => view('email.trade_receipt', [
                    'side' => '{{ $side }}',
                    'symbol' => '{{ $symbol }}',
                    'quantity' => '{{ $quantity }}',
                    'price' => '{{ $price }}',
                    'total' => '{{ $total }}',
                    'fees' => '{{ $fees }}',
                ])->render(),
                'variables' => [
                    'side' => 'BUY or SELL',
                    'symbol' => 'Asset ticker symbol',
                    'quantity' => 'Amount traded',
                    'price' => 'Execution price',
                    'total' => 'Total trade value',
                    'fees' => 'Trading commissions',
                ],
            ],
            [
                'slug' => 'tier',
                'name' => 'Account Tier Upgrade',
                'subject' => 'Account Tier Upgraded!',
                'content' => view('email.tier_upgrade', [
                    'tier' => '{{ $tier }}',
                    'benefit1' => '{{ $benefit1 }}',
                    'benefit2' => '{{ $benefit2 }}',
                    'benefit3' => '{{ $benefit3 }}',
                    'benefit4' => '{{ $benefit4 }}',
                ])->render(),
                'variables' => [
                    'tier' => 'New account level (e.g., Gold)',
                    'benefit1' => 'First feature benefit',
                    'benefit2' => 'Second feature benefit',
                    'benefit3' => 'Third feature benefit',
                    'benefit4' => 'Fourth feature benefit',
                ],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(['slug' => $template['slug']], $template);
        }
    }
}
