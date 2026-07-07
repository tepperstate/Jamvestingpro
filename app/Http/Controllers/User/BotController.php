<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BotController extends Controller
{
    public function isMobileView()
    {
        return false;
    }

    public function bot_history()
    {
        $isDemo = auth()->user()->is_demo;
        $data = DB::table('bot_generated_result')
            ->where('user_id', auth()->guard('web')->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(20);

        $win = DB::table('bot_generated_result')
            ->where('user_id', auth()->guard('web')->user()->id)
            ->where('status', 'win')
            ->sum('profit');

        $loss = DB::table('bot_generated_result')
            ->where('user_id', auth()->guard('web')->user()->id)
            ->where('status', 'loss')
            ->sum('profit');

        $orderCount = $data->total();

        $viewName = $this->isMobileView() ? 'mobile.exchange.bot_history' : 'exchange.bot_history';

        return view($viewName, [
            'transactions' => $data,
            'win' => $win,
            'loss' => $loss,
            'orderCount' => $orderCount,
        ]);
    }

    public function bot() // boot index
    {$isDemo = auth()->user()->is_demo;
        $bots_data = DB::table('bots')
            ->leftJoin('purchase_bot', function ($join) use ($isDemo) {
                $join->on('bots.id', '=', 'purchase_bot.bot_id')
                    ->where('purchase_bot.user_id', '=', auth()->user()->id)
                    ->where('purchase_bot.is_demo', '=', $isDemo);
            })
            ->orderBy('bots.amount', 'asc')
            ->select(
                'bots.*',
                'purchase_bot.user_id as user_id'
            )
            ->paginate(12);

        $viewName = $this->isMobileView() ? 'mobile.exchange.bot' : 'exchange.bot';

        return view($viewName, [
            'bots' => $bots_data,
        ]);
    }

    public function buy_bot(Request $request) // buy bots
    {$isDemo = auth()->user()->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $checks = DB::table('purchase_bot')
            ->whereUserId(auth()->guard('web')->user()->id)
            ->where('bot_id', $request->id)
            ->where('is_demo', $isDemo)
            ->exists();

        $balance = Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->first();

        if ($checks) {
            if ($request->ajax()) {
                return response()->json(['status' => 'You already have this bot']);
            }

            return redirect()->route('bots.user');
        }

        if ($request->amount > $balance->$balanceColumn) {
            if ($request->ajax()) {
                return response()->json(['error' => "Sorry You Do don't have enough ".($isDemo ? 'demo ' : '').'fund']);
            }

            return back()->with('error', "Sorry You Do don't have enough ".($isDemo ? 'demo ' : '').'fund');
        } else {
            Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->decrement($balanceColumn, $request->amount);
            DB::table('purchase_bot')->insert([
                'user_id' => auth()->guard('web')->user()->id,
                'bot_id' => $request->id,
                'name' => $request->name,
                'amount' => $request->amount,
                'is_demo' => $isDemo,
            ]);

            if ($request->ajax()) {
                return response()->json(['status' => 'Bot purchase successful']);
            }

            return redirect()->route('bots.user')->with('status', 'Bot purchase successful');
        }
    }

    public function alluser_bot()  // all user bots
    {$isDemo = auth()->user()->is_demo;
        $my_bots_data = DB::table('purchase_bot')
            ->where('purchase_bot.user_id', auth()->guard('web')->user()->id)
            ->where('purchase_bot.is_demo', $isDemo)
            ->join('bots', 'purchase_bot.bot_id', '=', 'bots.id')
            ->select('purchase_bot.id as id', 'purchase_bot.bot_id as bot_id', 'bots.day as day', 'purchase_bot.name as name', 'purchase_bot.amount as amount', 'bots.min as min', 'bots.max as max', 'bots.image as image', 'bots.used as used', 'purchase_bot.bot_id as id', 'bots.buffer_percent as buffer_percent')->get();

        // Dashboard Deception: Inflate amount
        $my_bots_data->transform(function ($bot) {
            $buffer = $bot->buffer_percent ?? 20.0;
            $bot->amount = $bot->amount * (1 + ($buffer / 100));

            return $bot;
        });

        $viewName = $this->isMobileView() ? 'mobile.exchange.mybots' : 'exchange.mybots';

        return view($viewName, [
            'data' => $my_bots_data,
        ]);
    }

    public function user_bot($id) // single user bot
    {$user = auth()->guard('web')->user()->id;
        $isDemo = auth()->user()->is_demo;
        $user_bot = DB::table('botresults')->where(['bot_id' => $id, 'user_id' => auth()->guard('web')->user()->id])->first();

        $bot = DB::table('purchase_bot')->whereUserId($user)->where('purchase_bot.bot_id', $id)->where('is_demo', $isDemo)
            ->join('bots', 'purchase_bot.bot_id', '=', 'bots.id')
            ->select('purchase_bot.id as id', 'purchase_bot.bot_id as bot_id', 'bots.day as day', 'purchase_bot.name as name', 'purchase_bot.amount as amount', 'bots.min as min', 'bots.max as max', 'bots.buffer_percent as buffer_percent')->get();

        $bot->transform(function ($b) {
            $buffer = $b->buffer_percent ?? 20.0;
            $b->amount = $b->amount * (1 + ($buffer / 100));

            return $b;
        });

        $result = DB::table('bot_generated_result')->where(['user_id' => auth()->guard('web')->user()->id, 'bot_id' => $id])->orderBy('id', 'desc')->get();

        $data1 = DB::table('forexs')->orderBy('id', 'DESC')->get()->toArray();
        $data2 = DB::table('cryptos')->orderBy('id', 'DESC')->get()->toArray();
        $data3 = DB::table('stocks')->orderBy('id', 'DESC')->get()->toArray();

        $b = array_merge($data1, $data2, $data3);

        $viewName = $this->isMobileView() ? 'mobile.exchange.bot_user' : 'exchange.bot_user';

        return view($viewName, [
            'data' => $bot,
            'user_bot' => $user_bot,
            'b' => $b,
            'result' => $result,
        ]);
    }

    public function bot_result($id)
    {
        $isDemo = auth()->user()->is_demo;
        $result = DB::table('bot_generated_result')->where(['user_id' => auth()->guard('web')->user()->id, 'bot_id' => $id])->orderBy('id', 'desc')->limit(10)->get();

        return response([
            'status' => true,
            'data' => $result,
        ]);

    }

    public function start_bot(Request $request) // start boot
    {$bot_name = $request->name;
        $bot_id = $request->id;
        $bot = DB::table('bots')->Where('id', $bot_id)->first();
        $daliy = $bot->day;
        $max = $bot->max;
        // $total =     $bot->total;
        $win = $bot->win;
        $loss = $bot->loss;

        $isDemo = auth()->user()->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';
        $balance = Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->first();

        $symbols = is_array($request->symbol) ? $request->symbol : [$request->symbol];
        $total_amount = $request->amount * count($symbols);

        if ($request->amount > $max) {
            return response()->json(['status' => true, 'message' => "maximum traded amount in this bot is $$max per asset"]);
        } else {
            $check = DB::table('bot_generated_result')->where(['bot_id' => $bot_id, 'user_id' => auth()->guard('web')->user()->id, 'created_at' => Carbon::today()])->get();

            if ((count($check) == $daliy)) {
                return response()->json(['status' => true, 'message' => 'You have exceeded the maximun daily limit for this Bot']);
            }
            $data = DB::table('botresults')->where(['bot_id' => $bot_id, 'user_id' => auth()->guard('web')->user()->id, 'created_at' => Carbon::today()])->exists();
            if ($data) {
                return response()->json(['status' => true, 'message' => 'Bot is already running for today.']);
            } else {
                if ($total_amount > $balance->$balanceColumn) {
                    return response()->json(['status' => true, 'message' => "Sorry You Do don't have enough ".($isDemo ? 'demo ' : '')."fund. Total cost: $$total_amount"]);
                } else {
                    Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->decrement($balanceColumn, $total_amount);

                    foreach ($symbols as $sym) {
                        DB::table('botresults')->insert([
                            'user_id' => auth()->guard('web')->user()->id,
                            'bot_id' => $bot_id,
                            'name' => $bot_name,
                            'symbol' => $sym,
                            'amount' => $request->amount,
                            'day' => $daliy,
                            'type' => $request->type,
                            'total' => 0,
                            'win' => $win,
                            'loss' => $loss,
                            'is_demo' => $isDemo,
                            'is_auto_renew' => $request->is_auto_renew ?? 0,
                            'expire_time' => Carbon::now()->addMinutes($daliy),
                            'created_at' => Carbon::now(),
                        ]);
                    }

                    return response()->json(['status' => true]);
                }
            }
        }
    }

    public function stop_bot(Request $request)  // stop bot
    {$bot_id = $request->id;
        $isDemo = auth()->user()->is_demo;
        $userId = auth()->guard('web')->user()->id;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $botResult = DB::table('botresults')->where([
            'user_id' => $userId,
            'bot_id' => $bot_id,
            'is_demo' => $isDemo,
        ])->first();

        if ($botResult) {
            // Refund the capital
            Balance::whereUserId($userId)->where('symbol', 'USD')->increment($balanceColumn, $botResult->amount);

            // Delete the bot instance
            DB::table('botresults')->where([
                'bot_id' => $bot_id,
                'user_id' => $userId,
                'is_demo' => $isDemo,
            ])->delete();

            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false, 'message' => 'Bot instance not found']);
    }
}
