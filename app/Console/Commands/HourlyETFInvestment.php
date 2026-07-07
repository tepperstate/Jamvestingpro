<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Investment_history;
use App\Models\Noti;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HourlyETFInvestment extends Command
{
    protected $signature = 'hourly:etf-investment';

    protected $description = 'Process hourly compounding yield for all active Investment Packages';

    public function handle()
    {
        // Get all active investments (Growth Plans and ETFs)
        $activeInvestments = Investment_history::where('status', 'active')->get();

        $now = now();

        foreach ($activeInvestments as $investment) {
            // Check if we already credited this hour
            if ($investment->last_credited_date && Carbon::parse($investment->last_credited_date)->isCurrentHour()) {
                continue;
            }

            $user = $investment->user;
            if (! $user) {
                continue;
            }

            $initialAmount = floatval($investment->amount);
            $currentValue = $investment->current_value ? floatval($investment->current_value) : $initialAmount;

            $perc = floatval($investment->perc); // Total percentage for the term
            $days = intval($investment->day);

            if ($days <= 0) {
                continue;
            }

            // Total hours for the term
            $totalHours = $days * 24;

            // Compounding rate per hour based on the total expected percentage over the term
            // Final Value = P * (1 + r/100)
            // Final Value = P * (1 + hourly_rate) ^ totalHours
            // (1 + hourly_rate) = (1 + perc/100)^(1/totalHours)
            $termMultiplier = 1 + ($perc / 100);
            $hourlyRate = pow($termMultiplier, 1 / $totalHours) - 1;

            $hourlyProfit = $currentValue * $hourlyRate;
            $newCurrentValue = $currentValue + $hourlyProfit;

            DB::transaction(function () use ($investment, $user, $newCurrentValue, $now) {

                $investment->update([
                    'current_value' => $newCurrentValue,
                    'last_credited_date' => $now->format('Y-m-d H:i:s'),
                ]);

                // Check if term is over
                if ($now->greaterThanOrEqualTo(Carbon::parse($investment->end_date))) {
                    // Return Principal + Compounded Profit to the user's wallet
                    $balance = Balance::where('user_id', $user->id)->where('symbol', 'USD')->first();
                    if ($balance) {
                        $balance->increment('amount', $newCurrentValue);
                    }

                    $investment->update(['status' => 'completed']);

                    Noti::create([
                        'user_id' => $user->id,
                        'title' => 'Investment Plan Matured',
                        'message' => 'Your investment in '.($investment->plan_name ?? $investment->name).' has completed. A total of $'.number_format($newCurrentValue, 2).' (Initial: $'.number_format($investment->amount, 2).') has been returned to your wallet.',
                        'status' => 'unread',
                    ]);
                }
            });

            $this->info("Processed hourly compounding for User ID {$user->id} Plan: +$".number_format($hourlyProfit, 2));
        }

        return Command::SUCCESS;
    }
}
