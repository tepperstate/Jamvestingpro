<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{
    /**
     * DEFINITIVE Production Asset Proxy & Logo Resolver
     * Ensures 100% resolution for core assets with multi-layer fallback logic.
     */
    public function resolve(Request $request, $symbol)
    {
        $symbol = strtoupper(trim($symbol));
        $cleanSymbol = explode('.', $symbol)[0];

        if ($cleanSymbol === 'META') {
            return redirect(asset('assets/img/meta_logo.png'));
        }

        $cacheSymbol = str_replace(['/', '.'], '_', $symbol);
        $cachePath = public_path("storage/image/logo_{$cacheSymbol}.png");

        $isRefresh = $request->has('refresh');

        if (! $isRefresh && file_exists($cachePath)) {
            $imageData = file_get_contents($cachePath);

            // Only serve if it's a valid image (not a tiny error placeholder)
            if (strlen($imageData) > 200) {
                $mimeType = mime_content_type($cachePath) ?: 'image/png';

                return response($imageData)
                    ->header('Content-Type', $mimeType)
                    ->header('Cache-Control', 'public, max-age=86400');
            }
        }

        $baseAsset = explode('/', $symbol)[0];

        // Handle currency pairs (e.g. BTCUSD -> BTC)
        if (! str_contains($symbol, '/') && strlen($symbol) > 4) {
            $suffixes = ['USDT', 'USD', 'ETH', 'BTC', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'NZD'];
            foreach ($suffixes as $suffix) {
                if (str_ends_with($symbol, $suffix) && strlen($symbol) > strlen($suffix)) {
                    $baseAsset = substr($symbol, 0, -strlen($suffix));
                    break;
                }
            }
        }

        $fallbacks = [];

        // --- LAYER 1: SPECIALIZED FINANCE CDNS ---

        // A. STOCKS & ETFs (Financial Modeling Prep - High Fidelity)
        // Only add if it's explicitly requested as a stock, or if it is not a known crypto base asset.
        $type = $request->get('type');
        if ($type === 'stock' || ($type !== 'crypto' && ! Asset::where('symbols', $cleanSymbol)->where('type', 'crypto')->exists())) {
            $fallbacks[] = "https://financialmodelingprep.com/image-stock/{$cleanSymbol}.png";
        }

        // B. CRYPTO (AtomicLabs + CoinCap + CryptoIcons API)
        if (strlen($baseAsset) >= 2 && strlen($baseAsset) <= 8) {
            $fallbacks[] = 'https://raw.githubusercontent.com/atomiclabs/cryptocurrency-icons/master/128/color/'.strtolower($baseAsset).'.png';
            $fallbacks[] = 'https://assets.coincap.io/assets/icons/'.strtolower($baseAsset).'@2x.png';
            $fallbacks[] = 'https://cryptoicons.org/api/color/'.strtolower($baseAsset).'/200';
            $fallbacks[] = 'https://api.coinmarketcap.com/static/img/coins/64x64/'.strtolower($baseAsset).'.png';
        }

        // C. FOREX (Flags)
        $currencyFlags = [
            'EUR' => 'eu', 'USD' => 'us', 'GBP' => 'gb', 'JPY' => 'jp',
            'CHF' => 'ch', 'CAD' => 'ca', 'AUD' => 'au', 'NZD' => 'nz',
            'CNY' => 'cn', 'INR' => 'in', 'SGD' => 'sg', 'ZAR' => 'za',
            'HKD' => 'hk', 'SEK' => 'se', 'NOK' => 'no', 'DKK' => 'dk',
        ];

        if (isset($currencyFlags[$baseAsset])) {
            $fallbacks[] = "https://flagcdn.com/w160/{$currencyFlags[$baseAsset]}.png";
        }

        // D. COMMODITIES & INDICES (Direct Links to reliable CDNs)
        $specialMappings = [
            'XAU' => 'https://raw.githubusercontent.com/spothq/cryptocurrency-icons/master/128/color/gold.png',
            'GOLD' => 'https://raw.githubusercontent.com/spothq/cryptocurrency-icons/master/128/color/gold.png',
            'XAG' => 'https://raw.githubusercontent.com/spothq/cryptocurrency-icons/master/128/color/meta.png', // Silver mapped to meta for color
            'OIL' => 'https://cdn-icons-png.flaticon.com/512/2964/2964514.png',
            'WTI' => 'https://cdn-icons-png.flaticon.com/512/2964/2964514.png',
            'SPX' => 'https://static2.seekingalpha.com/logos/SPY.png',
            'DJI' => 'https://static2.seekingalpha.com/logos/DIA.png',
            'IXIC' => 'https://static2.seekingalpha.com/logos/QQQ.png',
            'MSFT' => 'https://static2.seekingalpha.com/logos/MSFT.png',
            'BRK.B' => 'https://static2.seekingalpha.com/logos/BRK.B.png',
            'BRK.A' => 'https://static2.seekingalpha.com/logos/BRK.A.png',
            'TON' => 'https://assets.coincap.io/assets/icons/ton@2x.png',
            'MRLN' => 'https://ui-avatars.com/api/?name=Marlin&background=0ea5e9&color=fff&size=128&bold=true',
            'NVR' => 'https://ui-avatars.com/api/?name=NVR&background=1e293b&color=fff&size=128&bold=true',
            'RE' => 'https://ui-avatars.com/api/?name=EG&background=0f172a&color=fff&size=128&bold=true',
            'FCNCA' => 'https://ui-avatars.com/api/?name=FCNCA&background=1e3a8a&color=fff&size=128&bold=true',
            'RMS' => 'https://ui-avatars.com/api/?name=Hermes&background=f97316&color=fff&size=128&bold=true',
            'MELI' => 'https://ui-avatars.com/api/?name=MELI&background=eab308&color=1e293b&size=128&bold=true',
            'MC' => 'https://ui-avatars.com/api/?name=LVMH&background=000&color=fff&size=128&bold=true',
        ];

        if (isset($specialMappings[$cleanSymbol])) {
            $fallbacks[] = $specialMappings[$cleanSymbol];
        }

        // Iterate through fallbacks, fetch, verify, and cache the first successful one
        foreach ($fallbacks as $url) {
            try {
                $response = Http::timeout(5)->get($url);
                if ($response->successful()) {
                    $imageData = $response->body();

                    // Basic check to ensure it's a real image and not a tiny 1px spacer or error
                    if (strlen($imageData) > 200) {
                        try {
                            if (! is_dir(dirname($cachePath))) {
                                mkdir(dirname($cachePath), 0755, true);
                            }
                            file_put_contents($cachePath, $imageData);
                        } catch (\Exception $cacheEx) {
                            // Don't fail the request if caching fails
                        }

                        $mimeType = $response->header('Content-Type') ?: 'image/png';

                        return response($imageData)
                            ->header('Content-Type', $mimeType)
                            ->header('Cache-Control', 'public, max-age=86400');
                    }
                }
            } catch (\Exception $e) {
                // Ignore and try the next fallback
                continue;
            }
        }

        // FINAL FALLBACK: Dynamic UI Avatar
        $cleanSym = substr(preg_replace('/[^a-zA-Z0-9]/', '', $cleanSymbol), 0, 3) ?: 'S';
        $fallbackUrl = 'https://ui-avatars.com/api/?name='.urlencode(strtoupper($cleanSym)).'&background=0ea5e9&color=fff&bold=true';

        return redirect($fallbackUrl);
    }
}
