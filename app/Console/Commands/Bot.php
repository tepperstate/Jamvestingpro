<?php

namespace App\Console\Commands;

use App\Models\Balance;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Bot extends Command
{
    protected $signature = 'daily:bot';

    protected $description = 'Command description';

    public function handle()
    {
        $bot_result = DB::table('botresults')->get();
        $today = date('Y-m-d H:i:s');

        foreach ($bot_result as $bot) {
            $user = $bot->user_id;
            $bot_id = $bot->bot_id;
            $bot_name = $bot->name;
            $symbol = $bot->symbol;
            $amount = $bot->amount;
            $duration = $bot->day;
            $type = $bot->type;
            // $total       = $bot->total;
            $no_of_win = $bot->win;
            $no_of_loss = $bot->loss;
            // $expire_date = $bot->expire_time;

            $check = DB::table('bot_generated_result')->where('bot_id', $bot_id)->whereDate('created_at', Carbon::today())->get();

            if (count($check) < $duration) {
                $table = '';
                $forex = DB::table('forexs')->where('symbols', 'like', $symbol)->get()->first();
                $crypto = DB::table('cryptos')->where('symbols', 'like', $symbol)->get()->first();
                $stock = DB::table('stocks')->where('symbols', 'like', $symbol)->get()->first();

                if ($forex) {
                    $table = $forex;
                } elseif ($crypto) {
                    $table = $crypto;
                } else {
                    $table = $stock;
                }
                $percentage = $table->percentage;

                $divided_amount = ($percentage / 100) * $amount;

                $win_loss = $amount + $divided_amount;

                $check_for_win = DB::table('bot_generated_result')->where('bot_id', $bot_id)->where('status', 'win')->whereDate('created_at', Carbon::today())->get();
                $check_for_loss = DB::table('bot_generated_result')->where('bot_id', $bot_id)->where('status', 'loss')->whereDate('created_at', Carbon::today())->get();

                $isDemo = $bot->is_demo;
                $balanceColumn = $isDemo ? 'demo' : 'amount';
                $balance = Balance::whereUserId($user)->where('symbol', 'USD')->first();

                // Auto-renew deduction for a new day
                if ($bot->is_auto_renew && ! Carbon::parse($bot->created_at)->isToday() && count($check) == 0) {
                    if (! $balance || $amount > $balance->$balanceColumn) {
                        DB::table('botresults')->where('id', $bot->id)->delete();

                        continue;
                    }
                    Balance::whereUserId($user)->where('symbol', 'USD')->decrement($balanceColumn, $amount);
                    DB::table('botresults')->where('id', $bot->id)->update(['created_at' => Carbon::now()]);
                }

                if (! $balance || $amount > $balance->$balanceColumn) {
                    DB::table('botresults')->where('id', $bot->id)->delete();
                } else {
                    if (count($check_for_win) == $no_of_win) {
                        Balance::whereUserId($user)->where('symbol', 'USD')->increment($balanceColumn, $win_loss);

                        DB::table('bot_generated_result')->insert([
                            'user_id' => $user,
                            'bot_id' => $bot_id,
                            'name' => $bot_name,
                            'symbol' => $symbol,
                            'amount' => $amount,
                            'type' => $type,
                            'status' => 'win',
                            'win' => '',
                            'profit' => $win_loss,
                            'is_demo' => $isDemo,
                            'created_at' => Carbon::now(),
                        ]);
                    } elseif (count($check_for_loss) == $no_of_loss) {
                        DB::table('bot_generated_result')->insert([
                            'user_id' => $user,
                            'bot_id' => $bot_id,
                            'name' => $bot_name,
                            'symbol' => $symbol,
                            'amount' => $amount,
                            'type' => $type,
                            'status' => 'loss',
                            'win' => '',
                            'profit' => 0,
                            'is_demo' => $isDemo,
                            'created_at' => Carbon::now(),
                        ]);
                    } else {
                        DB::table('bot_generated_result')->insert([
                            'user_id' => $user,
                            'bot_id' => $bot_id,
                            'name' => $bot_name,
                            'symbol' => $symbol,
                            'amount' => $amount,
                            'type' => $type,
                            'status' => 'loss',
                            'win' => '',
                            'profit' => 0,
                            'created_at' => Carbon::now(),
                        ]);
                    }
                }

            } else {
                if (! $bot->is_auto_renew) {
                    DB::table('botresults')->where('id', $bot->id)->delete();
                }
            }

        }

        return true;
    }
}
