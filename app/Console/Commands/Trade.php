<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Trade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:trade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'daily:trade';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $order = Order::where('status', 'pending')
            ->where('expire_date', '<=', date('Y-m-d H:i:s'))
            ->get();
        $today = date('Y-m-d H:i:s');

        foreach ($order as $val) {
            $id = $val->id;
            $user = $val->user_id;
            $amount = $val->amount;
            $win = $val->win;
            $loss = $val->loss;
            $symbol = $val->symbol;
            $mode = $val->types;
            $expire_date = $val->expire_date;
            $admin_status = $val->admin_status;
            $isDemo = $val->is_demo;
            $balanceColumn = $isDemo ? 'demo' : 'amount';

            $divided_amount = ($win / 100) * $amount;
            $win_loss = $amount + $divided_amount;
            $divided_amounts = ($loss / 100) * $amount;
            $loss_amount = $divided_amounts;
            $loss_data = $amount - $divided_amounts;

            if ($today > $expire_date) {

                $assets = DB::table('assets')->get();
                $signal = DB::table('generate_signal')->get();
                $signal_matched = false;

                // Admin override takes priority
                if ($admin_status === 'win') {
                    Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $win_loss);
                    Order::whereUserId($user)->whereSymbol($symbol)->whereId($id)->update(['status' => 'win', 'p_l' => $win_loss, 'modal' => 'open']);
                } elseif ($admin_status === 'loss') {
                    Order::whereUserId($user)->whereSymbol($symbol)->whereId($id)->update(['status' => 'loss', 'p_l' => $loss_amount, 'modal' => 'open']);
                    Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $loss_data);
                } elseif ($admin_status === 'draw') {
                    Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $amount);
                    Order::whereUserId($user)->whereSymbol($symbol)->whereId($id)->update(['status' => 'draw', 'p_l' => 0, 'modal' => 'open']);
                } elseif ($val->outcome_preset) {
                    if ($val->outcome_preset === 'win') {
                        Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $win_loss);
                        Order::whereUserId($user)->whereSymbol($symbol)->whereId($id)->update(['status' => 'win', 'p_l' => $win_loss, 'modal' => 'open']);
                    } elseif ($val->outcome_preset === 'draw') {
                        Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $amount);
                        Order::whereUserId($user)->whereSymbol($symbol)->whereId($id)->update(['status' => 'draw', 'p_l' => 0, 'modal' => 'open']);
                    } else {
                        Order::whereUserId($user)->whereSymbol($symbol)->whereId($id)->update(['status' => 'loss', 'p_l' => $loss_amount, 'modal' => 'open']);
                        Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $loss_data);
                    }
                } else {
                    // Check against generate_signal table
                    if ($signal->count() > 0) {
                        foreach ($signal as $sig) {
                            $signal_symbol = $sig->symbols;
                            $profit = $sig->profits;

                            if ($symbol == $signal_symbol) {
                                $signal_matched = true;
                                if ($profit === 'win') {
                                    Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $win_loss);
                                    Order::whereUserId($user)->whereSymbol($symbol)->whereId($id)->update(['status' => 'win', 'p_l' => $win_loss, 'modal' => 'open']);
                                } else {
                                    Order::whereUserId($user)->whereSymbol($symbol)->whereId($id)->update(['status' => 'loss', 'p_l' => $loss_amount, 'modal' => 'open']);
                                    Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $loss_data);
                                }
                                break; // Exit loop as match is found
                            }
                        }
                    }

                    // If no match in generate_signal, fallback to assets table
                    if (! $signal_matched) {
                        foreach ($assets as $data) {
                            $symbols = $data->symbols;
                            $profit = $data->profits;

                            if ($symbols == $symbol) {
                                // Randomized trade result as fallback
                                $ran = rand(1, 10);
                                if ($ran === 2) {
                                    Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $win_loss);
                                    Order::whereUserId($user)->whereSymbol($symbol)->whereId($id)->update(['status' => 'win', 'p_l' => $win_loss, 'modal' => 'open']);
                                } else {
                                    Order::whereUserId($user)->whereSymbol($symbol)->whereId($id)->update(['status' => 'loss', 'p_l' => $loss_amount, 'modal' => 'open']);
                                    Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $loss_data);
                                }
                            }
                        }
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
