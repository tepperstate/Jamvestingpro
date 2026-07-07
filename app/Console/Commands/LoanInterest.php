<?php

namespace App\Console\Commands;

use App\Models\LoanPosition;
use Illuminate\Console\Command;

class LoanInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:loan-interest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accrues daily interest, checks LTV, and triggers liquidations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $positions = LoanPosition::with('plan')->where('status', 'active')->get();

        foreach ($positions as $position) {
            $plan = $position->plan;
            if (! $plan) {
                continue;
            }

            // 1. Accrue Daily Interest
            if ($plan->interest_rate_daily > 0) {
                $interest = ($position->loan_amount * $plan->interest_rate_daily) / 100;
                $position->interest_accrued += $interest;
                $position->remaining_balance += $interest;
            }

            // 2. Check LTV and Liquidations
            if ($position->collateral_value > 0) {
                // Real-world logic would update collateral_value from oracle here.
                // We're calculating LTV based on the remaining balance and collateral value.
                $currentLtv = ($position->remaining_balance / $position->collateral_value) * 100;
                $position->current_ltv = $currentLtv;

                // Liquidate if current LTV exceeds liquidation threshold
                if ($currentLtv >= $plan->liquidation_ltv) {
                    $position->admin_status = 'liquidated';
                    $position->status = 'liquidated';
                    $this->info("Liquidated position #{$position->id}");
                } elseif ($currentLtv >= ($plan->max_ltv + 5)) {
                    $position->admin_status = 'margin_call';
                } else {
                    $position->admin_status = 'healthy';
                }
            }

            $position->save();
        }

        $this->info('Loan interest accrued and liquidations checked.');

        return 0;
    }
}
