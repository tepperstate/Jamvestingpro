<?php

namespace App\Services;

class EtfLogoService
{
    /**
     * Resolves an ETF logo URL based on the ticker symbol and name.
     */
    public static function getLogoUrl(?string $ticker, ?string $name = null): string
    {
        if (! $name) {
            $name = $ticker;
        }

        $providers = [
            'iShares' => 'ishares.com',
            'Fidelity' => 'fidelity.com',
            'Grayscale' => 'grayscale.com',
            'ARK' => 'ark-invest.com',
            'Bitwise' => 'bitwiseinvestments.com',
            'CoinShares' => 'coinshares.com',
            'VanEck' => 'vaneck.com',
            'Invesco' => 'invesco.com',
            'WisdomTree' => 'wisdomtree.com',
            'Franklin' => 'franklintempleton.com',
            'Vanguard' => 'vanguard.com',
            'Global X' => 'globalxetfs.com',
            'ProShares' => 'proshares.com',
            'Valkyrie' => 'valkyrieinvest.com',
            'Hashdex' => 'hashdex.com',
            'SPDR' => 'ssga.com',
        ];

        foreach ($providers as $provider => $domain) {
            if (stripos($name, $provider) !== false) {
                return "https://t3.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=http://{$domain}&size=128";
            }
        }

        if (! $ticker || trim($ticker) === '') {
            return AssetLogoService::getFallbackUrl('ETF');
        }

        $cleanTicker = strtoupper(trim($ticker));

        return "https://img.logokit.com/ticker/{$cleanTicker}";
    }

    /**
     * Fetch a list of known crypto ETFs to auto-populate.
     */
    public static function getKnownCryptoETFs(): array
    {
        $base = 'https://t3.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&size=128&url=http://';

        return [
            ['ticker' => 'IBIT', 'name' => 'iShares Bitcoin Trust', 'logo_url' => $base.'ishares.com'],
            ['ticker' => 'FBTC', 'name' => 'Fidelity Wise Origin Bitcoin Fund', 'logo_url' => $base.'fidelity.com'],
            ['ticker' => 'GBTC', 'name' => 'Grayscale Bitcoin Trust', 'logo_url' => $base.'grayscale.com'],
            ['ticker' => 'ARKB', 'name' => 'ARK 21Shares Bitcoin ETF', 'logo_url' => $base.'ark-invest.com'],
            ['ticker' => 'BITB', 'name' => 'Bitwise Bitcoin ETF', 'logo_url' => $base.'bitwiseinvestments.com'],
            ['ticker' => 'BRRR', 'name' => 'CoinShares Valkyrie Bitcoin Fund', 'logo_url' => $base.'coinshares.com'],
            ['ticker' => 'HODL', 'name' => 'VanEck Bitcoin ETF', 'logo_url' => $base.'vaneck.com'],
            ['ticker' => 'BTCO', 'name' => 'Invesco Galaxy Bitcoin ETF', 'logo_url' => $base.'invesco.com'],
            ['ticker' => 'EZBC', 'name' => 'Franklin Bitcoin ETF', 'logo_url' => $base.'franklintempleton.com'],
            ['ticker' => 'BTCW', 'name' => 'WisdomTree Bitcoin Fund', 'logo_url' => $base.'wisdomtree.com'],
            ['ticker' => 'DEFI', 'name' => 'Hashdex Bitcoin ETF', 'logo_url' => $base.'hashdex.com'],
            ['ticker' => 'ETHA', 'name' => 'iShares Ethereum Trust ETF', 'logo_url' => $base.'ishares.com'],
            ['ticker' => 'FETH', 'name' => 'Fidelity Ethereum Fund', 'logo_url' => $base.'fidelity.com'],
            ['ticker' => 'ETHV', 'name' => 'VanEck Ethereum ETF', 'logo_url' => $base.'vaneck.com'],
            ['ticker' => 'ETHE', 'name' => 'Grayscale Ethereum Trust', 'logo_url' => $base.'grayscale.com'],
            ['ticker' => 'ETHW', 'name' => 'Bitwise Ethereum ETF', 'logo_url' => $base.'bitwiseinvestments.com'],
            ['ticker' => 'CETH', 'name' => '21Shares Core Ethereum ETF', 'logo_url' => $base.'ark-invest.com'],
        ];
    }

