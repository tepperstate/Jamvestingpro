<?php

namespace App\Console\Commands;

use App\Models\StakingPosition;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HourlyStaking extends Command
{
    protected $signature = 'hourly:staking';

    protected $description = 'Process hourly compounding yield for active Staking Positions';

    public function handle()
    {
        // Get all active staking positions
        $activePositions = StakingPosition::where('status', 'active')->with('plan')->get();
        $now = now();

        foreach ($activePositions as $position) {
            // Check if we already credited this hour
            if ($position->updated_at && Carbon::parse($position->updated_at)->isCurrentHour()) {
                continue;
            }

            $user = $position->user;
            if (! $user) {
                continue;
            }

            $initialAmount = floatval($position->amount);
            $earned = floatval($position->earned);
            $currentValue = $initialAmount + $earned;

            if (! $position->plan) {
                continue;
            }

            $apy = floatval($position->plan->apy_percentage); // e.g. 15.0 for 15%
            $days = intval($position->plan->lock_days);

            if ($days <= 0) {
                continue;
            }

            // Total hours for the term
            $totalHours = $days * 24;

            // Compounding rate per hour based on the total expected percentage over the term
            // Final Value = P * (1 + hourly_rate) ^ totalHours
            // (1 + hourly_rate) = (1 + apy/100)^(1/totalHours)
            $termMultiplier = 1 + ($apy / 100);
            $hourlyRate = pow($termMultiplier, 1 / $totalHours) - 1;

            $hourlyProfit = $currentValue * $hourlyRate;
            $newEarned = $earned + $hourlyProfit;

            DB::transaction(function () use ($position, $newEarned) {
                // We do NOT return principal here, because Staking finishes on Unstake click.
                // We just update the earned amount.
                $position->update([
                    'earned' => $newEarned,
                ]);
            });

            $this->info("Processed hourly compounding for User ID {$user->id} Staking: +$".number_format($hourlyProfit, 2));
        }

        return Command::SUCCESS;
    }
}
