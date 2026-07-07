<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    /**
     * Get the current price of a cryptocurrency in USD.
     * We use Binance's public API. It's free and requires no API keys for basic ticker prices.
     * 
     * @param string $symbol e.g. 'BTC', 'ETH', 'TRX'
     * @return float|null
     */
    public function getCryptoPriceInUsd(string $symbol): ?float
    {
        $symbol = strtoupper($symbol);
        
        // Handle special cases
        if ($symbol === 'USDT' || $symbol === 'USDC') {
            return 1.0;
        }

        // Cache the price for 5 minutes to avoid hitting rate limits
        return Cache::remember("crypto_price_{$symbol}_USD", 300, function () use ($symbol) {
            try {
                // Binance pair is like 'BTCUSDT'
                $pair = "{$symbol}USDT";
                
                $response = Http::timeout(5)->get("https://api.binance.com/api/v3/ticker/price", [
                    'symbol' => $pair
                ]);

                if ($response->successful()) {
                    return (float) $response->json('price');
                }

                Log::warning("Failed to fetch price for {$pair} from Binance. Response: " . $response->body());
                
            } catch (\Exception $e) {
                Log::error("Exception fetching price for {$symbol}: " . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Convert a USD amount to a crypto amount.
     * 
     * @param float $usdAmount
     * @param string $cryptoSymbol
     * @return float|null
     */
    public function convertUsdToCrypto(float $usdAmount, string $cryptoSymbol): ?float
    {
        $price = $this->getCryptoPriceInUsd($cryptoSymbol);
        
        if (!$price || $price <= 0) {
            return null;
        }

        return $usdAmount / $price;
    }
}
