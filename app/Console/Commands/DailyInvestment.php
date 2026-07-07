<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Investment_history;
use App\Models\Noti;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DailyInvestment extends Command
{
    protected $signature = 'daily:investment';

    protected $description = 'Process daily growth for active Investment Packages';

    public function handle()
    {
        // Exclude ETF plans (7-12) as they are handled by HourlyETFInvestment
        $activeInvestments = Investment_history::where('status', 'active')
            ->whereNotBetween('plan_id', [7, 12])
            ->get();
        $now = now();

        foreach ($activeInvestments as $investment) {
            // Check if we already credited today
            if ($investment->last_credited_date && Carbon::parse($investment->last_credited_date)->isToday()) {
                continue;
            }

            $user = $investment->user;
            if (! $user) {
                continue;
            }

            $amount = floatval($investment->amount);
            $perc = floatval($investment->perc); // Total percentage for the term
            $days = intval($investment->day);

            if ($days <= 0) {
                continue;
            }

            // Calculate daily profit: (Total Profit) / Duration
            $dailyProfit = ($amount * ($perc / 100)) / $days;

            DB::transaction(function () use ($investment, $user, $dailyProfit, $now) {
                // Add profit to USD balance
                $balance = Balance::where('user_id', $user->id)->where('symbol', 'USD')->first();
                if ($balance) {
                    $balance->increment('amount', $dailyProfit);
                }

                $investment->update([
                    'last_credited_date' => $now->format('Y-m-d H:i:s'),
                ]);

                // Check if term is over
                if ($now->greaterThanOrEqualTo(Carbon::parse($investment->end_date))) {
                    // Return Principal
                    if ($balance) {
                        $balance->increment('amount', $investment->amount);
                    }

                    $investment->update(['status' => 'completed']);

                    Noti::create([
                        'user_id' => $user->id,
                        'title' => 'Growth Plan Matured',
                        'message' => 'Your investment in '.$investment->name.' has completed. Principal of $'.number_format($investment->amount, 2).' has been returned to your wallet.',
                        'status' => 'unread',
                    ]);
                }
            });

            $this->info("Processed growth for User ID {$user->id}: +$".number_format($dailyProfit, 2));
        }

        return Command::SUCCESS;
    }
}
