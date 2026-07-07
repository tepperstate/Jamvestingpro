<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\Stock_Trade;
use App\Services\MarketData\MarketDataGateway;
use Illuminate\Console\Command;

class Stocks extends Command
{
    protected $signature = 'stocks:price';

    protected $description = 'Fetch Stock prices using multi-provider service';

    protected $priceService;

    public function __construct(MarketDataGateway $priceService)
    {
        parent::__construct();
        $this->priceService = $priceService;
    }

    public function handle()
    {
        $assets = Stock_Trade::all();

        foreach ($assets as $asset) {
            $priceData = $this->priceService->getPrice($asset->symbol, 'stock');

            if ($priceData && isset($priceData['price'])) {
                $price = (float) $priceData['price'];
                $percentage = (float) ($priceData['percentage'] ?? 0);
                $spreadRaw = (isset($priceData['spread']) && $priceData['spread'] > 0) ? (float) $priceData['spread'] : 1.0; // Assume 1% default if from spread field
                $spread = $spreadRaw / 100;

                $highPrice = $price * (1 + ($spread / 2));
                $lowPrice = $price * (1 - ($spread / 2));
                $priceChange = $highPrice - $lowPrice;

                $open = (float) ($priceData['open'] ?? 0);
                $high = (float) ($priceData['high'] ?? 0);
                $low = (float) ($priceData['low'] ?? 0);
                $volume = $priceData['volume'] ?? ($asset->volume ?: rand(1000, 5000));

                // Update Stock_Trade table
                Stock_Trade::where('id', $asset->id)->update([
                    'buy' => $highPrice, // User Buys (High)
                    'sell' => $lowPrice,  // User Sells (Low)
                    'changes_percentage' => $percentage,
                    'volume' => $volume,
                    'daily_open' => $open,
                    'daily_high' => $high,
                    'daily_low' => $low,
                ]);

                // Sync with Asset table (ID 3 = STOCKS)
                Asset::where('symbols', $asset->symbol)->where('exchanges_id', 3)->update([
                    'buy' => $highPrice,
                    'sell' => $lowPrice,
                    'percentage' => $percentage,
                ]);

                $this->info("Updated Stock {$asset->symbol}: {$price} ({$percentage}%) (Source: {$priceData['source']})");
            } else {
                $this->error("Failed for {$asset->symbol}");
            }

            usleep(500000);
        }

        return Command::SUCCESS;
    }
}
