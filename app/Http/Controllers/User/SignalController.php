<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Signal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SignalController extends Controller
{
    public function isMobileView()
    {
        return false;
    }

    public function signals()  // signal
    {$signals_data = Signal::orderBy('day', 'asc')->get();

        $viewName = $this->isMobileView() ? 'mobile.exchange.signal' : 'exchange.signal';

        return view($viewName, [
            'data' => $signals_data,
        ]);
    }

    public function alluser_signal()  // all user signlas
    {$isDemo = auth()->user()->is_demo;
        $my_signals_data = DB::table('purchase_signal')
            ->whereUserId(auth()->guard('web')->user()->id)
            ->where('purchase_signal.is_demo', $isDemo)
            ->join('signals', 'purchase_signal.signal_id', '=', 'signals.id')
            ->select('purchase_signal.*', 'signals.buffer_percent')
            ->get();

        $my_signals_data->transform(function ($signal) {
            $buffer = $signal->buffer_percent ?? 20.0;
            $signal->amount = $signal->amount * (1 + ($buffer / 100));

            return $signal;
        });

        $viewName = $this->isMobileView() ? 'mobile.exchange.mysignals' : 'exchange.mysignals';

        return view($viewName, [
            'data' => $my_signals_data,
        ]);
    }

    public function user_signal() // all user signal
    {$isDemo = auth()->user()->is_demo;
        $signal_user_data = DB::table('signalresults')
            ->whereUserId(auth()->guard('web')->user()->id)
            ->where('signalresults.is_demo', $isDemo)
            ->join('signals', 'signalresults.signal_id', '=', 'signals.id')
            ->select('signalresults.*', 'signals.buffer_percent')
            ->get();

        $signal_user_data->transform(function ($signal) {
            $buffer = $signal->buffer_percent ?? 20.0;
            $signal->amount = $signal->amount * (1 + ($buffer / 100));

            return $signal;
        });

        // The aggregated values could be scaled similarly
        $profit = $signal_user_data->sum('amount');

        // Let's get today's trades from the modified collection
        $today_trade = $signal_user_data->where('created_at', '>=', Carbon::today())->sum('amount');
        $today_profit = DB::table('signalresults')->whereUserId(auth()->guard('web')->user()->id)->whereDate('created_at', Carbon::today())->whereStatus('win')->where('is_demo', $isDemo)->sum('profit');
        $today_profits = $signal_user_data->where('created_at', '>=', Carbon::today())->where('status', 'win')->sum('amount');

        $viewName = $this->isMobileView() ? 'mobile.exchange.signal_user' : 'exchange.signal_user';

        return view($viewName, [
            'data' => $signal_user_data,
            'trade' => $profit,
            'today' => $today_trade,
            'today_profit' => $today_profit - $today_profits,
        ]);

    }

    public function buy_signal(Request $request)   // buy signals
    {$isDemo = auth()->user()->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $checks = DB::table('purchase_signal')
            ->whereUserId(auth()->guard('web')->user()->id)
            ->where('signal_id', $request->id)
            ->where('is_demo', $isDemo)
            ->exists();

        if ($checks) {
            if ($request->ajax()) {
                return response()->json(['status' => 'You are already subscribed to this signal']);
            }

            return redirect()->route('signals.user');
        }

        $balance = Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->first();

        if ($request->amount > $balance->$balanceColumn) {
            if ($request->ajax()) {
                return response()->json(['error' => "Sorry You Do don't have enough ".($isDemo ? 'demo ' : '').'fund']);
            }

            return back()->with('error', "Sorry You Do don't have enough ".($isDemo ? 'demo ' : '').'fund');
        } else {
            Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->decrement($balanceColumn, $request->amount);

            DB::table('purchase_signal')->insert([
                'user_id' => auth()->guard('web')->user()->id,
                'signal_id' => $request->id,
                'name' => $request->name,
                'amount' => $request->amount,
                'is_demo' => $isDemo,
            ]);

            if ($request->ajax()) {
                return response()->json(['status' => 'Signal subscription successful']);
            }

            return redirect()->route('signals.user')->with('status', 'Signal subscription successful');
        }
    }
}
