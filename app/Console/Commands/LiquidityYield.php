<?php

namespace App\Console\Commands;

use App\Models\LiquidityPosition;
use Illuminate\Console\Command;

class LiquidityYield extends Command
{
    protected $signature = 'hourly:liquidity-yield';

    protected $description = 'Accrues yield for liquidity pool positions';

    public function handle()
    {
        $positions = LiquidityPosition::where('status', 'active')->with('pool')->get();
        foreach ($positions as $position) {
            $apy = $position->pool->apy ?? 0;
            // hourly yield calculation
            $hourlyYield = ($apy / 100) / (365 * 24);
            $reward = $position->amount_deposited * $hourlyYield;

            $position->earned_rewards += $reward;
            $position->current_value = $position->amount_deposited + $position->earned_fees + $position->earned_rewards;
            $position->save();
        }
        $this->info('Liquidity yield accrued successfully.');
    }
}
