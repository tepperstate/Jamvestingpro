<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Generate a payment URL for the user to visit and pay.
     *
     * @param float $amount The fiat amount to be paid.
     * @param string $currency The fiat currency (e.g., USD).
     * @param string $cryptoCurrency The target cryptocurrency (e.g., BTC, USDT).
     * @param string $txnId A unique internal transaction/order ID.
     * @return string The payment URL.
     */
    public function generatePaymentUrl(float $amount, string $currency, string $cryptoCurrency, string $txnId): string;

    /**
     * Verify the incoming webhook request.
     *
     * @param Request $request
     * @return bool
     */
    public function verifyWebhook(Request $request): bool;
}
