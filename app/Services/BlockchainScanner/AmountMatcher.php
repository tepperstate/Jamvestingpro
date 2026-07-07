<?php

namespace App\Services\BlockchainScanner;

use App\Models\BlockchainTransaction;
use App\Models\PendingPaymentRequest;
use Illuminate\Support\Collection;

class AmountMatcher
{
    /**
     * Find the best matching PendingPaymentRequest for a given Transaction based on amount.
     *
     * @param BlockchainTransaction $tx
     * @param Collection<PendingPaymentRequest> $candidates
     * @return PendingPaymentRequest|null
     */
    public function findBestMatch(BlockchainTransaction $tx, Collection $candidates): ?PendingPaymentRequest
    {
        $txAmount = (float) $tx->amount_decimal;

        // Exact match first
        $exactMatch = $candidates->first(function ($req) use ($txAmount) {
            return abs((float) $req->expected_amount - $txAmount) < 0.00000001;
        });

        if ($exactMatch) {
            return $exactMatch;
        }

        // Tolerance match (e.g. they sent slightly more or less due to exchange rates)
        $toleranceMatch = $candidates->first(function ($req) use ($txAmount) {
            return $txAmount >= (float) $req->amount_min && $txAmount <= (float) $req->amount_max;
        });

        return $toleranceMatch;
    }

    /**
     * Generate a unique expected amount for a new payment request to avoid collisions.
     * We add a tiny amount to the base crypto amount.
     */
    public static function perturbAmount(float $baseAmount, int $decimals = 8): float
    {
        // Add between 1 and 99 in the lowest visible decimal to make it unique
        // Example: 0.05 BTC -> 0.05000014
        $perturbation = rand(1, 99) / pow(10, $decimals);
        return round($baseAmount + $perturbation, $decimals);
    }
}
