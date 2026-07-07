<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OxaPayService implements PaymentGatewayInterface
{
    protected string $merchantKey;

    public function __construct()
    {
        $this->merchantKey = env('OXAPAY_MERCHANT_KEY', '');
    }

    public function generatePaymentUrl(float $amount, string $currency, string $cryptoCurrency, string $txnId): string
    {
        $response = Http::post('https://api.oxapay.com/merchants/request', [
            'merchant' => $this->merchantKey,
            'amount' => $amount,
            'currency' => $currency,
            'orderId' => $txnId,
            'callbackUrl' => route('webhook.oxapay'),
            'returnUrl' => route('home'), // User redirected here after payment
            'description' => 'Deposit for ' . $txnId
        ]);

        if ($response->successful() && $response->json('result') === 100) {
            return $response->json('payLink');
        }

        Log::error('OxaPay Payment Request Failed', ['response' => $response->json()]);
        throw new \Exception('Failed to generate OxaPay payment link: ' . $response->json('message', 'Unknown error'));
    }

    public function verifyWebhook(Request $request): bool
    {
        $hmacHeader = $request->header('HMAC');
        $payload = $request->getContent();
        
        $calculatedHmac = hash_hmac('sha512', $payload, $this->merchantKey);
        
        return hash_equals($calculatedHmac, $hmacHeader ?? '');
    }
}
