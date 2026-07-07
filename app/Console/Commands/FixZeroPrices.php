<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Services\MarketData\MarketDataGateway;
use Illuminate\Console\Command;

class FixZeroPrices extends Command
{
    protected $signature = 'assets:fix-zeros';

    protected $description = 'Identify and fix assets with zero or placeholder prices';

    protected $priceService;

    public function __construct(MarketDataGateway $priceService)
    {
        parent::__construct();
        $this->priceService = $priceService;
    }

    public function handle()
    {
        $this->info('Scanning for zero prices...');

        $assets = Asset::where('buy', 0)->orWhere('sell', 0)->get();
        $this->info('Found '.$assets->count().' assets with zero prices.');

        foreach ($assets as $asset) {
            $type = 'crypto';
            if ($asset->exchanges_id == 1) {
                $type = 'forex';
            } elseif ($asset->exchanges_id == 3) {
                $type = 'stock';
            }

            $this->output->write("Fixing {$asset->symbols} ({$type})... ");

            $priceData = $this->priceService->getPrice($asset->symbols, $type);

            if ($priceData && isset($priceData['price']) && $priceData['price'] > 0) {
                $convertedAmount = $priceData['price'];
                $spread = (isset($priceData['spread']) && $priceData['spread'] > 0) ? ($priceData['spread'] / 100) : 0.02;
                $buyAmount = $convertedAmount * (1 + $spread);

                Asset::where('id', $asset->id)->update([
                    'buy' => $buyAmount,
                    'sell' => $convertedAmount,
                    'changes' => rand(1, 15),
                ]);
                $this->line("<fg=green>FIXED</> (Source: {$priceData['source']})");
            } else {
                $this->line('<fg=red>FAILED</>');
            }
        }

        $this->info('Scan completed.');

        return Command::SUCCESS;
    }
}
