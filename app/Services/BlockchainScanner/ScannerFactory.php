<?php

namespace App\Services\BlockchainScanner;

use App\Enums\Blockchain;
use App\Models\MonitoredWallet;
use App\Services\BlockchainScanner\Contracts\BlockchainScannerInterface;
use App\Services\BlockchainScanner\Scanners\BitcoinScanner;
use App\Services\BlockchainScanner\Scanners\TronScanner;
use Exception;

class ScannerFactory
{
    /**
     * @throws Exception
     */
    public static function make(Blockchain $blockchain): BlockchainScannerInterface
    {
        return match ($blockchain) {
            Blockchain::BTC => app(BitcoinScanner::class),
            Blockchain::TRX => app(TronScanner::class),
            // We will stub the rest for now and add them as needed
            default => throw new Exception("No scanner implemented for blockchain: {$blockchain->value}"),
        };
    }
}
