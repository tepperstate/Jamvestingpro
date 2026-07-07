<?php

namespace Database\Seeders;

use App\Models\SystemCoin;
use Illuminate\Database\Seeder;

class BinanceCoinsSeeder extends Seeder
{
    public function run()
    {
        $coins = [
            ['symbol' => 'BTC', 'name' => 'Bitcoin', 'network' => 'BTC'],
            ['symbol' => 'ETH', 'name' => 'Ethereum', 'network' => 'ERC20'],
            ['symbol' => 'BNB', 'name' => 'BNB', 'network' => 'BEP20'],
            ['symbol' => 'SOL', 'name' => 'Solana', 'network' => 'SOL'],
            ['symbol' => 'XRP', 'name' => 'XRP', 'network' => 'XRP'],
            ['symbol' => 'ADA', 'name' => 'Cardano', 'network' => 'Cardano'],
            ['symbol' => 'DOGE', 'name' => 'Dogecoin', 'network' => 'Dogecoin'],
            ['symbol' => 'TRX', 'name' => 'TRON', 'network' => 'TRC20'],
            ['symbol' => 'DOT', 'name' => 'Polkadot', 'network' => 'Polkadot'],
            ['symbol' => 'MATIC', 'name' => 'Polygon', 'network' => 'Polygon'],
            ['symbol' => 'LTC', 'name' => 'Litecoin', 'network' => 'LTC'],
            ['symbol' => 'BCH', 'name' => 'Bitcoin Cash', 'network' => 'BCH'],
            ['symbol' => 'LINK', 'name' => 'Chainlink', 'network' => 'ERC20'],
            ['symbol' => 'XLM', 'name' => 'Stellar', 'network' => 'Stellar'],
            ['symbol' => 'ATOM', 'name' => 'Cosmos', 'network' => 'Cosmos'],
            ['symbol' => 'USDT', 'name' => 'Tether', 'network' => 'TRC20'],
        ];

        foreach ($coins as $c) {
            SystemCoin::updateOrCreate(
                ['symbol' => $c['symbol']],
                array_merge($c, ['is_active' => true, 'min_swap_amount' => 0.001, 'fee_percentage' => 0.5])
            );
        }
    }
}
