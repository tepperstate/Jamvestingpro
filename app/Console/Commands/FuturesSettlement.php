<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\FuturesPosition;
use Illuminate\Console\Command;

class FuturesSettlement extends Command
{
    protected $signature = 'futures:settle';

    protected $description = 'Cron command to settle expired futures positions';

    public function handle()
    {
        $positions = FuturesPosition::where('status', 'open')
            ->whereNotNull('expire_date')
            ->where('expire_date', '<=', now())
            ->get();

        foreach ($positions as $position) {
            $balanceColumn = $position->is_demo ? 'demo' : 'amount';
            $balance = Balance::where('user_id', $position->user_id)->where('symbol', 'USD')->first();

            if (! $balance) {
                continue;
            }

            if ($position->admin_status === 'win') {
                $balance->increment($balanceColumn, $position->margin_amount + $position->realized_pnl);
                $position->update(['status' => 'closed']);
            } elseif ($position->admin_status === 'loss') {
                // partial return
                $returnAmount = max(0, $position->margin_amount - abs($position->realized_pnl));
                $balance->increment($balanceColumn, $returnAmount);
                $position->update(['status' => 'closed']);
            } elseif ($position->admin_status === 'liquidated') {
                $position->update(['status' => 'liquidated']);
            } else {
                // Default handling
                $balance->increment($balanceColumn, $position->margin_amount + $position->realized_pnl);
                $position->update(['status' => 'closed']);
            }
        }

        $this->info('Futures settled.');
    }
}
