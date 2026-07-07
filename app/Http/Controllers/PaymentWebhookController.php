<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Balance;
use App\Models\Deposit;
use App\Models\Referral;
use App\Notifications\DepositNotification;
use Illuminate\Support\Facades\Notification;
use App\Services\OxaPayService;
use App\Services\NowPaymentsService;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function oxapay(Request $request)
    {
        $service = new OxaPayService();
        if (!$service->verifyWebhook($request)) {
            Log::warning('Invalid OxaPay Webhook signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $txnId = $request->input('orderId');
        $status = $request->input('status'); // Paid, Processing, Failed...

        if (strtolower($status) === 'paid' || strtolower($status) === 'finished') {
            $this->processPayment('oxapay', $txnId);
        }

        return response()->json(['status' => 'success']);
    }

    public function nowpayments(Request $request)
    {
        $service = new NowPaymentsService();
        if (!$service->verifyWebhook($request)) {
            Log::warning('Invalid NowPayments Webhook signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $txnId = $request->input('order_id');
        $status = $request->input('payment_status');

        if (strtolower($status) === 'finished') {
            $this->processPayment('nowpayments', $txnId);
        }

        return response()->json(['status' => 'success']);
    }

    protected function processPayment($gateway, $txnId)
    {
        $payment = DB::table('crypto_payments')
            ->where('gateway_name', $gateway)
            ->where('txn_id', $txnId)
            ->where('status', 'pending')
            ->first();

        if (!$payment) {
            Log::info("Payment already processed or not found: {$txnId}");
            return;
        }

        // Update crypto_payments status
        DB::table('crypto_payments')->where('id', $payment->id)->update([
            'status' => 'success',
            'updated_at' => now(),
        ]);

        // Find main deposit record and update it
        $deposit = Deposit::where('trx_id', $txnId)->first();
        if ($deposit && $deposit->status === 'pending') {
            $deposit->update(['status' => 'success']);

            // Increment user balance
            Balance::where('user_id', $deposit->user_id)->where('symbol', 'USD')->increment('amount', $deposit->amount);

            // Handle referrals
            $check_referral = Referral::where('referral_id', $deposit->user_id)->exists();
            if ($check_referral) {
                $datas = Referral::where('referral_id', $deposit->user_id)->first();

                $user_id = $datas->user_id;
                $ref_id = $datas->referral_id;

                $divided_amount = (10 / 100) * $deposit->amount;

                Balance::where('user_id', $user_id)->where('symbol', 'USD')->increment('referral', $divided_amount);
                Balance::where('user_id', $user_id)->where('symbol', 'USD')->increment('amount', $divided_amount);

                Referral::where('referral_id', $ref_id)->increment('balance', $divided_amount);
            }

            // Notification
            // $deposit->buyer_email is not accessible directly if it's missing from model,
            // the previous code was using auth()->guard('web')->user()->email
            // Let's use the DB query
            $userEmail = DB::table('users')->where('id', $deposit->user_id)->value('email');
            
            if ($userEmail) {
                $text = [
                    'greeting' => 'Hello User',
                    'subject' => 'Your Deposit was Successful',
                    'body' => 'Your Deposit to our wallet was approved and credited to your account. You can now start trading.',
                    'data' => 'Click Here',
                    'url' => url('/user'),
                    'thanks' => 'Thank you for choosing ' . env('APP_NAME'),
                ];
                Notification::route('mail', $userEmail)->notify(new DepositNotification($text));
            }
        }
    }
}
