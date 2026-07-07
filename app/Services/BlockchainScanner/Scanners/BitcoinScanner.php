<?php

namespace App\Services\BlockchainScanner\Scanners;

use App\Enums\Blockchain;
use App\Models\BlockchainScanCheckpoint;
use App\Models\MonitoredWallet;
use App\Services\BlockchainScanner\AbstractBlockchainScanner;
use App\Services\BlockchainScanner\DTOs\DiscoveredTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class BitcoinScanner extends AbstractBlockchainScanner
{
    protected string $baseUrl = 'https://mempool.space/api';

    public function getBlockchain(): Blockchain
    {
        return Blockchain::BTC;
    }

    protected function fetchTransactionsForAddress(MonitoredWallet $wallet, BlockchainScanCheckpoint $checkpoint): array
    {
        $address = $wallet->address;
        
        // Mempool space API
        $response = Http::timeout(10)->get("{$this->baseUrl}/address/{$address}/txs");

        if (!$response->successful()) {
            throw new \Exception("Mempool API error: " . $response->body());
        }

        $transactions = $response->json();
        $discovered = [];

        // We fetch the current tip height to calculate confirmations
        $tipResponse = Http::timeout(10)->get("{$this->baseUrl}/blocks/tip/height");
        $currentHeight = $tipResponse->successful() ? (int) $tipResponse->body() : null;

        foreach ($transactions as $tx) {
            // Only care about incoming transactions
            // A transaction is incoming if it has an output matching our address
            $incomingAmountRaw = 0;
            $isIncoming = false;

            foreach ($tx['vout'] as $vout) {
                if (isset($vout['scriptpubkey_address']) && $vout['scriptpubkey_address'] === $address) {
                    $incomingAmountRaw += $vout['value'];
                    $isIncoming = true;
                }
            }

            if (!$isIncoming) {
                continue;
            }

            // We skip if we have scanned this before. 
            // In a real system, you might paginate or filter by time. Mempool returns newest first.
            if ($checkpoint->last_tx_hash === $tx['txid']) {
                // We've reached the last seen transaction. We can break since they are ordered by time desc.
                break;
            }

            $amountDecimal = $incomingAmountRaw / 100000000; // 8 decimals for BTC

            $blockHeight = $tx['status']['block_height'] ?? null;
            $confirmations = 0;
            if ($blockHeight && $currentHeight) {
                $confirmations = max(0, $currentHeight - $blockHeight + 1);
            }

            $discovered[] = new DiscoveredTransaction(
                blockchain: Blockchain::BTC,
                network: $wallet->network,
                txHash: $tx['txid'],
                fromAddress: $tx['vin'][0]['prevout']['scriptpubkey_address'] ?? null, // First input address
                toAddress: $address,
                currency: 'BTC',
                amountDecimal: $amountDecimal,
                amountRaw: (string)$incomingAmountRaw,
                confirmations: $confirmations,
                blockNumber: $blockHeight,
                blockHash: $tx['status']['block_hash'] ?? null,
                blockTimestamp: isset($tx['status']['block_time']) ? Carbon::createFromTimestamp($tx['status']['block_time']) : null,
                feeDecimal: ($tx['fee'] ?? 0) / 100000000,
                feeCurrency: 'BTC',
                rawData: $tx
            );
        }

        // Update the checkpoint with the most recent transaction hash
        if (!empty($discovered)) {
            $checkpoint->last_tx_hash = $discovered[0]->txHash;
        }

        return array_reverse($discovered); // Return oldest to newest
    }

    public function getConfirmations(string $txHash): int
    {
        $response = Http::timeout(10)->get("{$this->baseUrl}/tx/{$txHash}/status");
        if (!$response->successful()) {
            return 0;
        }

        $status = $response->json();
        if (!isset($status['block_height'])) {
            return 0;
        }

        $tipResponse = Http::timeout(10)->get("{$this->baseUrl}/blocks/tip/height");
        if (!$tipResponse->successful()) {
            return 0;
        }

        $currentHeight = (int) $tipResponse->body();
        return max(0, $currentHeight - $status['block_height'] + 1);
    }
}
