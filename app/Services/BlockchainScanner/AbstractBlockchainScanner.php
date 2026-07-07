<?php

namespace App\Services\BlockchainScanner;

use App\Models\BlockchainScanCheckpoint;
use App\Models\BlockchainScanLog;
use App\Models\MonitoredWallet;
use App\Services\BlockchainScanner\Contracts\BlockchainScannerInterface;
use App\Services\BlockchainScanner\DTOs\DiscoveredTransaction;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

abstract class AbstractBlockchainScanner implements BlockchainScannerInterface
{
    /**
     * Executes the scan process and handles checkpointing and error logging.
     */
    public function scan(Collection $wallets): Collection
    {
        $discoveredTxs = collect();
        $blockchain = $this->getBlockchain();

        foreach ($wallets as $wallet) {
            $checkpoint = BlockchainScanCheckpoint::firstOrCreate([
                'blockchain' => $blockchain->value,
                'network' => $wallet->network,
                'scanner_type' => 'address',
                'address' => $wallet->address,
            ]);

            try {
                $txs = $this->fetchTransactionsForAddress($wallet, $checkpoint);
                
                foreach ($txs as $tx) {
                    $discoveredTxs->push($tx);
                }

                $this->updateCheckpointOnSuccess($checkpoint);
            } catch (Exception $e) {
                $this->logError($wallet, $e);
                $this->updateCheckpointOnError($checkpoint, $e);
            }
        }

        return $discoveredTxs;
    }

    /**
     * Concrete classes must implement this to actually call the API.
     * 
     * @return DiscoveredTransaction[]
     */
    abstract protected function fetchTransactionsForAddress(MonitoredWallet $wallet, BlockchainScanCheckpoint $checkpoint): array;

    protected function updateCheckpointOnSuccess(BlockchainScanCheckpoint $checkpoint): void
    {
        $checkpoint->update([
            'last_scan_at' => now(),
            'scan_count' => $checkpoint->scan_count + 1,
            'consecutive_errors' => 0,
        ]);
    }

    protected function updateCheckpointOnError(BlockchainScanCheckpoint $checkpoint, Exception $e): void
    {
        $checkpoint->update([
            'last_scan_at' => now(),
            'scan_count' => $checkpoint->scan_count + 1,
            'consecutive_errors' => $checkpoint->consecutive_errors + 1,
            'last_error' => $e->getMessage(),
        ]);
    }

    protected function logError(MonitoredWallet $wallet, Exception $e): void
    {
        Log::error("BlockchainScanner Error [{$wallet->blockchain->value}]: " . $e->getMessage(), [
            'wallet' => $wallet->address,
            'trace' => $e->getTraceAsString(),
        ]);

        BlockchainScanLog::create([
            'blockchain' => $wallet->blockchain->value,
            'event_type' => 'scan_error',
            'severity' => 'error',
            'message' => $e->getMessage(),
            'context' => ['wallet_address' => $wallet->address],
        ]);
    }
}
