<?php

namespace App\Services;

class AssetLogoService
{
    /**
     * Resolves a logo URL for a given asset symbol.
     *
     * Returns RELATIVE URLs so they work regardless of APP_URL configuration.
     *
     * Strategy (in order):
     * 1. If a locally cached file exists at public/storage/image/logo_{SYMBOL}.png,
     *    return a relative path (served directly by Apache as a static file).
     * 2. If the asset has an existing full-URL image in the DB, use it.
     * 3. For crypto: return a direct CDN URL to atomiclabs/cryptocurrency-icons.
     * 4. For stocks: return a direct CDN URL to financialmodelingprep.
     * 5. Fallback: neutral SVG icon.
     */
    /**
     * Generates a fallback avatar image using ui-avatars.com based on the symbol name.
     */
    public static function getFallbackUrl(?string $symbol, ?string $bgColor = '0ea5e9'): string
    {
        if (! $symbol || trim($symbol) === '') {
            return '/assets/img/profit.svg';
        }
        $clean = strtoupper(trim(preg_replace('/[^a-zA-Z0-9]/', '', $symbol)));
        if (empty($clean)) {
            return '/assets/img/profit.svg';
        }

        return 'https://ui-avatars.com/api/?name='.urlencode(substr($clean, 0, 3))."&background={$bgColor}&color=fff&bold=true";
    }

    public static function getLogoUrl(?string $symbol, ?string $assetType = 'crypto', ?string $existingImage = null): string
    {
        if (! $symbol || trim($symbol) === '') {
            return self::getFallbackUrl($symbol);
        }

        $upperSymbol = strtoupper(trim($symbol));
        $cacheKey = str_replace(['/', '.'], '_', $upperSymbol);

        // 1. Check for locally cached file — ALWAYS prefer this
        //    Apache serves public/ as the docroot, so /storage/image/... is correct.
        $cachedFile = public_path("storage/image/logo_{$cacheKey}.png");
        if (file_exists($cachedFile) && filesize($cachedFile) > 200) {
            return "/storage/image/logo_{$cacheKey}.png";
        }

        // 2. DB image — only if it's a real full URL (not a seeder placeholder like "btcusdt.png")
        if ($existingImage && $existingImage !== '') {
            if (filter_var($existingImage, FILTER_VALIDATE_URL)) {
                return $existingImage;
            }
            // Non-URL values like "btcusdt.png" are seeder artifacts — skip them
        }

        // Auto-detect crypto to fix misclassified portfolio assets
        $cryptoSuffixes = ['USDT', 'BUSD', 'USDC', 'DAI', 'TUSD', 'FDUSD'];
        $isCrypto = false;
        foreach ($cryptoSuffixes as $suffix) {
            if (str_ends_with($upperSymbol, $suffix) && strlen($upperSymbol) > strlen($suffix)) {
                $isCrypto = true;
                break;
            }
        }

        $knownCrypto = [
            'BTC', 'ETH', 'BNB', 'SOL', 'ADA', 'DOGE', 'XRP', 'DOT', 'AVAX', 'MATIC',
            'LINK', 'UNI', 'ATOM', 'NEAR', 'FTM', 'ALGO', 'XLM', 'VET', 'ICP', 'FIL',
            'SHIB', 'PEPE', 'LTC', 'BCH', 'ETC', 'TRX',
        ];

        if ($isCrypto || in_array($upperSymbol, $knownCrypto)) {
            $assetType = 'crypto';
        } elseif (!in_array($assetType, ['crypto', 'stock', 'forex'])) {
            $assetType = 'stock';
        }

        // 3. Extract base symbol for CDN lookups
        $baseAsset = $upperSymbol;
        $suffixes = ['USDT', 'BUSD', 'USDC', 'USD', 'ETH', 'BTC', 'EUR', 'GBP', 'JPY'];
        foreach ($suffixes as $suffix) {
            if (str_ends_with($upperSymbol, $suffix) && strlen($upperSymbol) > strlen($suffix)) {
                $baseAsset = substr($upperSymbol, 0, -strlen($suffix));
                break;
            }
        }
        $baseAsset = rtrim($baseAsset, '/');

        // For crypto: direct CDN URL to atomiclabs/cryptocurrency-icons
        if ($assetType === 'crypto') {
            return 'https://cdn.jsdelivr.net/gh/atomiclabs/cryptocurrency-icons@1a63530be6e374711a8554f31b17e4cb92c25fa5/svg/color/'.strtolower($baseAsset).'.svg';
        }

        // For forex: try flag
        $currencyFlags = [
            'EUR' => 'eu', 'USD' => 'us', 'GBP' => 'gb', 'JPY' => 'jp',
            'CHF' => 'ch', 'CAD' => 'ca', 'AUD' => 'au', 'NZD' => 'nz',
        ];
        if (isset($currencyFlags[$baseAsset])) {
            return "https://flagcdn.com/w160/{$currencyFlags[$baseAsset]}.png";
        }

        // For stocks: direct CDN URL
        if ($assetType === 'stock') {
            return "https://financialmodelingprep.com/image-stock/{$upperSymbol}.png";
        }

        // Fallback: ui-avatars fallback instead of profit.svg
        return self::getFallbackUrl($symbol);
    }
}
