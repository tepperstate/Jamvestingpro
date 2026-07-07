<?php

namespace App\Console\Commands;

use App\Models\DualInvestmentProduct;
use App\Models\DualInvestmentSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DualInvestmentSettle extends Command
{
    protected $signature = 'daily:dual-settle';

    protected $description = 'Settles expired dual investment products';

    public function handle()
    {
        $products = DualInvestmentProduct::where('status', 'active')
            ->where('settlement_date', '<=', Carbon::now())
            ->get();

        foreach ($products as $product) {
            if (! $product->settlement_price) {
                $product->settlement_price = $product->strike_price * (1 + (mt_rand(-500, 500) / 10000));
            }

            $product->status = 'settled';
            $product->save();

            $subscriptions = DualInvestmentSubscription::where('dual_product_id', $product->id)
                ->where('status', 'active')
                ->get();

            foreach ($subscriptions as $sub) {
                $won = false;
                if ($product->direction === 'up' && $product->settlement_price >= $product->strike_price) {
                    $won = true;
                } elseif ($product->direction === 'down' && $product->settlement_price <= $product->strike_price) {
                    $won = true;
                }

                if ($sub->admin_status) {
                    $won = ($sub->admin_status === 'won');
                }

                if ($won) {
                    $sub->actual_return = $sub->expected_return;
                    $sub->admin_status = 'won';
                } else {
                    $sub->actual_return = 0;
                    $sub->admin_status = 'lost';
                }

                $sub->status = 'settled';
                $sub->save();
            }
        }
        $this->info('Dual Investment products settled.');
    }
}
