<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Services\MarketData\MarketDataGateway;
use Illuminate\Console\Command;

class Forex extends Command
{
    protected $signature = 'forex:price';

    protected $description = 'Fetch Forex prices using multi-provider service';

    protected $priceService;

    public function __construct(MarketDataGateway $priceService)
    {
        parent::__construct();
        $this->priceService = $priceService;
    }

    public function handle()
    {
        $assets = Asset::where('exchanges_id', 1)->get();

        foreach ($assets as $asset) {
            $priceData = $this->priceService->getPrice($asset->symbols, 'forex');

            if ($priceData && isset($priceData['price'])) {
                $price = $priceData['price'];
                $spread = (isset($priceData['spread']) && $priceData['spread'] > 0) ? ($priceData['spread'] / 100) : 0.002;
                $highPrice = $price * (1 + ($spread / 2));
                $lowPrice = $price * (1 - ($spread / 2));

                Asset::where('id', $asset->id)->update([
                    'buy' => $highPrice,
                    'sell' => $lowPrice,
                ]);
                $this->info("Updated Forex {$asset->symbols}: {$price} (Source: {$priceData['source']})");
            } else {
                $this->error("Failed for {$asset->symbols}");
            }

            usleep(500000);
        }

        return Command::SUCCESS;
    }
}
