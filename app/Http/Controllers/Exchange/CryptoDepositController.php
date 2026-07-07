<?php

namespace App\Http\Controllers\Exchange;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendingPaymentRequest;
use App\Models\MonitoredWallet;
use App\Services\ExchangeRateService;
use App\Services\BlockchainScanner\AmountMatcher;
use App\Enums\PaymentRequestStatus;
use App\Enums\Blockchain;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CryptoDepositController extends Controller
{
    protected ExchangeRateService $exchangeRateService;
    protected AmountMatcher $amountMatcher;

    public function __construct(ExchangeRateService $exchangeRateService, AmountMatcher $amountMatcher)
    {
        $this->exchangeRateService = $exchangeRateService;
        $this->amountMatcher = $amountMatcher;
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'amount_usd' => 'required|numeric|min:1',
            'symbol' => 'required|string',
        ]);

        $user = auth()->user();
        $symbol = strtoupper($request->symbol);
        $amountUsd = (float) $request->amount_usd;

        // Find the wallet for this symbol
        $wallet = MonitoredWallet::where('symbol', $symbol)->where('is_active', true)->first();

        if (!$wallet) {
            return response()->json(['success' => false, 'message' => "Deposits for {$symbol} are currently unavailable."]);
        }

        // Convert USD to Crypto
        $cryptoAmount = $this->exchangeRateService->convertUsdToCrypto($amountUsd, $symbol);

        if (!$cryptoAmount) {
            return response()->json(['success' => false, 'message' => "Unable to fetch exchange rate for {$symbol}."]);
        }

        // Perturb the amount to make it unique
        $uniqueCryptoAmount = $this->amountMatcher->perturbAmount($cryptoAmount, $wallet->id);

        // Calculate expiration (e.g., 24 hours from now)
        $expiresAt = Carbon::now()->addHours(24);

        // Create Pending Payment Request
        $paymentRequest = PendingPaymentRequest::create([
            'user_id' => $user->id,
            'monitored_wallet_id' => $wallet->id,
            'expected_amount' => $uniqueCryptoAmount,
            'amount_usd' => $amountUsd,
            'currency' => $symbol,
            'status' => PaymentRequestStatus::PENDING->value,
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'payment_request_id' => $paymentRequest->id,
                'address' => $wallet->address,
                'network' => $wallet->network,
                'exact_crypto_amount' => rtrim(sprintf('%.8f', $uniqueCryptoAmount), '0'),
                'amount_usd' => $amountUsd,
                'currency' => $symbol,
                'expires_at' => $expiresAt->toIso8601String(),
            ]
        ]);
    }
}
