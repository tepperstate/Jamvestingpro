<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Services\MarketData\MarketDataGateway;
use Illuminate\Console\Command;

class Stock extends Command
{
    protected $signature = 'stock:price';

    protected $description = 'Fetch Stock prices using multi-provider service';

    protected $priceService;

    public function __construct(MarketDataGateway $priceService)
    {
        parent::__construct();
        $this->priceService = $priceService;
    }

    public function handle()
    {
        $assets = Asset::where('exchanges_id', 3)->get();

        foreach ($assets as $asset) {
            $priceData = $this->priceService->getPrice($asset->symbols, 'stock');

            if ($priceData && isset($priceData['price'])) {
                $price = $priceData['price'];
                $highPrice = $price * 1.005;
                $lowPrice = $price * 0.995;

                Asset::where('id', $asset->id)->update([
                    'buy' => $highPrice,
                    'sell' => $lowPrice,
                ]);
                $this->info("Updated Stock {$asset->symbols}: {$price} (Source: {$priceData['source']})");
            } else {
                $this->error("Failed for {$asset->symbols}");
            }

            usleep(500000);
        }

        return Command::SUCCESS;
    }
}
