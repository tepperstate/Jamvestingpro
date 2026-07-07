<?php

namespace App\Services\MarketData;

use App\Models\Site_setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StockPriceService extends BasePriceService
{
    public function __construct()
    {
        $this->assetType = 'stock';
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
            $s = str_replace('/', '', $symbol);

            $response = Http::timeout(5)->get('https://www.alphavantage.co/query', [
                'function' => 'GLOBAL_QUOTE',
                'symbol' => $s,
                'apikey' => $apiKey,
            ]);

            if ($response->successful() && $response->json('Global Quote.05. price')) {
                $quote = $response->json('Global Quote');
                $p = (float) $quote['05. price'];
                $perc = (float) str_replace('%', '', $quote['10. change percent'] ?? 0);
                $data = [
                    'price' => $p,
                    'percentage' => $perc,
                    'open' => (float) ($quote['02. open'] ?? 0),
                    'high' => (float) ($quote['03. high'] ?? 0),
                    'low' => (float) ($quote['04. low'] ?? 0),
                    'volume' => $quote['06. volume'] ?? '0',
                    'source' => 'alphavantage_fallback',
                ];
                Cache::put($cacheKey, $data, 5);

                return $data;
            }

            $polyKey = $settings->polygon_api_key ?? env('POLYGON_API_KEY');
            if ($polyKey) {
                $response = Http::timeout(5)->get("https://api.polygon.io/v2/last/trade/{$s}", ['apiKey' => $polyKey]);
                if ($response->successful() && $response->json('results.p')) {
                    $p = (float) $response->json('results.p');
                    Cache::put($cacheKey, $p, 5);

                    return ['price' => $p, 'percentage' => 0, 'source' => 'polygon_fallback'];
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
            case 'polygon': $res = $this->fetchPolygon($provider, $symbol);
                break;
            case 'alphavantage': $res = $this->fetchAlphaVantage($provider, $symbol);
                break;
            case 'finnhub': $res = $this->fetchFinnhub($provider, $symbol);
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

        $ticker = $s;

        $response = Http::timeout(5)->get("https://api.polygon.io/v2/last/trade/{$ticker}", ['apiKey' => $apiKey]);
        if ($response->successful() && $response->json('results.p')) {
            return (float) $response->json('results.p');
        }

        $response = Http::timeout(5)->get("https://api.polygon.io/v2/aggs/ticker/{$ticker}/prev", ['apiKey' => $apiKey, 'adjusted' => 'true']);
        if ($response->successful() && isset($response->json('results')[0]['c'])) {
            return (float) $response->json('results')[0]['c'];
        }

        return null;
    }

    private function fetchAlphaVantage($provider, $symbol)
    {
        $settings = Site_setting::first();
        $apiKey = $provider->api_key ?? ($settings->alphavantage_api_key ?? null);
        if (! $apiKey) {
            return null;
        }

        $s = str_replace('/', '', $symbol);

        $response = Http::timeout(5)->get('https://www.alphavantage.co/query', [
            'function' => 'GLOBAL_QUOTE',
            'symbol' => $s,
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

            $quote = $body['Global Quote'] ?? null;
            if (! $quote || ! isset($quote['05. price'])) {
                return null;
            }

            return [
                'price' => (float) $quote['05. price'],
                'percentage' => (float) str_replace('%', '', $quote['10. change percent'] ?? 0),
                'open' => (float) ($quote['02. open'] ?? 0),
                'high' => (float) ($quote['03. high'] ?? 0),
                'low' => (float) ($quote['04. low'] ?? 0),
                'volume' => $quote['06. volume'] ?? '0',
                'source' => 'AlphaVantage',
            ];
        }

        return null;
    }

    private function fetchFinnhub($provider, $symbol)
    {
        $settings = Site_setting::first();
        $apiKey = $provider->api_key ?? ($settings->finnhub_api_key ?? null);
        if (! $apiKey) {
            return null;
        }

        $s = str_replace('/', '', $symbol);

        $response = Http::timeout(5)->get('https://finnhub.io/api/v1/quote', [
            'symbol' => $s,
            'token' => $apiKey,
        ]);
        if ($response->successful()) {
            return (float) $response->json('c');
        }

        return null;
    }

    private function fetchYahooFinance($provider, $symbol)
    {
        $s = str_replace('/', '', $symbol);

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
