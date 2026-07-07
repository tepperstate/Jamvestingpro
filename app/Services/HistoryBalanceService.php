<?php

namespace App\Services;

use App\Models\Balance;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Withdrawal;

class HistoryBalanceService
{
    /**
     * Recalculate balance when a trade status is changed.
     * Handles: pending->win, pending->loss, win->loss, loss->win, etc.
     */
    public static function recalculateTrade(Order $order, string $oldStatus, string $newStatus, float $oldAmount, float $newAmount)
    {
        $userId = $order->user_id;
        $balanceField = $order->is_demo ? 'demo' : 'amount';
        $winPerc = $order->win ?? 90;
        $lossPerc = $order->loss ?? 0;

        // Reverse the old status effect
        self::reverseTradeEffect($userId, $balanceField, $oldStatus, $oldAmount, $winPerc, $lossPerc);

        // Apply the new status effect
        self::applyTradeEffect($userId, $balanceField, $newStatus, $newAmount, $winPerc, $lossPerc);
    }

    private static function reverseTradeEffect(int $userId, string $balanceField, string $status, float $amount, float $winPerc, float $lossPerc)
    {
        switch ($status) {
            case 'win':
                $profitAmount = ($winPerc / 100) * $amount;
                $winTotal = $amount + $profitAmount;
                Balance::where('user_id', $userId)->where('symbol', 'USD')->decrement($balanceField, $winTotal);
                break;
            case 'loss':
                $lossReturn = ($lossPerc / 100) * $amount;
                Balance::where('user_id', $userId)->where('symbol', 'USD')->decrement($balanceField, $lossReturn);
                break;
            case 'draw':
                Balance::where('user_id', $userId)->where('symbol', 'USD')->decrement($balanceField, $amount);
                break;
            case 'pending':
                // Pending trades already had their amount deducted at creation, no reversal needed for balance
                break;
        }
    }

    private static function applyTradeEffect(int $userId, string $balanceField, string $status, float $amount, float $winPerc, float $lossPerc)
    {
        switch ($status) {
            case 'win':
                $profitAmount = ($winPerc / 100) * $amount;
                $winTotal = $amount + $profitAmount;
                Balance::where('user_id', $userId)->where('symbol', 'USD')->increment($balanceField, $winTotal);
                break;
            case 'loss':
                $lossReturn = ($lossPerc / 100) * $amount;
                Balance::where('user_id', $userId)->where('symbol', 'USD')->increment($balanceField, $lossReturn);
                break;
            case 'draw':
                Balance::where('user_id', $userId)->where('symbol', 'USD')->increment($balanceField, $amount);
                break;
            case 'pending':
                // Pending status means amount is locked, no credit
                break;
        }
    }

    /**
     * Recalculate balance when a deposit is modified.
     */
    public static function recalculateDeposit(int $userId, string $oldStatus, string $newStatus, float $oldAmount, float $newAmount, string $symbol = 'USD')
    {
        // Reverse old deposit credit
        if ($oldStatus === 'success') {
            Balance::where('user_id', $userId)->where('symbol', $symbol)->decrement('amount', $oldAmount);
        }

        // Apply new deposit credit
        if ($newStatus === 'success') {
            Balance::where('user_id', $userId)->where('symbol', $symbol)->increment('amount', $newAmount);
        }
    }

    /**
     * Recalculate balance when a withdrawal is modified.
     */
    public static function recalculateWithdrawal(int $userId, string $oldStatus, string $newStatus, float $oldAmount, float $newAmount)
    {
        // Reverse old withdrawal effect
        if ($oldStatus === 'confirmed' || $oldStatus === 'success') {
            // Withdrawal was processed, money was gone — give it back
            Balance::where('user_id', $userId)->where('symbol', 'USD')->increment('amount', $oldAmount);
        } elseif ($oldStatus === 'reversed') {
            // Reversal already returned money — take it back
            Balance::where('user_id', $userId)->where('symbol', 'USD')->decrement('amount', $oldAmount);
        }

        // Apply new withdrawal effect
        if ($newStatus === 'confirmed' || $newStatus === 'success') {
            Balance::where('user_id', $userId)->where('symbol', 'USD')->decrement('amount', $newAmount);
        } elseif ($newStatus === 'reversed') {
            Balance::where('user_id', $userId)->where('symbol', 'USD')->increment('amount', $newAmount);
        }
    }
}
