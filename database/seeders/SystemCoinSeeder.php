<?php

namespace Database\Seeders;

use App\Models\SystemCoin;
use Illuminate\Database\Seeder;

class SystemCoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $coins = [
            ['name' => 'Bitcoin', 'symbol' => 'BTC', 'is_active' => true],
            ['name' => 'Ethereum', 'symbol' => 'ETH', 'is_active' => true],
            ['name' => 'Solana', 'symbol' => 'SOL', 'is_active' => true],
            ['name' => 'Cardano', 'symbol' => 'ADA', 'is_active' => true],
            ['name' => 'Ripple', 'symbol' => 'XRP', 'is_active' => true],
            ['name' => 'TetherUS', 'symbol' => 'USDT', 'is_active' => true],
            ['name' => 'Dogecoin', 'symbol' => 'DOGE', 'is_active' => true],
            ['name' => 'Polkadot', 'symbol' => 'DOT', 'is_active' => true],
            ['name' => 'Polygon', 'symbol' => 'MATIC', 'is_active' => true],
            ['name' => 'Chainlink', 'symbol' => 'LINK', 'is_active' => true],
        ];

        foreach ($coins as $coin) {
            SystemCoin::updateOrCreate(
                ['symbol' => $coin['symbol']],
                $coin
            );
        }
    }
}
