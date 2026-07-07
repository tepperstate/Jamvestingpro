<?php

namespace App\Console\Commands;

use App\Models\FuturesPair;
use App\Models\MarginPair;
use App\Models\Stock_Trade;
use App\Models\SystemCoin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncBinanceCoins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:binance-coins {--limit=50 : The number of top coins to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch top coins from Binance and populate the system_coins table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Fetching data from Binance...');

        try {
            $limit = (int) $this->option('limit');

            // Try different Binance endpoints
            $endpoints = [
                'https://api.binance.us/api/v3/ticker/24hr',
                'https://data-api.binance.vision/api/v3/ticker/24hr',
                'https://api.binance.com/api/v3/ticker/24hr',
            ];

            $tickers = null;
            foreach ($endpoints as $endpoint) {
                try {
                    $response = Http::withoutVerifying()->timeout(15)->get($endpoint);
                    if ($response->successful()) {
                        $tickers = $response->json();
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if (! $tickers) {
                $this->warn('Failed to fetch data from Binance API. Using fallback top coins list.');
                // Fallback top coins list based on typical marketcap
                $topCoins = collect([
                    ['symbol' => 'BTCUSDT', 'quoteVolume' => 1000],
                    ['symbol' => 'ETHUSDT', 'quoteVolume' => 900],
                    ['symbol' => 'BNBUSDT', 'quoteVolume' => 800],
                    ['symbol' => 'SOLUSDT', 'quoteVolume' => 700],
                    ['symbol' => 'XRPUSDT', 'quoteVolume' => 600],
                    ['symbol' => 'ADAUSDT', 'quoteVolume' => 500],
                    ['symbol' => 'AVAXUSDT', 'quoteVolume' => 400],
                    ['symbol' => 'DOGEUSDT', 'quoteVolume' => 300],
                    ['symbol' => 'TRXUSDT', 'quoteVolume' => 200],
                    ['symbol' => 'DOTUSDT', 'quoteVolume' => 100],
                    ['symbol' => 'MATICUSDT', 'quoteVolume' => 90],
                    ['symbol' => 'LINKUSDT', 'quoteVolume' => 80],
                    ['symbol' => 'LTCUSDT', 'quoteVolume' => 70],
                    ['symbol' => 'BCHUSDT', 'quoteVolume' => 60],
                    ['symbol' => 'SHIBUSDT', 'quoteVolume' => 50],
                    ['symbol' => 'UNIUSDT', 'quoteVolume' => 40],
                    ['symbol' => 'ATOMUSDT', 'quoteVolume' => 30],
                    ['symbol' => 'XMRUSDT', 'quoteVolume' => 20],
                    ['symbol' => 'ETCUSDT', 'quoteVolume' => 10],
                    ['symbol' => 'FILUSDT', 'quoteVolume' => 5],
                ]);
            } else {
                // Filter popular quote pairs (USDT, BTC, ETH, BNB) and those with active trading
                $usdtPairs = collect($tickers)->filter(function ($ticker) {
                    return (str_ends_with($ticker['symbol'], 'USDT') || str_ends_with($ticker['symbol'], 'BTC') || str_ends_with($ticker['symbol'], 'ETH') || str_ends_with($ticker['symbol'], 'BNB')) && floatval($ticker['quoteVolume']) > 0;
                });

                // Sort by quote volume descending to get the most popular coins
                $topCoins = $usdtPairs->sortByDesc(function ($ticker) {
                    return floatval($ticker['quoteVolume']);
                })->take($limit)->values();
            }

            $this->info('Found '.$topCoins->count().' top pairs. Syncing to database...');

            $bar = $this->output->createProgressBar($topCoins->count());
            $bar->start();

            $count = 0;
            foreach ($topCoins as $coin) {
                $fullSymbol = $coin['symbol'];

                // Extract base asset
                $baseAsset = $fullSymbol;
                $quoteAsset = 'USDT';
                if (str_ends_with($fullSymbol, 'USDT')) {
                    $baseAsset = substr($fullSymbol, 0, -4);
                    $quoteAsset = 'USDT';
                } elseif (str_ends_with($fullSymbol, 'BTC')) {
                    $baseAsset = substr($fullSymbol, 0, -3);
                    $quoteAsset = 'BTC';
                } elseif (str_ends_with($fullSymbol, 'ETH')) {
                    $baseAsset = substr($fullSymbol, 0, -3);
                    $quoteAsset = 'ETH';
                } elseif (str_ends_with($fullSymbol, 'BNB')) {
                    $baseAsset = substr($fullSymbol, 0, -3);
                    $quoteAsset = 'BNB';
                }

                // Skip stablecoins that are basically USD if it's a USDT quote
                if (in_array($baseAsset, ['USDT', 'USDC', 'DAI', 'BUSD', 'TUSD', 'FDUSD'])) {
                    $bar->advance();

                    continue;
                }

                $knownNames = [
                    'BTC' => 'Bitcoin', 'ETH' => 'Ethereum', 'SOL' => 'Solana', 'BNB' => 'BNB',
                    'XRP' => 'Ripple', 'ADA' => 'Cardano', 'DOGE' => 'Dogecoin', 'TRX' => 'TRON',
                    'LINK' => 'Chainlink', 'DOT' => 'Polkadot', 'MATIC' => 'Polygon', 'LTC' => 'Litecoin',
                    'BCH' => 'Bitcoin Cash', 'AVAX' => 'Avalanche', 'SHIB' => 'Shiba Inu', 'XLM' => 'Stellar',
                    'UNI' => 'Uniswap', 'ATOM' => 'Cosmos', 'XMR' => 'Monero', 'ETC' => 'Ethereum Classic',
                    'HBAR' => 'Hedera', 'FIL' => 'Filecoin', 'NEAR' => 'NEAR Protocol', 'APT' => 'Aptos',
                    'ARB' => 'Arbitrum', 'OP' => 'Optimism', 'INJ' => 'Injective', 'RNDR' => 'Render',
                    'PEPE' => 'Pepe', 'SUI' => 'Sui', 'SEI' => 'Sei', 'TIA' => 'Celestia',
                ];

                $name = $knownNames[$baseAsset] ?? $baseAsset;

                SystemCoin::updateOrCreate(
                    ['symbol' => $baseAsset],
                    ['name' => $name]
                );

                // Also populate Stock_Trade (Spot)
                Stock_Trade::updateOrCreate(
                    ['symbol' => $fullSymbol],
                    ['name' => $name, 'is_vip' => false, 'image' => 'default.png']
                );

                // Also populate MarginPair
                MarginPair::updateOrCreate(
                    ['symbol' => $fullSymbol],
                    [
                        'status' => 'active',
                        'max_leverage' => 10,
                        'borrow_rate_hourly' => 0.001,
                        'maintenance_margin' => 5,
                        'max_borrow' => 100000,
                        'collateral_factor' => 90,
                        'mark_price' => 100,
                        'buffer_percent' => 5,
                        'per_withdrawal_percent' => 100,
                    ]
                );

                // Also populate FuturesPair
                FuturesPair::updateOrCreate(
                    ['symbol' => $fullSymbol],
                    [
                        'base_asset' => $baseAsset,
                        'quote_asset' => $quoteAsset,
                        'status' => 'active',
                        'max_leverage' => 50,
                        'funding_rate' => 0.01,
                        'maintenance_margin' => 5,
                        'mark_price' => 100,
                        'index_price' => 100,
                        'maker_fee' => 0.02,
                        'taker_fee' => 0.04,
                        'insurance_fund' => 10000,
                        'open_interest_long' => 0,
                        'open_interest_short' => 0,
                        'buffer_percent' => 5,
                        'per_withdrawal_percent' => 100,
                    ]
                );

                $count++;
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Successfully synced {$count} coins to the database!");

            return 0;

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
            Log::error('Binance sync error: '.$e->getMessage());

            return 1;
        }
    }
}
