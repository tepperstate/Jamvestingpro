<?php

namespace App\Services\BlockchainScanner\Contracts;

use App\Models\MonitoredWallet;
use Illuminate\Support\Collection;

interface BlockchainScannerInterface
{
    /**
     * Get the blockchain enum this scanner supports.
     */
    public function getBlockchain(): \App\Enums\Blockchain;

    /**
     * Scan the blockchain for incoming transactions to the given wallets.
     * 
     * @param Collection<MonitoredWallet> $wallets
     * @return Collection<\App\Services\BlockchainScanner\DTOs\DiscoveredTransaction>
     */
    public function scan(Collection $wallets): Collection;

    /**
     * Check confirmations for a specific transaction hash.
     * 
     * @param string $txHash
     * @return int
     */
    public function getConfirmations(string $txHash): int;
}
