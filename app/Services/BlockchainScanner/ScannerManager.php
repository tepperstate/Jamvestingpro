<?php

namespace App\Services\BlockchainScanner;

use App\Models\BlockchainTransaction;
use App\Models\MonitoredWallet;
use App\Services\BlockchainScanner\DTOs\DiscoveredTransaction;
use Illuminate\Support\Facades\Log;

class ScannerManager
{
    /**
     * Run all active scanners for all active monitored wallets.
     */
    public function runAllScanners(): void
    {
        // Group wallets by blockchain
        $walletsByChain = MonitoredWallet::where('is_active', true)
            ->get()
            ->groupBy(fn ($wallet) => $wallet->blockchain->value);

        foreach ($walletsByChain as $blockchainValue => $wallets) {
            try {
                $blockchain = \App\Enums\Blockchain::from($blockchainValue);
                $scanner = ScannerFactory::make($blockchain);
                
                Log::info("Starting scanner for blockchain: {$blockchainValue}");
                $discoveredTxs = $scanner->scan($wallets);
                
                $this->processDiscoveredTransactions($discoveredTxs);
                
            } catch (\Exception $e) {
                Log::error("Failed to run scanner for {$blockchainValue}: " . $e->getMessage());
            }
        }
    }

    /**
     * Persist discovered transactions to the database.
     * 
     * @param \Illuminate\Support\Collection<DiscoveredTransaction> $transactions
     */
    protected function processDiscoveredTransactions(\Illuminate\Support\Collection $transactions): void
    {
        foreach ($transactions as $dto) {
            // Upsert transaction to avoid duplicates
            // We consider blockchain + tx_hash + to_address as unique
            $tx = BlockchainTransaction::firstOrNew([
                'blockchain' => $dto->blockchain->value,
                'tx_hash' => $dto->txHash,
                'to_address' => $dto->toAddress,
            ]);

            // Only update if it's new or if confirmations have changed
            if (!$tx->exists || $tx->confirmations < $dto->confirmations) {
                $tx->fill([
                    'network' => $dto->network,
                    'block_number' => $dto->blockNumber,
                    'block_hash' => $dto->blockHash,
                    'block_timestamp' => $dto->blockTimestamp,
                    'from_address' => $dto->fromAddress,
                    'currency' => $dto->currency,
                    'token_contract' => $dto->tokenContract,
                    'amount' => $dto->amountRaw,
                    'amount_decimal' => $dto->amountDecimal,
                    'fee' => $dto->feeDecimal,
                    'fee_currency' => $dto->feeCurrency,
                    'confirmations' => $dto->confirmations,
                    'required_confirmations' => $dto->blockchain->requiredConfirmations(),
                    'is_confirmed' => $dto->confirmations >= $dto->blockchain->requiredConfirmations(),
                    'scan_source' => 'cron',
                    'raw_data' => $dto->rawData,
                ]);

                if (!$tx->exists) {
                    $tx->first_seen_at = now();
                }

                if ($tx->is_confirmed && !$tx->confirmed_at) {
                    $tx->confirmed_at = now();
                }

                $tx->save();
                
                // If it's a newly discovered transaction (or newly confirmed), we should trigger matching
                if (!$tx->is_processed) {
                    app(PaymentMatcher::class)->matchTransaction($tx);
                }
            }
        }
    }
}
