<?php

namespace App\Services\BlockchainScanner\DTOs;

use App\Enums\Blockchain;
use Carbon\Carbon;

class DiscoveredTransaction
{
    public function __construct(
        public Blockchain $blockchain,
        public string $network,
        public string $txHash,
        public ?string $fromAddress,
        public string $toAddress,
        public string $currency,
        public float $amountDecimal, // The human readable amount e.g. 0.05 BTC
        public string $amountRaw,    // The raw integer string e.g. 5000000
        public int $confirmations,
        public ?int $blockNumber = null,
        public ?string $blockHash = null,
        public ?Carbon $blockTimestamp = null,
        public ?float $feeDecimal = null,
        public ?string $feeCurrency = null,
        public ?string $tokenContract = null,
        public array $rawData = []
    ) {}
}
