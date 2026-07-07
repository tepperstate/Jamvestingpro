<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NowPaymentsService implements PaymentGatewayInterface
{
    protected string $apiKey;
    protected string $ipnSecret;

    public function __construct()
    {
        $settings = \Illuminate\Support\Facades\DB::table('payment__settings')->first();
        $this->apiKey = $settings->nowpayments_api_key ?? env('NOWPAYMENTS_API_KEY', '');
        $this->ipnSecret = $settings->nowpayments_ipn_secret ?? env('NOWPAYMENTS_IPN_SECRET', '');
    }

    public function generatePaymentUrl(float $amount, string $currency, string $cryptoCurrency, string $txnId): string
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.nowpayments.io/v1/invoice', [
            'price_amount' => $amount,
            'price_currency' => $currency,
            'pay_currency' => $cryptoCurrency,
            'ipn_callback_url' => route('webhook.nowpayments'),
            'order_id' => $txnId,
            'order_description' => 'Deposit for ' . $txnId,
            'success_url' => route('home'),
            'cancel_url' => route('deposit')
        ]);

        if ($response->successful() && $response->json('invoice_url')) {
            return $response->json('invoice_url');
        }

        Log::error('NowPayments Payment Request Failed', ['response' => $response->json()]);
        throw new \Exception('Failed to generate NowPayments link: ' . $response->json('message', 'Unknown error'));
    }

    public function verifyWebhook(Request $request): bool
    {
        $signature = $request->header('x-nowpayments-sig');
        if (!$signature) {
            return false;
        }

        $requestData = $request->toArray();
        ksort($requestData);
        $sortedJson = json_encode($requestData, JSON_UNESCAPED_SLASHES);

        $calculatedSignature = hash_hmac('sha512', $sortedJson, $this->ipnSecret);

        return hash_equals($calculatedSignature, $signature);
    }
}
