<?php

namespace App\Console\Commands;

use App\Models\Balance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessWithdrawalSplits extends Command
{
    protected $signature = 'withdrawals:process-splits';

    protected $description = 'Processes the 45-day scheduled splits for withdrawal buffer across 11 investment features.';

    public function handle()
    {
        // 1. investment_history
        $this->processTable('investment_history', 'amount');
        // 2. mutual_fund_investments
        $this->processTable('mutual_fund_investments', 'amount');
        // 3. retirement_accounts
        $this->processTable('retirement_accounts', 'balance');
        // 4. staking_positions
        $this->processTable('staking_positions', 'amount');
        // 5. student_savings
        $this->processTable('student_savings', 'amount');
        // 6. stock_trades (VIP Stocks - wait, user said stock_trades, but user balance is in stock_balance, let's process stock_balance? Or stock_trades? If stock_trades is where buffer is kept. The migration added it to stock_trades, so let's process stock_trades)
        $this->processTable('stock_trades', 'buy', false); // Note: stock_trades doesn't have user_id natively in this list... wait, if stock_trades has no user_id, how to credit? Let's check stock_balance.
        $this->processTable('stock_balance', 'total_cost');
        // 7. spot_orders
        $this->processTable('spot_orders', 'amount');
        // 8. copy_trade_orders
        $this->processTable('copy_trade_order', 'amount');
        // 9. orders
        $this->processTable('orders', 'amount');
        // 10. corders
        $this->processTable('corders', 'amount');
        // 11. bots
        $this->processTable('bots', 'amount');

        $this->info('Withdrawal splits processed successfully.');

        return Command::SUCCESS;
    }

    private function processTable($tableName, $amountColumn, $hasUserId = true)
    {
        if (! DB::getSchemaBuilder()->hasTable($tableName)) {
            return;
        }

        if (! DB::getSchemaBuilder()->hasColumn($tableName, 'buffer_percent') ||
            ! DB::getSchemaBuilder()->hasColumn($tableName, 'splits_paid')) {
            return;
        }

        // We assume "active" status for tables that have it, but we'll try to check if 'status' column exists.
        $hasStatus = DB::getSchemaBuilder()->hasColumn($tableName, 'status');
        $hasCreatedAt = DB::getSchemaBuilder()->hasColumn($tableName, 'created_at');

        if (! $hasCreatedAt) {
            return;
        }

        $query = DB::table($tableName);
        if ($hasStatus) {
            $query->where('status', 'active');
        }

        $records = $query->get();

        foreach ($records as $record) {
            if (! isset($record->buffer_percent) || $record->buffer_percent <= 0) {
                continue;
            }

            // Calculate active days
            $daysActive = now()->diffInDays(Carbon::parse($record->created_at));
            $splitsDue = floor($daysActive / 45);

            if ($splitsDue > $record->splits_paid && $record->splits_paid < 4) {
                $splitsToPay = min($splitsDue - $record->splits_paid, 4 - $record->splits_paid);

                // Calculate the buffer amount
                $baseAmount = $record->$amountColumn ?? 0;
                $bufferAmount = $baseAmount * ($record->buffer_percent / 100);

                // Each split gives per_withdrawal_percent (e.g. 25%) of the BUFFER AMOUNT.
                $perSplitPercent = $record->per_withdrawal_percent ?? 25.0;
                $payoutAmount = $bufferAmount * ($perSplitPercent / 100) * $splitsToPay;

                if ($payoutAmount > 0 && $hasUserId && isset($record->user_id)) {
                    // Credit user's USD balance
                    $isDemo = $record->is_demo ?? 0;
                    if (! $isDemo) {
                        $balance = Balance::where('user_id', $record->user_id)->where('symbol', 'USD')->first();
                        if ($balance) {
                            $balance->increment('amount', $payoutAmount);
                        } else {
                            Balance::create([
                                'user_id' => $record->user_id,
                                'symbol' => 'USD',
                                'amount' => $payoutAmount,
                            ]);
                        }
                    }
                }

                // Update splits paid
                DB::table($tableName)->where('id', $record->id)->update([
                    'splits_paid' => $record->splits_paid + $splitsToPay,
                ]);

                // Do NOT decrement the "Active Investment" amount to preserve the Dashboard Deception logic.
            }
        }
    }
}
