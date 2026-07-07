<?php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CryptoPriceService extends BasePriceService
{
    public function __construct()
    {
        $this->assetType = 'crypto';
    }

    protected function directFallback($symbol)
    {
        $cacheKey = 'price_service_fallback_'.str_replace('/', '_', $symbol);

        if (Cache::has($cacheKey)) {
            $cachedPrice = Cache::get($cacheKey);

            return ['price' => (float) $cachedPrice, 'percentage' => 0, 'source' => 'fallback_cache'];
        }

        try {
            $base = strtolower(explode('/', $symbol)[0]);
            $map = ['btc' => 'bitcoin', 'eth' => 'ethereum', 'ltc' => 'litecoin', 'xrp' => 'ripple', 'doge' => 'dogecoin', 'usdt' => 'tether', 'bnb' => 'binancecoin'];
            $id = $map[$base] ?? $base;
            $response = Http::timeout(5)->get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => $id,
                'vs_currencies' => 'usd',
            ]);
            if ($response->successful() && isset($response->json()[$id]['usd'])) {
                $p = (float) $response->json()[$id]['usd'];
                Cache::put($cacheKey, $p, 5);

                return ['price' => $p, 'source' => 'coingecko_fallback'];
            }
        } catch (\Exception $e) {
            Log::error("Direct fallback failed for {$symbol}: ".$e->getMessage());
        }

        return null;
    }

    protected function fetchFromProvider($provider, $symbol)
    {
        $res = null;
        switch ($provider->provider_type) {
            case 'binance': $res = $this->fetchBinance($provider, $symbol);
                break;
            case 'coingecko': $res = $this->fetchCoinGecko($provider, $symbol);
                break;
            case 'coincap': $res = $this->fetchCoinCap($provider, $symbol);
                break;
            case 'mexc': $res = $this->fetchMexc($provider, $symbol);
                break;
            case 'kucoin': $res = $this->fetchKucoin($provider, $symbol);
                break;
            case 'huobi': $res = $this->fetchHuobi($provider, $symbol);
                break;
            case 'bitfinex': $res = $this->fetchBitfinex($provider, $symbol);
                break;
            case 'gateio': $res = $this->fetchGateIo($provider, $symbol);
                break;
        }

        if ($res === null) {
            return null;
        }

        if (is_array($res)) {
            if (isset($res['price']) && $res['price'] > 0) {
                return $res;
            }

            return null;
        }

        if ((float) $res <= 0) {
            return null;
        }

        return [
            'price' => (float) $res,
            'percentage' => 0,
            'source' => $provider->name,
        ];
    }

    private function fetchBinance($provider, $symbol)
    {
        $s = strtoupper(str_replace(['/', 'USD'], ['', 'USDT'], $symbol));
        if (! str_contains($s, 'USDT')) {
            $s .= 'USDT';
        }

        $response = Http::timeout(5)->get('https://api.binance.com/api/v3/ticker/price', ['symbol' => $s]);
        if ($response->successful()) {
            return (float) $response->json('price');
        }

        return null;
    }

    private function fetchCoinGecko($provider, $symbol)
    {
        $map = [
            'BTC' => 'bitcoin', 'ETH' => 'ethereum', 'LTC' => 'litecoin',
            'XRP' => 'ripple', 'DOGE' => 'dogecoin', 'TRX' => 'tron',
            'BNB' => 'binancecoin', 'USDT' => 'tether', 'LINK' => 'chainlink',
            'DOT' => 'polkadot', 'ADA' => 'cardano', 'SOL' => 'solana',
        ];
        $base = strtoupper(explode('/', $symbol)[0]);
        $id = $map[$base] ?? strtolower($base);

        $response = Http::timeout(5)->get('https://api.coingecko.com/api/v3/simple/price', [
            'ids' => $id,
            'vs_currencies' => 'usd',
        ]);
        if ($response->successful()) {
            return (float) ($response->json()[$id]['usd'] ?? null);
        }

        return null;
    }

    private function fetchCoinCap($provider, $symbol)
    {
        $base = strtolower(explode('/', $symbol)[0]);
        $response = Http::timeout(5)->get("https://api.coincap.io/v2/assets/{$base}");
        if ($response->successful()) {
            return (float) $response->json('data.priceUsd');
        }

        return null;
    }

    private function fetchMexc($provider, $symbol)
    {
        $s = str_replace('/', '', $symbol);
        if (! str_contains($s, 'USDT')) {
            $s .= 'USDT';
        }
        $response = Http::timeout(5)->get('https://api.mexc.com/api/v3/ticker/price', ['symbol' => $s]);
        if ($response->successful()) {
            return (float) $response->json('price');
        }

        return null;
    }

    private function fetchKucoin($provider, $symbol)
    {
        $s = str_replace('/', '-', $symbol);
        if (! str_contains($s, 'USDT')) {
            $s .= '-USDT';
        }
        $response = Http::timeout(5)->get('https://api.kucoin.com/api/v1/market/orderbook/level1', ['symbol' => $s]);
        if ($response->successful()) {
            return (float) $response->json('data.price');
        }

        return null;
    }

    private function fetchHuobi($provider, $symbol)
    {
        $s = strtolower(str_replace('/', '', $symbol));
        if (! str_contains($s, 'usdt')) {
            $s .= 'usdt';
        }
        $response = Http::timeout(5)->get('https://api.huobi.pro/market/detail/merged', ['symbol' => $s]);
        if ($response->successful()) {
            return (float) $response->json('tick.close');
        }

        return null;
    }

    private function fetchBitfinex($provider, $symbol)
    {
        $s = str_replace('/', '', $symbol);
        if ($s == 'BTCUSD') {
            $s = 'tBTCUSD';
        } // Bitfinex format
        else {
            $s = 't'.$s.'UST';
        }
        $response = Http::timeout(5)->get("https://api-pub.bitfinex.com/v2/ticker/{$s}");
        if ($response->successful()) {
            return (float) $response->json()[6];
        } // Last price is at index 6

        return null;
    }

    private function fetchGateIo($provider, $symbol)
    {
        $s = strtolower(str_replace('/', '_', $symbol));
        if (! str_contains($s, '_') && ! str_contains($s, 'usdt')) {
            $s .= '_usdt';
        }
        $response = Http::timeout(5)->get("https://data.gateapi.io/api2/1/ticker/{$s}");
        if ($response->successful()) {
            return (float) $response->json('last');
        }

        return null;
    }
}
