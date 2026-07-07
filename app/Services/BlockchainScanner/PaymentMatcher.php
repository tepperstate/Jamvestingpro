<?php

namespace App\Services\BlockchainScanner;

use App\Models\BlockchainTransaction;
use App\Models\PendingPaymentRequest;
use App\Enums\PaymentRequestStatus;
use App\Enums\TransactionMatchStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentMatcher
{
    /**
     * Try to match a newly discovered transaction to a pending payment request.
     */
    public function matchTransaction(BlockchainTransaction $tx): void
    {
        if ($tx->is_processed || $tx->match_status !== TransactionMatchStatus::UNMATCHED) {
            return;
        }

        // Find all pending requests for this deposit address and currency
        $pendingRequests = PendingPaymentRequest::where('deposit_address', $tx->to_address)
            ->where('currency', $tx->currency)
            ->whereIn('status', [PaymentRequestStatus::PENDING, PaymentRequestStatus::SCANNING])
            ->get();

        if ($pendingRequests->isEmpty()) {
            $tx->update(['match_status' => TransactionMatchStatus::NO_MATCH->value]);
            return;
        }

        // Use AmountMatcher to find the best match
        $bestMatch = app(AmountMatcher::class)->findBestMatch($tx, $pendingRequests);

        if ($bestMatch) {
            DB::transaction(function () use ($tx, $bestMatch) {
                // Link them
                $tx->update([
                    'matched_payment_id' => $bestMatch->id,
                    'match_status' => TransactionMatchStatus::AUTO_MATCHED->value,
                    'match_confidence' => 100.0,
                    'is_processed' => true,
                ]);

                // Update the payment request
                $bestMatch->update([
                    'status' => $tx->is_confirmed ? PaymentRequestStatus::CONFIRMED->value : PaymentRequestStatus::CONFIRMING->value,
                    'matched_transaction_id' => $tx->id,
                    'matched_at' => now(),
                    'confirmed_at' => $tx->is_confirmed ? now() : null,
                ]);
                
                // If confirmed, you could trigger an event here to credit the user's wallet
                // event(new PaymentConfirmed($bestMatch));
            });
        } else {
            // Check if there is an amount mismatch (someone paid wrong amount)
            // Or maybe multiple candidates exist. We just mark it NO_MATCH for manual review.
            $tx->update(['match_status' => TransactionMatchStatus::NO_MATCH->value]);
        }
    }
}
