<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BlockchainScanner\ScannerManager;
use App\Services\BlockchainScanner\ConfirmationTracker;
use Illuminate\Support\Facades\Log;

class ScanBlockchainCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blockchain:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan blockchains for incoming crypto deposits and match them to requests';

    /**
     * Execute the console command.
     */
    public function handle(ScannerManager $scannerManager, ConfirmationTracker $confirmationTracker)
    {
        $this->info('Starting blockchain scan...');
        
        try {
            $scannerManager->runAllScanners();
            $this->info('Scanners completed.');

            $this->info('Tracking unconfirmed transactions...');
            $confirmationTracker->trackUnconfirmed();
            $this->info('Tracking completed.');

        } catch (\Exception $e) {
            $this->error('Error during blockchain scan: ' . $e->getMessage());
            Log::error('ScanBlockchainCommand failed: ' . $e->getMessage());
        }
    }
}
