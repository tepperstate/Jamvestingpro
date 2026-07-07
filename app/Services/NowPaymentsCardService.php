<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NowPaymentsCardService implements PaymentGatewayInterface
{
    protected string $apiKey;
    protected string $ipnSecret;

    public function __construct()
    {
        $this->apiKey = env('NOWPAYMENTS_API_KEY', '');
        $this->ipnSecret = env('NOWPAYMENTS_IPN_SECRET', '');
    }

    public function generatePaymentUrl(float $amount, string $currency, string $cryptoCurrency, string $txnId): string
    {
        // For fiat-to-crypto, we generally generate a standard invoice 
        // and allow the user to select the fiat checkout option (Guardarian/Mercuryo) 
        // on the NowPayments invoice page.
        
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.nowpayments.io/v1/invoice', [
            'price_amount' => $amount,
            'price_currency' => $currency,
            'order_id' => $txnId,
            'order_description' => 'Credit Card Deposit for ' . $txnId,
            'ipn_callback_url' => route('webhook.nowpayments'),
            'success_url' => route('home'),
            'cancel_url' => route('deposit')
            // Omitting pay_currency forces the user to select a coin or use the fiat widget
        ]);

        if ($response->successful() && $response->json('invoice_url')) {
            return $response->json('invoice_url');
        }

        Log::error('NowPayments Card Payment Request Failed', ['response' => $response->json()]);
        throw new \Exception('Failed to generate NowPayments Card link: ' . $response->json('message', 'Unknown error'));
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
