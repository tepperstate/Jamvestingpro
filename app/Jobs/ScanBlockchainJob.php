<?php

namespace App\Jobs;

use App\Services\BlockchainScanner\ConfirmationTracker;
use App\Services\BlockchainScanner\ScannerManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScanBlockchainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ScannerManager $scannerManager, ConfirmationTracker $confirmationTracker): void
    {
        // 1. Scan for new incoming transactions
        $scannerManager->runAllScanners();

        // 2. Re-check confirmations for previously discovered but unconfirmed matched transactions
        $confirmationTracker->trackUnconfirmed();
    }
}
