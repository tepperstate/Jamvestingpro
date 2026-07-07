<?php

namespace App\Services\BlockchainScanner\Scanners;

use App\Enums\Blockchain;
use App\Models\BlockchainScanCheckpoint;
use App\Models\MonitoredWallet;
use App\Services\BlockchainScanner\AbstractBlockchainScanner;
use App\Services\BlockchainScanner\DTOs\DiscoveredTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TronScanner extends AbstractBlockchainScanner
{
    protected string $baseUrl = 'https://api.trongrid.io';

    public function getBlockchain(): Blockchain
    {
        return Blockchain::TRX;
    }

    protected function fetchTransactionsForAddress(MonitoredWallet $wallet, BlockchainScanCheckpoint $checkpoint): array
    {
        $address = $wallet->address;
        
        // Fetch TRC-20 transfers (most likely USDT)
        // If it's pure TRX, we'd use /v1/accounts/{address}/transactions
        $url = "{$this->baseUrl}/v1/accounts/{$address}/transactions/trc20";
        
        $query = [
            'only_to' => 'true',
            'limit' => 50,
        ];

        // If we want a specific token
        if ($wallet->token_contract) {
            $query['contract_address'] = $wallet->token_contract;
        }

        // Add min_timestamp to only fetch new txs if we have a checkpoint
        // TronGrid uses milliseconds
        if ($checkpoint->last_scan_at) {
            $query['min_timestamp'] = $checkpoint->last_scan_at->subMinutes(5)->timestamp * 1000;
        }

        $response = Http::timeout(10)->get($url, $query);

        if (!$response->successful()) {
            throw new \Exception("TronGrid API error: " . $response->body());
        }

        $data = $response->json();
        $transactions = $data['data'] ?? [];
        $discovered = [];

        foreach ($transactions as $tx) {
            if ($checkpoint->last_tx_hash === $tx['transaction_id']) {
                break; // Reached last checkpoint
            }

            // TRC20 Decimals depends on the token. USDT is 6. 
            $decimals = $tx['token_info']['decimals'] ?? 6;
            $amountDecimal = $tx['value'] / pow(10, $decimals);

            $discovered[] = new DiscoveredTransaction(
                blockchain: Blockchain::TRX,
                network: $wallet->network,
                txHash: $tx['transaction_id'],
                fromAddress: $tx['from'],
                toAddress: $tx['to'],
                currency: $tx['token_info']['symbol'] ?? 'USDT',
                amountDecimal: $amountDecimal,
                amountRaw: $tx['value'],
                confirmations: 19, // Tron confirms very quickly
                blockNumber: null,
                blockHash: null,
                blockTimestamp: isset($tx['block_timestamp']) ? Carbon::createFromTimestampMs($tx['block_timestamp']) : null,
                tokenContract: $tx['token_info']['address'] ?? null,
                rawData: $tx
            );
        }

        if (!empty($discovered)) {
            $checkpoint->last_tx_hash = $discovered[0]->txHash;
        }

        return array_reverse($discovered);
    }

    public function getConfirmations(string $txHash): int
    {
        // Tron finalizes quickly. We can just assume confirmed if it exists, or check solidity node.
        // For simplicity:
        return 19;
    }
}
