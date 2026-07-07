<?php

namespace App\Console\Commands;

use App\Models\DcaExecution;
use App\Models\DcaSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DcaExecute extends Command
{
    protected $signature = 'hourly:dca-execute';

    protected $description = 'Executes DCA bot purchases';

    public function handle()
    {
        $subscriptions = DcaSubscription::where('status', 'active')
            ->where('next_execution', '<=', Carbon::now())
            ->get();

        foreach ($subscriptions as $sub) {
            $plan = $sub->plan;
            if (! $plan) {
                continue;
            }

            $marketPrice = 50000; // Fake fallback market price

            $spread = $plan->spread_markup ?? 0.02;
            $executionPrice = $sub->admin_price_override ?? ($marketPrice * (1 + $spread));

            $amountUsd = $sub->amount_per_purchase;
            $units = $amountUsd / max($executionPrice, 0.0001);

            DcaExecution::create([
                'dca_subscription_id' => $sub->id,
                'amount_usd' => $amountUsd,
                'units_acquired' => $units,
                'execution_price' => $executionPrice,
                'market_price' => $marketPrice,
                'spread_charged' => $executionPrice - $marketPrice,
                'status' => 'executed',
                'executed_at' => Carbon::now(),
            ]);

            $sub->total_invested += $amountUsd;
            $sub->total_units_acquired += $units;
            $sub->avg_purchase_price = $sub->total_invested / max($sub->total_units_acquired, 0.00000001);

            $sub->executions_completed += 1;
            $sub->current_value = $sub->total_units_acquired * $marketPrice;
            $sub->unrealized_pnl = $sub->current_value - $sub->total_invested;

            if ($sub->admin_status) {
                if ($sub->admin_status == 'profitable') {
                    $sub->current_value = $sub->total_invested * 1.1;
                    $sub->unrealized_pnl = $sub->current_value - $sub->total_invested;
                } elseif ($sub->admin_status == 'loss') {
                    $sub->current_value = $sub->total_invested * 0.9;
                    $sub->unrealized_pnl = $sub->current_value - $sub->total_invested;
                }
            }

            if ($plan->frequency == 'daily') {
                $sub->next_execution = Carbon::now()->addDay()->setHour($plan->execution_hour);
            } elseif ($plan->frequency == 'weekly') {
                $sub->next_execution = Carbon::now()->addWeek()->setHour($plan->execution_hour);
            } elseif ($plan->frequency == 'biweekly') {
                $sub->next_execution = Carbon::now()->addWeeks(2)->setHour($plan->execution_hour);
            } elseif ($plan->frequency == 'monthly') {
                $sub->next_execution = Carbon::now()->addMonth()->setHour($plan->execution_hour);
            }

            $sub->save();
        }

        $this->info('DCA executed.');
    }
}