    /**
     * Fetch a list of known stocks to auto-populate.
     */
    public static function getKnownStocks(): array
    {
        $base = 'https://t3.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&size=128&url=http://';

        return [
            ['ticker' => 'AAPL', 'name' => 'Apple Inc.', 'logo_url' => $base.'apple.com'],
            ['ticker' => 'MSFT', 'name' => 'Microsoft Corporation', 'logo_url' => $base.'microsoft.com'],
            ['ticker' => 'TSLA', 'name' => 'Tesla, Inc.', 'logo_url' => $base.'tesla.com'],
            ['ticker' => 'NVDA', 'name' => 'NVIDIA Corporation', 'logo_url' => $base.'nvidia.com'],
            ['ticker' => 'GOOGL', 'name' => 'Alphabet Inc.', 'logo_url' => $base.'abc.xyz'],
            ['ticker' => 'AMZN', 'name' => 'Amazon.com, Inc.', 'logo_url' => $base.'amazon.com'],
            ['ticker' => 'META', 'name' => 'Meta Platforms, Inc.', 'logo_url' => $base.'meta.com'],
            ['ticker' => 'NFLX', 'name' => 'Netflix, Inc.', 'logo_url' => $base.'netflix.com'],
            ['ticker' => 'AMD', 'name' => 'Advanced Micro Devices, Inc.', 'logo_url' => $base.'amd.com'],
            ['ticker' => 'INTC', 'name' => 'Intel Corporation', 'logo_url' => $base.'intel.com'],
            ['ticker' => 'JNJ', 'name' => 'Johnson & Johnson', 'logo_url' => $base.'jnj.com'],
            ['ticker' => 'V', 'name' => 'Visa Inc.', 'logo_url' => $base.'visa.com'],
            ['ticker' => 'JPM', 'name' => 'JPMorgan Chase & Co.', 'logo_url' => $base.'jpmorganchase.com'],
            ['ticker' => 'WMT', 'name' => 'Walmart Inc.', 'logo_url' => $base.'walmart.com'],
            ['ticker' => 'PG', 'name' => 'Procter & Gamble', 'logo_url' => $base.'pg.com'],
            ['ticker' => 'MA', 'name' => 'Mastercard', 'logo_url' => $base.'mastercard.com'],
            ['ticker' => 'HD', 'name' => 'The Home Depot, Inc.', 'logo_url' => $base.'homedepot.com'],
            ['ticker' => 'CVX', 'name' => 'Chevron Corporation', 'logo_url' => $base.'chevron.com'],
            ['ticker' => 'KO', 'name' => 'The Coca-Cola Company', 'logo_url' => $base.'coca-colacompany.com'],
            ['ticker' => 'PEP', 'name' => 'PepsiCo, Inc.', 'logo_url' => $base.'pepsico.com'],
        ];
    }

    /**
     * Fetch the live market price of an ETF using Yahoo Finance API (No API Key Required)
     */
    public static function getEtfPrice($ticker, $fallback = 100)
    {
        try {
            $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$ticker}?interval=1d";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);

            $result = curl_exec($ch);
            curl_close($ch);

            if ($result) {
                $data = json_decode($result, true);
                if (isset($data['chart']['result'][0]['meta']['regularMarketPrice'])) {
                    return round($data['chart']['result'][0]['meta']['regularMarketPrice'], 2);
                }
            }
        } catch (\Exception $e) {
            // Silently fail and return fallback
        }

        return $fallback;
    }
}
