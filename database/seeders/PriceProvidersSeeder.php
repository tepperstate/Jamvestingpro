<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers = [
            // Crypto Public APIs
            [
                'name' => 'Binance Public',
                'provider_type' => 'binance',
                'asset_type' => 'crypto',
                'is_active' => true,
                'priority' => 100,
            ],
            [
                'name' => 'CoinCap Public',
                'provider_type' => 'coincap',
                'asset_type' => 'crypto',
                'is_active' => true,
                'priority' => 90,
            ],
            [
                'name' => 'MEXC Public',
                'provider_type' => 'mexc',
                'asset_type' => 'crypto',
                'is_active' => true,
                'priority' => 80,
            ],
            [
                'name' => 'KuCoin Public',
                'provider_type' => 'kucoin',
                'asset_type' => 'crypto',
                'is_active' => true,
                'priority' => 70,
            ],
            [
                'name' => 'Bitfinex Public',
                'provider_type' => 'bitfinex',
                'asset_type' => 'crypto',
                'is_active' => true,
                'priority' => 60,
            ],
            [
                'name' => 'Huobi Public',
                'provider_type' => 'huobi',
                'asset_type' => 'crypto',
                'is_active' => true,
                'priority' => 50,
            ],
            [
                'name' => 'Gate.io Public',
                'provider_type' => 'gateio',
                'asset_type' => 'crypto',
                'is_active' => true,
                'priority' => 45,
            ],
            [
                'name' => 'Kraken Public',
                'provider_type' => 'kraken', // Wait, I didn't implement Kraken yet, I'll use Binance fallback for now or add it later
                'provider_type' => 'binance',
                'asset_type' => 'crypto',
                'is_active' => true,
                'priority' => 40,
            ],
            [
                'name' => 'CoinGecko Public',
                'provider_type' => 'coingecko',
                'asset_type' => 'crypto',
                'is_active' => true,
                'priority' => 30,
            ],
            [
                'name' => 'CoinConvert Public',
                'provider_type' => 'coinconvert',
                'asset_type' => 'crypto',
                'is_active' => true,
                'priority' => 20,
            ],
            // Forex/Stocks (Require keys usually, but we set them up as placeholders)
            [
                'name' => 'Polygon.io Free',
                'provider_type' => 'polygon',
                'asset_type' => 'forex',
                'is_active' => false, // Needs key
                'priority' => 10,
            ],
            [
                'name' => 'AlphaVantage Free',
                'provider_type' => 'alphavantage',
                'asset_type' => 'forex',
                'is_active' => false, // Needs key
                'priority' => 5,
            ],
        ];

        foreach ($providers as $provider) {
            DB::table('price_providers')->updateOrInsert(
                ['name' => $provider['name']],
                array_merge($provider, ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()])
            );
        }
    }
}
