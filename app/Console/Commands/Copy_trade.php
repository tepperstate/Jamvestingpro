<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Copy_trade_order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class Copy_trade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trade:copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            Artisan::call('forex:price');
            Artisan::call('crypto:price');
            Artisan::call('stock:price');
            Artisan::call('stocks:price');
        } catch (\Throwable $th) {
            // throw $th;
        }

        $orders = Copy_trade_order::whereStatus('pending')->get();
        $today = date('Y-m-d H:i:s');

        foreach ($orders as $order) {
            $id = $order->id;
            $user = $order->user_id;
            $amount = $order->amount;        // Capital amount
            $win = $order->win;           // Win percentage
            $loss = $order->loss;          // Loss percentage
            $symbol = $order->symbol;
            $expire_date = $order->expire_date;
            $admin_status = $order->admin_status;

            $isDemo = $order->is_demo;
            $balanceColumn = $isDemo ? 'demo' : 'amount';

            // Calculate profit and loss amounts
            $profit_amount = ($win / 100) * $amount;
            $p_l_win = $amount + $profit_amount;  // Capital + Profit
            $loss_amount = ($loss / 100) * $amount;
            $p_l_loss = $amount + $loss_amount;    // Capital + Loss

            // Check if trade has expired
            if ($today > $expire_date) {
                // Fetch asset directly to check symbol association and avoid N+1 query
                $asset = DB::table('assets')->where('symbols', $symbol)->first();

                if ($asset) {
                    if ($admin_status === 'win') {
                        // Win Case: Credit only the profit and update p_l with capital + profit
                        Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $profit_amount);

                        Copy_trade_order::whereUserId($user)
                            ->whereSymbol($symbol)
                            ->whereId($id)
                            ->update([
                                'status' => 'win',
                                'p_l' => $p_l_win,  // Capital + Profit
                                'modal' => 'open',
                            ]);
                    } elseif ($admin_status === 'loss') {
                        // Loss Case: Deduct only the loss amount and update p_l with capital + loss
                        Balance::whereUserId($user)
                            ->where('symbol', 'USD')
                            ->decrement($balanceColumn, $loss_amount);

                        Copy_trade_order::whereUserId($user)
                            ->whereSymbol($symbol)
                            ->whereId($id)
                            ->update([
                                'status' => 'loss',
                                'p_l' => $p_l_loss,  // Capital + Loss
                                'modal' => 'open',
                            ]);
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
