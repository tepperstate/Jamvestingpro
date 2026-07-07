<?php

namespace App\Console\Commands;

use App\Models\FuturesPair;
use App\Models\Stock_Trade;
use App\Services\BinancePriceService;
use Illuminate\Console\Command;

class SyncBinanceMarkets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'binance:sync-markets {--limit=150 : Limit the number of top pairs synced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize top USDT market pairs from Binance into spot and futures tables';

    // A mapping for some common coin names
    private $knownNames = [
        'BTC' => 'Bitcoin', 'ETH' => 'Ethereum', 'SOL' => 'Solana', 'BNB' => 'Binance Coin',
        'XRP' => 'Ripple', 'ADA' => 'Cardano', 'DOGE' => 'Dogecoin', 'TRX' => 'TRON',
        'DOT' => 'Polkadot', 'LTC' => 'Litecoin', 'LINK' => 'Chainlink', 'MATIC' => 'Polygon',
        'AVAX' => 'Avalanche', 'ATOM' => 'Cosmos', 'SHIB' => 'Shiba Inu', 'UNI' => 'Uniswap',
        'XLM' => 'Stellar', 'ETC' => 'Ethereum Classic', 'BCH' => 'Bitcoin Cash', 'FIL' => 'Filecoin',
        'NEAR' => 'Near Protocol', 'GRT' => 'The Graph', 'FTM' => 'Fantom', 'MKR' => 'Maker',
        'AAVE' => 'Aave', 'ALGO' => 'Algorand', 'MANA' => 'Decentraland', 'AXS' => 'Axie Infinity',
        'EOS' => 'EOS', 'NEO' => 'NEO', 'EGLD' => 'MultiversX', 'VET' => 'VeChain',
        'SAND' => 'The Sandbox', 'CHZ' => 'Chiliz', 'OP' => 'Optimism', 'ARB' => 'Arbitrum',
        'APT' => 'Aptos', 'SUI' => 'Sui', 'IMX' => 'Immutable', 'RNDR' => 'Render',
        'TIA' => 'Celestia', 'SEI' => 'Sei', 'INJ' => 'Injective', 'LDO' => 'Lido DAO',
        'FET' => 'Fetch.ai', 'PEPE' => 'Pepe', 'FLOKI' => 'Floki', 'BONK' => 'Bonk',
        'WIF' => 'Dogwifhat', 'JUP' => 'Jupiter', 'PYTH' => 'Pyth Network', 'ONDO' => 'Ondo',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $this->info("Starting Binance Market Sync (Limit: $limit top USDT pairs)...");

        $fullTickerData = BinancePriceService::get24hrTickerData();

        // ---------------------------------------------------------
        // 1. Sync Spot Markets
        // ---------------------------------------------------------
        $this->info('Syncing Spot markets...');
        $spotInfo = BinancePriceService::getSpotExchangeInfo();

        if (! $spotInfo || ! isset($spotInfo['symbols'])) {
            $this->error('Failed to fetch Spot exchange info.');
        } else {
            // Filter USDT pairs that are trading
            $spotPairs = array_filter($spotInfo['symbols'], function ($s) {
                return $s['quoteAsset'] === 'USDT' && $s['status'] === 'TRADING';
            });

            // If we have ticker data, let's sort by volume so we get the top ones
            if ($fullTickerData) {
                usort($spotPairs, function ($a, $b) use ($fullTickerData) {
                    $volA = isset($fullTickerData[$a['symbol']]) ? (float) $fullTickerData[$a['symbol']]['quoteVolume'] : 0;
                    $volB = isset($fullTickerData[$b['symbol']]) ? (float) $fullTickerData[$b['symbol']]['quoteVolume'] : 0;

                    return $volB <=> $volA;
                });
            }

            $topSpot = array_slice($spotPairs, 0, $limit);
            $bar = $this->output->createProgressBar(count($topSpot));
            $bar->start();

            foreach ($topSpot as $pair) {
                $sym = $pair['symbol'];
                $base = $pair['baseAsset'];
                $friendlyName = $this->knownNames[$base] ?? $base;

                $livePrice = isset($fullTickerData[$sym]) ? (float) $fullTickerData[$sym]['lastPrice'] : 0.01;
                $liveChange = isset($fullTickerData[$sym]) ? (float) $fullTickerData[$sym]['priceChangePercent'] : 0;

                Stock_Trade::updateOrCreate(
                    ['symbol' => $sym],
                    array_filter([
                        'name' => $friendlyName,
                        'buy' => $livePrice,
                        'sell' => $livePrice * 0.999, // slight spread
                        'changes' => $liveChange,
                        'volume' => rand(100000, 9000000), // fallback if needed
                        'image' => strtolower($base).'.png',
                        // Keep is_vip out so we don't accidentally update it if admin changed it
                    ])
                );
                // default is_vip = false
                Stock_Trade::where('symbol', $sym)->whereNull('is_vip')->update(['is_vip' => false]);
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info('Spot markets synced.');
        }

        // ---------------------------------------------------------
        // 2. Sync Futures Markets
        // ---------------------------------------------------------
        $this->info('Syncing Futures markets...');
        $futuresInfo = BinancePriceService::getFuturesExchangeInfo();

        if (! $futuresInfo || ! isset($futuresInfo['symbols'])) {
            $this->error('Failed to fetch Futures exchange info.');
        } else {
            // Filter USDT margin contracts that are trading
            $futuresPairs = array_filter($futuresInfo['symbols'], function ($s) {
                return $s['quoteAsset'] === 'USDT' && $s['status'] === 'TRADING' && $s['contractType'] === 'PERPETUAL';
            });

            // Sort by volume if available in our full spot ticker map, though futures might have its own ticker.
            // But doing it approximately is fine.
            if ($fullTickerData) {
                usort($futuresPairs, function ($a, $b) use ($fullTickerData) {
                    $volA = isset($fullTickerData[$a['symbol']]) ? (float) $fullTickerData[$a['symbol']]['quoteVolume'] : 0;
                    $volB = isset($fullTickerData[$b['symbol']]) ? (float) $fullTickerData[$b['symbol']]['quoteVolume'] : 0;

                    return $volB <=> $volA;
                });
            }

            $topFutures = array_slice($futuresPairs, 0, $limit);
            $bar = $this->output->createProgressBar(count($topFutures));
            $bar->start();

            foreach ($topFutures as $pair) {
                $sym = $pair['symbol'];
                $base = $pair['baseAsset'];

                $livePrice = isset($fullTickerData[$sym]) ? (float) $fullTickerData[$sym]['lastPrice'] : 0.01;

                $existing = FuturesPair::where('symbol', $sym)->first();

                if (! $existing) {
                    FuturesPair::create([
                        'symbol' => $sym,
                        'mirror_symbol' => $sym,
                        'base_asset' => $base,
                        'quote_asset' => $pair['quoteAsset'],
                        'max_leverage' => 125,
                        'funding_rate' => 0.0100,
                        'mark_price' => $livePrice,
                        'index_price' => $livePrice,
                        'maintenance_margin' => 0.5,
                        'maker_fee' => 0.02,
                        'taker_fee' => 0.04,
                        'insurance_fund' => 1000000,
                        'open_interest_long' => 0,
                        'open_interest_short' => 0,
                        'buffer_percent' => 0.1,
                        'per_withdrawal_percent' => 10,
                        'status' => 'active',
                        'image' => strtolower($base).'.png',
                    ]);
                } else {
                    $existing->update([
                        'mark_price' => $livePrice,
                        'index_price' => $livePrice,
                    ]);
                }

                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info('Futures markets synced.');
        }

        $this->info('Binance Market Sync Complete!');
    }
}
