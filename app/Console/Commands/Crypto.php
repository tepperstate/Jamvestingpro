<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Services\MarketData\MarketDataGateway;
use Illuminate\Console\Command;

class Crypto extends Command
{
    protected $signature = 'crypto:price';

    protected $description = 'Fetch Crypto prices using multi-provider service';

    protected $priceService;

    public function __construct(MarketDataGateway $priceService)
    {
        parent::__construct();
        $this->priceService = $priceService;
    }

    public function handle()
    {
        $assets = Asset::where('exchanges_id', 2)->get();

        foreach ($assets as $asset) {
            $priceData = $this->priceService->getPrice($asset->symbols, 'crypto');

            if ($priceData && isset($priceData['price'])) {
                $convertedAmount = $priceData['price'];
                $spread = (isset($priceData['spread']) && $priceData['spread'] > 0) ? ($priceData['spread'] / 100) : 0.02;
                $buyAmount = $convertedAmount * (1 + $spread);
                $change = rand(1, 30);

                Asset::where('id', $asset->id)->update([
                    'buy' => $buyAmount,
                    'sell' => $convertedAmount,
                    'changes' => $change,
                ]);
                $this->info("Updated {$asset->symbols}: Buy {$buyAmount}, Sell {$convertedAmount} (Source: {$priceData['source']})");
            } else {
                $this->error("Failed to update {$asset->symbols}");
            }

            // Minimal sleep to respect rate limits if providers are shared
            usleep(500000); // 0.5s
        }

        return Command::SUCCESS;
    }
}
