<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Centralized Binance market data fetcher.
 * Uses data-api.binance.vision (confirmed accessible) with caching and retry.
 */
class BinancePriceService
{
    private const BASE_URL = 'https://data-api.binance.vision/api/v3';

    private const CACHE_TTL = 30; // seconds

    private const CONNECT_TIMEOUT = 15;

    private const TRANSFER_TIMEOUT = 300;

    private const RETRIES = 2;

    private const RETRY_DELAY = 1000; // ms

    /**
     * Get a map of symbol => price for ALL Binance pairs.
     * Cached for 30 seconds.
     */
    public static function getPriceMap(): ?array
    {
        return Cache::remember('binance_ticker_prices', self::CACHE_TTL, function () {
            try {
                $resp = Http::connectTimeout(self::CONNECT_TIMEOUT)
                    ->timeout(self::TRANSFER_TIMEOUT)
                    ->retry(self::RETRIES, self::RETRY_DELAY)
                    ->get(self::BASE_URL.'/ticker/price');

                if ($resp->successful()) {
                    $map = [];
                    foreach ($resp->json() as $item) {
                        $map[$item['symbol']] = (float) $item['price'];
                    }

                    return $map;
                }
            } catch (\Exception $e) {
                Log::warning('BinancePriceService: price fetch failed: '.$e->getMessage());
            }

            return null;
        });
    }

    /**
     * Get a map of symbol => 24h change percentage for ALL Binance pairs.
     * Cached for 30 seconds.
     */
    public static function getChangeMap(): ?array
    {
        return Cache::remember('binance_ticker_24hr', self::CACHE_TTL, function () {
            try {
                $resp = Http::connectTimeout(self::CONNECT_TIMEOUT)
                    ->timeout(self::TRANSFER_TIMEOUT)
                    ->retry(self::RETRIES, self::RETRY_DELAY)
                    ->get(self::BASE_URL.'/ticker/24hr');

                if ($resp->successful()) {
                    $map = [];
                    foreach ($resp->json() as $item) {
                        $map[$item['symbol']] = (float) $item['priceChangePercent'];
                    }

                    return $map;
                }
            } catch (\Exception $e) {
                Log::warning('BinancePriceService: 24hr fetch failed: '.$e->getMessage());
            }

            return null;
        });
    }

    /**
     * Convenience: get both price and change maps in one call.
     *
     * @return array{priceMap: ?array, changeMap: ?array}
     */
    public static function fetchAll(): array
    {
        return [
            'priceMap' => self::getPriceMap(),
            'changeMap' => self::getChangeMap(),
        ];
    }

    /**
     * Get the full 24hr ticker data for all pairs.
     * Includes volume, quoteVolume, highPrice, lowPrice, etc.
     */
    public static function get24hrTickerData(): ?array
    {
        return Cache::remember('binance_ticker_24hr_full', self::CACHE_TTL, function () {
            try {
                $resp = Http::connectTimeout(self::CONNECT_TIMEOUT)
                    ->timeout(self::TRANSFER_TIMEOUT)
                    ->retry(self::RETRIES, self::RETRY_DELAY)
                    ->get(self::BASE_URL.'/ticker/24hr');

                if ($resp->successful()) {
                    $map = [];
                    foreach ($resp->json() as $item) {
                        $map[$item['symbol']] = $item;
                    }

                    return $map;
                }
            } catch (\Exception $e) {
                Log::warning('BinancePriceService: 24hr full fetch failed: '.$e->getMessage());
            }

            return null;
        });
    }

    /**
     * Fetch Exchange Info (Spot)
     */
    public static function getSpotExchangeInfo(): ?array
    {
        return Cache::remember('binance_spot_exchange_info', 3600, function () {
            try {
                $resp = Http::connectTimeout(self::CONNECT_TIMEOUT)
                    ->timeout(self::TRANSFER_TIMEOUT)
                    ->retry(self::RETRIES, self::RETRY_DELAY)
                    ->get(self::BASE_URL.'/exchangeInfo');

                if ($resp->successful()) {
                    return $resp->json();
                }
            } catch (\Exception $e) {
                Log::warning('BinancePriceService: spot exchange info fetch failed: '.$e->getMessage());
            }

            return null;
        });
    }

    /**
     * Fetch Exchange Info (Futures)
     * Uses fapi endpoint which might need a different base url but data-api.binance.vision supports spot.
     * Actually fapi.binance.com is the standard for futures.
     */
    public static function getFuturesExchangeInfo(): ?array
    {
        return Cache::remember('binance_futures_exchange_info', 3600, function () {
            try {
                $resp = Http::connectTimeout(self::CONNECT_TIMEOUT)
                    ->timeout(self::TRANSFER_TIMEOUT)
                    ->get('https://fapi.binance.com/fapi/v1/exchangeInfo');

                if ($resp->successful()) {
                    return $resp->json();
                } elseif ($resp->status() === 451) {
                    // Fallback to testnet if blocked by region (e.g., US)
                    $resp = Http::connectTimeout(self::CONNECT_TIMEOUT)
                        ->timeout(self::TRANSFER_TIMEOUT)
                        ->get('https://testnet.binancefuture.com/fapi/v1/exchangeInfo');

                    if ($resp->successful()) {
                        return $resp->json();
                    }
                }
            } catch (\Exception $e) {
                // If it fails, fallback to Spot Exchange Info
            }

            // Ultimate fallback to Spot API if Futures APIs are blocked
            try {
                $resp = Http::connectTimeout(self::CONNECT_TIMEOUT)
                    ->timeout(self::TRANSFER_TIMEOUT)
                    ->get(self::BASE_URL.'/exchangeInfo');
                if ($resp->successful()) {
                    return $resp->json();
                }
            } catch (\Exception $ex) {
                Log::warning('BinancePriceService: futures and spot fallback fetch failed: '.$ex->getMessage());
            }

            return null;
        });
    }
}
