<?php

namespace App\Services\MarketData;

use App\Models\PriceProvider;
use App\Models\Site_setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

abstract class BasePriceService
{
    protected $assetType;

    abstract protected function directFallback($symbol);

    abstract protected function fetchFromProvider($provider, $symbol);

    public function getPrice($symbol, $isInvertedCall = false)
    {
        $symbol = strtoupper(str_replace('-', '/', $symbol));
        $cacheKey = 'price_service_'.str_replace('/', '_', $symbol);

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);

            return [
                'price' => (float) $cached['price'],
                'source' => $cached['source'],
                'spread' => (float) ($cached['spread'] ?? 0),
                'percentage' => (float) ($cached['percentage'] ?? 0),
                'open' => (float) ($cached['open'] ?? 0),
                'high' => (float) ($cached['high'] ?? 0),
                'low' => (float) ($cached['low'] ?? 0),
                'volume' => $cached['volume'] ?? '0',
            ];
        }

        $settings = Site_setting::first();
        $useRoundRobin = $settings->use_round_robin ?? true;

        $priceData = $this->tryCategory($symbol, 'public', $useRoundRobin);
        if ($priceData !== null) {
            return $priceData;
        }

        $priceData = $this->tryCategory($symbol, 'premium', $useRoundRobin);
        if ($priceData !== null) {
            return $priceData;
        }

        if (! $isInvertedCall && str_contains($symbol, '/')) {
            $parts = explode('/', $symbol);
            if (count($parts) === 2) {
                $invertedSymbol = $parts[1].'/'.$parts[0];
                $invertedPriceData = $this->getPrice($invertedSymbol, true);
                if ($invertedPriceData && isset($invertedPriceData['price']) && $invertedPriceData['price'] > 0 && $invertedPriceData['source'] !== 'mock') {
                    $p = 1 / $invertedPriceData['price'];

                    return [
                        'price' => (float) $p,
                        'source' => $invertedPriceData['source'].' (Inverted)',
                        'spread' => (float) ($invertedPriceData['spread'] ?? 0),
                        'percentage' => (float) ($invertedPriceData['percentage'] ?? 0),
                    ];
                }
            }
        }

        $price = $this->directFallback($symbol);
        if ($price !== null) {
            return $price;
        }

        return $this->getMockPrice($symbol);
    }

    protected function tryCategory($symbol, $category, $useRoundRobin)
    {
        $cacheKey = 'price_service_'.str_replace('/', '_', $symbol);
        $query = PriceProvider::where('is_active', true)
            ->where('asset_type', $this->assetType)
            ->where('category', $category);

        if ($useRoundRobin) {
            $count = $query->count();
            if ($count > 0) {
                $indexKey = "provider_index_{$category}_{$this->assetType}";
                $currentIndex = Cache::get($indexKey, 0);
                $providerList = $query->orderBy('id', 'asc')->get();

                for ($i = 0; $i < $count; $i++) {
                    $idx = ($currentIndex + $i) % $count;
                    $provider = $providerList[$idx];

                    if (Cache::has("provider_cooldown_{$provider->id}")) {
                        continue;
                    }

                    $priceData = $this->attemptFetch($provider, $symbol, $cacheKey);
                    if ($priceData !== null) {
                        Cache::put($indexKey, ($idx + 1) % $count, 3600);

                        return $priceData;
                    }
                }
            }
        } else {
            $providers = $query->orderBy('last_used_at', 'asc')
                ->orderBy('priority', 'desc')
                ->get();

            foreach ($providers as $provider) {
                if (Cache::has("provider_cooldown_{$provider->id}")) {
                    continue;
                }

                $priceData = $this->attemptFetch($provider, $symbol, $cacheKey);
                if ($priceData !== null) {
                    return $priceData;
                }
            }
        }

        return null;
    }

    protected function attemptFetch($provider, $symbol, $cacheKey)
    {
        try {
            $data = $this->fetchFromProvider($provider, $symbol);
            if ($data !== null) {
                if (! is_array($data)) {
                    $data = [
                        'price' => (float) $data,
                        'percentage' => 0,
                        'source' => $provider->name,
                    ];
                }

                $data['spread'] = (float) ($provider->spread_percentage ?? 0);

                $this->updateProviderStatus($provider, 'success');
                Cache::put($cacheKey, $data, 5);

                return $data;
            }
        } catch (\Throwable $e) {
            $this->updateProviderStatus($provider, 'error', $e->getMessage());
            Log::error("MarketDataGateway error for {$provider->name} on {$symbol}: ".$e->getMessage());
        }

        return null;
    }

    protected function updateProviderStatus($provider, $status, $error = null)
    {
        if ($status === 'error' && (str_contains(strtolower((string) $error), '429') || str_contains(strtolower((string) $error), 'too many requests') || str_contains(strtolower((string) $error), 'rate limit'))) {
            Log::warning("Provider {$provider->name} hit rate limit. Cooling down for 15 minutes.");
            Cache::put("provider_cooldown_{$provider->id}", true, 900);
        }

        $provider->update([
            'last_used_at' => Carbon::now(),
            'last_status' => $status,
            'last_error' => $error,
        ]);
    }

    protected function getMockPrice($symbol)
    {
        if (request()->header('host') == 'localhost' || str_contains(request()->header('host'), '127.0.0.1')) {
            if (str_contains($symbol, 'BTC')) {
                return ['price' => 67890.12, 'percentage' => 1.2, 'source' => 'mock', 'spread' => 0.1];
            }
            if (str_contains($symbol, 'ETH')) {
                return ['price' => 3456.78, 'percentage' => -0.5, 'source' => 'mock', 'spread' => 0.1];
            }

            return ['price' => 1.08, 'percentage' => 0.1, 'source' => 'mock', 'spread' => 0.1];
        }

        return null;
    }
}
