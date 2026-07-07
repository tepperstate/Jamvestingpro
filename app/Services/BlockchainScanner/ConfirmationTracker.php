<?php

namespace App\Services\BlockchainScanner;

use App\Enums\PaymentRequestStatus;
use App\Models\BlockchainTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfirmationTracker
{
    /**
     * Re-check unconfirmed transactions to see if they've reached the threshold.
     */
    public function trackUnconfirmed(): void
    {
        $unconfirmedTxs = BlockchainTransaction::where('is_confirmed', false)
            ->whereNotNull('matched_payment_id') // We only care about matching txs
            ->get();

        foreach ($unconfirmedTxs as $tx) {
            try {
                $scanner = ScannerFactory::make($tx->blockchain);
                $currentConfirmations = $scanner->getConfirmations($tx->tx_hash);

                if ($currentConfirmations > $tx->confirmations) {
                    $tx->confirmations = $currentConfirmations;
                    
                    if ($currentConfirmations >= $tx->required_confirmations) {
                        $tx->is_confirmed = true;
                        $tx->confirmed_at = now();
                        
                        // Update the payment request
                        if ($tx->matchedPayment) {
                            $payment = $tx->matchedPayment;
                            
                            if ($payment->status !== PaymentRequestStatus::CONFIRMED->value) {
                                $payment->update([
                                    'status' => PaymentRequestStatus::CONFIRMED->value,
                                    'confirmed_at' => now(),
                                ]);
                                
                                // Create legacy Deposit record for history
                                \App\Models\Deposit::create([
                                    'user_id' => $payment->user_id,
                                    'pay_currency' => $payment->currency,
                                    'trx_id' => $tx->tx_hash, // Use tx_hash as the trx_id
                                    'name' => 'Auto Crypto Deposit',
                                    'amount' => $payment->amount_usd,
                                    'status' => 'success',
                                ]);

                                // Credit user balance
                                \App\Services\HistoryBalanceService::recalculateDeposit(
                                    $payment->user_id, 
                                    'pending', 
                                    'success', 
                                    0, 
                                    $payment->amount_usd
                                );
                                
                                // Send Notification
                                $user = \App\Models\User::find($payment->user_id);
                                if ($user) {
                                    $text = [
                                        'greeting' => $user->first_name,
                                        'subject' => 'Deposit Confirmed',
                                        'body' => "Your automated deposit of {$payment->expected_amount} {$payment->currency} (\${$payment->amount_usd}) has been confirmed and credited to your account.",
                                        'data' => 'balance',
                                        'url' => url('/dashboard'),
                                        'thanks' => 'Thank you for choosing ' . env('APP_NAME'),
                                    ];
                                    try {
                                        \Illuminate\Support\Facades\Notification::route('mail', $user->email)
                                            ->notify(new \App\Notifications\DepositNotification($text));
                                    } catch (\Exception $e) {
                                        Log::error("Failed to send deposit notification: " . $e->getMessage());
                                    }
                                }
                            }
                        }
                    }
                    
                    $tx->save();
                }
            } catch (\Exception $e) {
                Log::error("ConfirmationTracker failed for {$tx->tx_hash}: " . $e->getMessage());
            }
        }
    }
}
