<?php

namespace App\Services\MarketData;

use App\Models\Site_setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ForexPriceService extends BasePriceService
{
    public function __construct()
    {
        $this->assetType = 'forex';
    }

    protected function directFallback($symbol)
    {
        $cacheKey = 'price_service_fallback_'.str_replace('/', '_', $symbol);

        if (Cache::has($cacheKey)) {
            $cachedPrice = Cache::get($cacheKey);

            return ['price' => (float) $cachedPrice, 'percentage' => 0, 'source' => 'fallback_cache'];
        }

        try {
            $settings = Site_setting::first();
            $apiKey = $settings->alphavantage_api_key ?? config('services.alphavantage.key', '');

            $parts = explode('/', $symbol);
            $from = $parts[0];
            $to = $parts[1] ?? 'USD';

            $response = Http::timeout(5)->get('https://www.alphavantage.co/query', [
                'function' => 'CURRENCY_EXCHANGE_RATE',
                'from_currency' => $from,
                'to_currency' => $to,
                'apikey' => $apiKey,
            ]);

            if ($response->successful()) {
                $rate = $response->json('Realtime Currency Exchange Rate.5. Exchange Rate');
                if ($rate) {
                    $p = (float) $rate;
                    Cache::put($cacheKey, $p, 5);

                    return ['price' => $p, 'percentage' => 0, 'source' => 'alphavantage_fallback'];
                }
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
            case 'alphavantage': $res = $this->fetchAlphaVantage($provider, $symbol);
                break;
            case 'coinconvert': $res = $this->fetchCoinConvert($provider, $symbol);
                break;
            case 'polygon': $res = $this->fetchPolygon($provider, $symbol);
                break;
            case 'yahoo': $res = $this->fetchYahooFinance($provider, $symbol);
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

    private function fetchAlphaVantage($provider, $symbol)
    {
        $settings = Site_setting::first();
        $apiKey = $provider->api_key ?? ($settings->alphavantage_api_key ?? null);
        if (! $apiKey) {
            return null;
        }

        $parts = explode('/', $symbol);
        if (count($parts) === 2) {
            $from = $parts[0];
            $to = $parts[1];
        } elseif (strlen($symbol) === 6) {
            $from = substr($symbol, 0, 3);
            $to = substr($symbol, 3, 3);
        } else {
            $from = $symbol;
            $to = 'USD';
        }

        $response = Http::timeout(5)->get('https://www.alphavantage.co/query', [
            'function' => 'CURRENCY_EXCHANGE_RATE',
            'from_currency' => $from,
            'to_currency' => $to,
            'apikey' => $apiKey,
        ]);

        if ($response->successful()) {
            $body = $response->json();
            if (isset($body['Information']) || isset($body['Note'])) {
                $msg = $body['Information'] ?? $body['Note'];
                Log::warning('AlphaVantage rate limit hit: '.$msg);
                $this->updateProviderStatus($provider, 'error', 'Rate limit hit: '.$msg);

                return null;
            }

            Log::info("AlphaVantage CURRENCY_EXCHANGE_RATE for {$from}/{$to}: ".$response->body());
            $rate = $response->json('Realtime Currency Exchange Rate.5. Exchange Rate');

            return $rate ? (float) $rate : null;
        }

        return null;
    }

    private function fetchCoinConvert($provider, $symbol)
    {
        $parts = explode('/', $symbol);
        $from = $parts[0];
        $to = strtolower($parts[1] ?? 'USD');
        $response = Http::timeout(5)->get("https://api.coinconvert.net/convert/{$from}/{$to}", ['amount' => 1]);
        if ($response->successful()) {
            return (float) $response->json($to);
        }

        return null;
    }

    private function fetchPolygon($provider, $symbol)
    {
        $settings = Site_setting::first();
        $keys = $provider->api_key ?? ($settings->polygon_api_key ?? env('POLYGON_API_KEY'));
        if (! $keys) {
            return null;
        }

        $keyList = array_filter(explode(',', $keys));
        if (empty($keyList)) {
            return null;
        }

        $apiKey = $keyList[intval(date('i')) % count($keyList)];
        $s = str_replace('/', '', $symbol);

        $ticker = 'C:'.$s;

        $response = Http::timeout(5)->get("https://api.polygon.io/v2/last/nbbo/{$ticker}", ['apiKey' => $apiKey]);
        if ($response->successful() && $response->json('results.p')) {
            return (float) $response->json('results.p');
        }

        $response = Http::timeout(5)->get("https://api.polygon.io/v2/aggs/ticker/{$ticker}/prev", ['apiKey' => $apiKey, 'adjusted' => 'true']);
        if ($response->successful() && isset($response->json('results')[0]['c'])) {
            return (float) $response->json('results')[0]['c'];
        }

        return null;
    }

    private function fetchYahooFinance($provider, $symbol)
    {
        $s = str_replace('/', '', $symbol);
        if (! str_contains($s, '=X')) {
            $s .= '=X';
        }

        $response = Http::timeout(5)->withHeaders([
            'User-Agent' => 'Mozilla/5.0',
        ])->get("https://query1.finance.yahoo.com/v8/finance/chart/{$s}");

        if ($response->successful()) {
            $result = $response->json('chart.result.0');
            if (! $result || ! isset($result['meta'])) {
                return null;
            }

            $meta = $result['meta'];

            return [
                'price' => (float) ($meta['regularMarketPrice'] ?? 0),
                'percentage' => (float) ($meta['regularMarketChangePercent'] ?? 0),
                'open' => (float) ($meta['regularMarketOpen'] ?? 0),
                'high' => (float) ($meta['regularMarketHigh'] ?? 0),
                'low' => (float) ($meta['regularMarketLow'] ?? 0),
                'volume' => $meta['regularMarketVolume'] ?? '0',
                'source' => 'Yahoo Finance',
            ];
        }

        return null;
    }
}
