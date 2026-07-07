<?php

namespace App\Services;

use App\Models\Balance;
use App\Models\Swap;
use App\Models\SystemCoin;
use App\Models\UserWallet;
use Exception;
use Illuminate\Support\Facades\DB;

class SwapService
{
    public function executeSwap($userId, $fromSymbol, $toSymbol, $amount)
    {
        if ($amount <= 0) {
            throw new Exception('Invalid swap amount.');
        }

        if ($fromSymbol === $toSymbol) {
            throw new Exception('Source and destination assets cannot be the same.');
        }

        // Validate fee from SystemCoin
        $feePercentage = 0.005; // Default 0.5%
        if ($fromSymbol !== 'USD') {
            $sysCoin = SystemCoin::where('symbol', $fromSymbol)->first();
            if ($sysCoin) {
                $feePercentage = $sysCoin->fee_percentage / 100;
            }
        } elseif ($toSymbol !== 'USD') {
            $sysCoin = SystemCoin::where('symbol', $toSymbol)->first();
            if ($sysCoin) {
                $feePercentage = $sysCoin->fee_percentage / 100;
            }
        }

        // Get Price Rate (from BinancePriceService)
        $rate = 1.0;

        if ($fromSymbol === 'USD' && $toSymbol !== 'USD') {
            $priceData = BinancePriceService::getSpotExchangeInfo(); // Actually we need price
            // Wait, we need the exact price. We can use the cached fetchAll or direct get.
            $prices = BinancePriceService::getPriceMap();
            $pair = strtoupper($toSymbol).'USDT';
            if (! isset($prices[$pair])) {
                throw new Exception("Unable to resolve exchange rate for $toSymbol.");
            }
            $coinPrice = $prices[$pair];
            // If I am swapping USD to BTC, rate is 1 / BTC_Price
            $rate = 1 / $coinPrice;
        } elseif ($fromSymbol !== 'USD' && $toSymbol === 'USD') {
            $prices = BinancePriceService::getPriceMap();
            $pair = strtoupper($fromSymbol).'USDT';
            if (! isset($prices[$pair])) {
                throw new Exception("Unable to resolve exchange rate for $fromSymbol.");
            }
            $rate = $prices[$pair];
        } else {
            // Crypto to Crypto
            $prices = BinancePriceService::getPriceMap();
            $pairFrom = strtoupper($fromSymbol).'USDT';
            $pairTo = strtoupper($toSymbol).'USDT';
            if (! isset($prices[$pairFrom]) || ! isset($prices[$pairTo])) {
                throw new Exception('Unable to resolve exchange rates for crypto pair.');
            }
            $fromUsdValue = $prices[$pairFrom];
            $toUsdValue = $prices[$pairTo];

            $rate = $fromUsdValue / $toUsdValue;
        }

        $grossToAmount = $amount * $rate;
        $feeAmount = $grossToAmount * $feePercentage;
        $netToAmount = $grossToAmount - $feeAmount;

        DB::beginTransaction();
        try {
            // Deduct 'from'
            if ($fromSymbol === 'USD') {
                $senderBalance = Balance::where('user_id', $userId)->first();
                if (! $senderBalance || $amount > $senderBalance->amount) {
                    throw new Exception('Insufficient USD balance.');
                }
                $senderBalance->decrement('amount', $amount);
            } else {
                $senderBalance = UserWallet::where('user_id', $userId)->where('coin_symbol', $fromSymbol)->first();
                if (! $senderBalance || $amount > $senderBalance->balance) {
                    throw new Exception("Insufficient $fromSymbol balance.");
                }
                $senderBalance->decrement('balance', $amount);
            }

            // Credit 'to'
            if ($toSymbol === 'USD') {
                $receiverBalance = Balance::where('user_id', $userId)->first();
                if ($receiverBalance) {
                    $receiverBalance->increment('amount', $netToAmount);
                } else {
                    Balance::create([
                        'user_id' => $userId,
                        'symbol' => 'USD',
                        'name' => 'USD',
                        'amount' => $netToAmount,
                        'demo' => 0,
                        'bitcoin' => 0,
                        'bonus' => 0,
                        'bonus_balance' => 0,
                        'referral' => 0,
                    ]);
                }
            } else {
                $receiverBalance = UserWallet::firstOrCreate(
                    ['user_id' => $userId, 'coin_symbol' => $toSymbol],
                    ['balance' => 0, 'is_enabled' => true]
                );
                $receiverBalance->increment('balance', $netToAmount);
            }

            // Record swap
            $swap = Swap::create([
                'user_id' => $userId,
                'from_currency' => $fromSymbol,
                'to_currency' => $toSymbol,
                'from_amount' => $amount,
                'to_amount' => $netToAmount,
                'exchange_rate' => $rate,
                'fee_amount' => $feeAmount,
                'status' => 'completed',
            ]);

            DB::commit();

            return $swap;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
