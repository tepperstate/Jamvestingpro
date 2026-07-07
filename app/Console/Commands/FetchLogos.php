<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\Stock_Trade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FetchLogos extends Command
{
    protected $signature = 'assets:fetch-logos {--symbol= : Specific symbol to sync} {--force : Force re-download of existing logos}';

    protected $description = 'Surgical & Mass Asset Logo Synchronization Agent';

    public function handle()
    {
        $symbol = $this->option('symbol');
        $force = $this->option('force');

        if ($symbol) {
            $assets = Asset::where('symbols', 'LIKE', "%$symbol%")->get();
            $stockTrades = Stock_Trade::where('symbol', 'LIKE', "%$symbol%")->get();
        } else {
            $assets = Asset::all();
            $stockTrades = Stock_Trade::all();
        }

        $this->info('Processing '.$assets->count().' global assets...');
        foreach ($assets as $asset) {
            $this->processAsset($asset, 'asset', $force);
        }

        $this->info('Processing '.$stockTrades->count().' trade entities...');
        foreach ($stockTrades as $stock) {
            $this->processAsset($stock, 'stocks', $force);
        }

        $this->info('Institutional Logo Synchronization sequence complete.');
    }

    private function processAsset($model, $type, $force = false)
    {
        $symbol = ($type === 'asset') ? $model->symbols : $model->symbol;
        if (! $symbol) {
            return;
        }

        // Skip non-tradable or garbage tickers
        if (strlen($symbol) > 12 && ! str_contains($symbol, '.')) {
            return;
        }

        $cacheSymbol = strtoupper(str_replace(['/', '.'], '_', $symbol));
        $filename = "logo_{$cacheSymbol}.png";
        $path = 'image/'.$filename;

        // Check if we already have a valid logo
        if (! $force && Storage::disk('public')->exists($path)) {
            if (Storage::disk('public')->size($path) > 500) {
                // Already has a valid logo, update the database field if missing
                $field = ($type === 'asset') ? 'image1' : 'image';
                if (! $model->$field) {
                    $model->update([$field => $filename]);
                }

                return;
            }
        }

        $this->info("Syncing Identity: $symbol...");

        if ($type === 'asset' && $model->exchanges_id == 1) {
            $this->fetchForexFlags($model, $filename);
        } else {
            $this->fetchBrandIdentity($model, $type, $filename);
        }
    }

    private function fetchForexFlags($asset, $filename)
    {
        $parts = explode('/', strtoupper($asset->symbols));
        $base = strtolower(substr($parts[0], 0, 2));

        // Special case for EUR
        if (str_contains($parts[0], 'EUR')) {
            $base = 'eu';
        }

        $url = "https://flagcdn.com/w160/$base.png";
        $this->downloadAndSave($url, $asset, 'image1', $filename);
    }

    private function fetchBrandIdentity($model, $type, $filename)
    {
        $symbol = strtoupper(($type === 'asset') ? $model->symbols : $model->symbol);
        $cleanSymbol = explode('.', $symbol)[0];
        $baseAsset = explode('/', $cleanSymbol)[0];
        $field = ($type === 'asset') ? 'image1' : 'image';

        // 1. Specialized Finance CDNs (Highest Fidelity)
        $fallbacks = [
            "https://static2.seekingalpha.com/logos/{$cleanSymbol}.png",
            "https://cdn.tickerlogos.com/logos/{$cleanSymbol}.png",
            "https://static2.seekingalpha.com/logos/{$baseAsset}.png",
            'https://assets.coincap.io/assets/icons/'.strtolower($baseAsset).'@2x.png',
            'https://raw.githubusercontent.com/atomiclabs/cryptocurrency-icons/master/128/color/'.strtolower($baseAsset).'.png',
        ];

        // 2. Global Brand Domain Resolution
        $domains = [
            'AAPL' => 'apple.com', 'TSLA' => 'tesla.com', 'MSFT' => 'microsoft.com',
            'AMZN' => 'amazon.com', 'GOOGL' => 'google.com', 'META' => 'meta.com',
            'NVDA' => 'nvidia.com', 'NFLX' => 'netflix.com',
        ];
        $domain = $domains[$cleanSymbol] ?? (strtolower($cleanSymbol).'.com');
        $fallbacks[] = "https://logo.clearbit.com/{$domain}";
        $fallbacks[] = "https://www.google.com/s2/favicons?domain={$domain}&sz=128";

        foreach ($fallbacks as $url) {
            if ($this->downloadAndSave($url, $model, $field, $filename)) {
                return true;
            }
        }

        return false;
    }

    private function downloadAndSave($url, $model, $field, $filename)
    {
        try {
            $response = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);

            if ($response->successful() && strlen($response->body()) > 200) {
                Storage::disk('public')->put('image/'.$filename, $response->body());
                $model->update([$field => $filename]);

                // Keep Asset and Stock_Trade tables in sync
                $symbol = ($field === 'image1') ? $model->symbols : $model->symbol;
                if ($field === 'image1') {
                    Stock_Trade::where('symbol', $symbol)->update(['image' => $filename]);
                } else {
                    Asset::where('symbols', $symbol)->update(['image1' => $filename]);
                }

                $this->info("  [SUCCESS] $filename generated.");

                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
