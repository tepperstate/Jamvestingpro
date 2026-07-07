<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Services\EtfLogoService;
use Illuminate\Console\Command;

class SeedCryptoETFs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etf:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed real US Crypto ETFs into the packages table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding Crypto ETFs...');

        $etfs = EtfLogoService::getKnownCryptoETFs();
        $count = 0;

        foreach ($etfs as $etfData) {
            $exists = Package::where('type', 'etf')
                ->where(function ($q) use ($etfData) {
                    $q->where('ticker', $etfData['ticker'])
                        ->orWhere('name', 'like', '%'.$etfData['ticker'].'%');
                })->exists();

            if (! $exists) {
                Package::create([
                    'type' => 'etf',
                    'name' => $etfData['name'],
                    'ticker' => $etfData['ticker'],
                    'logo_url' => $etfData['logo_url'],
                    'amount' => 100, // Default minimum
                    'min_deposit' => 100,
                    'perc' => 15, // Default ROI
                    'day' => 30, // Default duration
                ]);
                $count++;
                $this->line("Created ETF: {$etfData['ticker']} - {$etfData['name']}");
            } else {
                $this->line("Skipped existing ETF: {$etfData['ticker']}");
            }
        }

        $this->info("Successfully seeded {$count} new Crypto ETF plans.");
    }
}
