<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Balance;
use App\Models\Copy_trade_order;
use App\Models\Corder;
use App\Models\Trader;
use App\Models\User;
use App\Notifications\DepositNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CopyTradeController extends Controller
{
    public function isMobileView()
    {
        return false;
    }

    public function getData(Request $request)
    {
        $columns = ['trade_id', 'trade_type', 'trader_name', 'asset', 'amount', 'profit_loss', 'status'];

        $query = Copy_trade_order::with(['asset', 'exchanges'])->where('user_id', auth()->guard('web')->user()->id)->orderBy('id', 'desc');

        $totalRecords = $query->count();

        if ($request->has('search') && $request->input('search')['value']) {
            $searchValue = $request->input('search')['value'];
            $query->where(function ($query) use ($searchValue) {
                $query->where('trade_id', 'like', "%{$searchValue}%")
                    ->orWhere('trader_name', 'like', "%{$searchValue}%")
                    ->orWhere('symbol', 'like', "%{$searchValue}%");
            });
        }
        $filteredRecords = $query->count();

        if ($request->has('order')) {
            $order = $request->input('order')[0];
            $query->orderBy($columns[$order['column']], $order['dir']);
        }
        if ($request->has('length') && $request->input('length') > 0) {
            $query->skip($request->input('start', 0))->take($request->input('length'));
        }
        $trades = $query->get();
        $data = [];
        foreach ($trades as $key => $trade) {
            $data[] = [
                'sn' => $key + 1,
                'trade_id' => $trade->trade_id,
                'trade_type' => $trade->type,
                'trader_name' => $trade->trader_name,
                'trader_image' => DB::table('traders')->where('name', $trade->trader_name)->first(),
                'exchanges' => $trade->exchanges,
                'p_l' => $trade->p_l,
                'asset' => [
                    'image1' => $trade->asset->image1 ? asset('storage/image/'.$trade->asset->image1) : null,
                    'image2' => asset('storage/image/'.$trade->asset->image2),
                ],
                'symbol' => $trade->symbol,
                'amount' => 'USD '.number_format($trade->amount),
                'profit_loss' => $trade->p_l,
                'Total_Profit_Loss' => $trade->p_l - $trade->amount,

                'status' => $trade->status,
                'time' => $trade->time,
                'expire_date' => $trade->expire_date,
                'created_at' => $trade->created_at,
                'id' => $trade->id,
                'modal' => $trade->modal,
            ];
        }

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function traders_details()
    {
        // Get all orders by the authenticated user
        $data = Copy_trade_order::with('exchanges')
            ->where('user_id', auth()->guard('web')->user()->id)
            ->where('is_demo', auth()->user()->is_demo)
            ->orderBy('id', 'desc')
            ->get();

        $data->transform(function ($order) {
            $trader = Trader::where('name', $order->trader_name)->first();
            $buffer = $trader ? (float) $trader->buffer_percent : 20.0;
            $order->amount = $order->amount * (1 + ($buffer / 100));

            return $order;
        });

        // Count of total orders
        $orderCount = $data->count();

        $viewName = $this->isMobileView() ? 'mobile.exchange.trader_d' : 'exchange.trader_d';

        return view($viewName, [
            'trade' => $data,
            'order' => $data,
        ]);
    }

    public function get_traders_details()
    {
        // Calculate total profit and loss amounts without capital
        $data = Copy_trade_order::with('exchanges')
            ->where('user_id', auth()->guard('web')->user()->id)
            ->where('is_demo', auth()->user()->is_demo)
            ->orderBy('id', 'desc')
            ->get();

        $win = DB::table('copy_trade_order')
            ->where('user_id', auth()->guard('web')->user()->id)
            ->where('is_demo', auth()->user()->is_demo)
            ->where('status', 'win')
            ->sum(DB::raw('p_l - amount'));

        $loss = DB::table('copy_trade_order')
            ->where('user_id', auth()->guard('web')->user()->id)
            ->where('is_demo', auth()->user()->is_demo)
            ->where('status', 'loss')
            ->sum(DB::raw('amount - p_l'));

        // Count of total orders
        $orderCount = $data->count();

        $content = [
            'count' => $orderCount,
            'win' => $win,
            'loss' => $loss,
        ];

        return response()->json(['status' => true, 'data' => $content]);
    }

    public function trader_result()
    {
        $cop_order = DB::table('copy_generated_result')->whereUserId(auth()->guard('web')->user()->id)->orderBy('id', 'desc')->limit(10)->get();

        return response()->json(['data' => $cop_order]);
    }

    public function video()
    {
        return view('exchange.video');
    }

    public function trader_index() //
    {if (! auth()->user()->hasFeature('copy_trading')) {
        return redirect()->route('user.upgrade')->with('error', 'Copy Trading is a premium feature. Please upgrade to unlock.');
    }
        $isDemo = auth()->user()->is_demo;
        $data = DB::table('traders')
            ->leftJoin('corders', function ($join) use ($isDemo) {
                $join->on('traders.id', '=', 'corders.trades_id')
                    ->where('corders.user_id', '=', auth()->user()->id)
                    ->where('corders.is_demo', '=', $isDemo);
            })
            ->orderBy('traders.id', 'desc')
            ->select(
                'traders.*',
                'corders.user_id as user_id',
                'corders.approved as approved'
            )
            ->paginate(12); // Adjust the number of items per page

        $viewName = $this->isMobileView() ? 'mobile.exchange.traders' : 'exchange.traders';

        $b = Asset::all();

        return view($viewName, [
            'data' => $data,
            'b' => $b,
        ]);
    }

    public function copy_trading_history()
    {
        if (! auth()->user()->hasFeature('copy_trading')) {
            return redirect()->route('user.upgrade')->with('error', 'Copy Trading is a premium feature. Please upgrade to unlock.');
        }
        $isDemo = auth()->user()->is_demo;

        // Fetch user's copy orders
        $orders = Corder::where('user_id', auth()->user()->id)
            ->where('is_demo', $isDemo)
            ->orderBy('id', 'desc')
            ->get();

        // Fetch detailed result logs if applicable
        $results = DB::table('copy_generated_result')
            ->whereUserId(auth()->guard('web')->user()->id)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        // Calculate performance metrics
        $activeOrders = $orders->where('status', '!=', 'terminated');
        $totalCapital = $activeOrders->sum('amount');
        $totalPL = $orders->sum('profit') ?: 0;

        $viewName = $this->isMobileView() ? 'mobile.exchange.copy-history' : 'exchange.copy-history';

        return view($viewName, [
            'orders' => $orders,
            'results' => $results,
            'totalCapital' => $totalCapital,
            'totalPL' => $totalPL,
            'activeOrders' => $activeOrders,
        ]);
    }

    public function cancel_copy(Request $request)
    {
        $isDemo = auth()->user()->is_demo;
        $data = Corder::where('trades_id', $request->did)->where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->first();

        if ($data) {
            $balanceColumn = $isDemo ? 'demo' : 'amount';
            $balance = Balance::where('user_id', auth()->user()->id)->where('symbol', 'USD')->first();
            if ($balance) {
                $balance->increment($balanceColumn, $data->amount);
            }
            $data->delete();
        }

        return response()->json(['status' => 'Your copy trading request has been canceled and your allocated funds have been refunded.']);
    }

    public function copy_trade(Request $request) // copy trade
    {$isDemo = auth()->user()->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $data = Trader::where('id', $request->did)->first();
        $trader = Corder::where('user_id', auth()->guard('web')->user()->id)->where('is_demo', $isDemo)->count();

        $balance = Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->first();

        $symbols = is_array($request->symbols) ? $request->symbols : [$request->symbols];
        $total_amount = $request->amount * count($symbols);

        if ($request->amount < 5000) {
            return response()->json(['error' => 'Minimum mirroring capital is $5000 per asset']);
        } elseif ($total_amount > $balance->$balanceColumn) {
            return response()->json(['error' => "Do don't have enough ".($isDemo ? 'demo ' : '')."fund please fund your account. Total cost: $$total_amount"]);
        } elseif ($trader > 0) {
            return response()->json(['error' => 'You can not copy another trader until your cancel your previous trader']);
        } else {

            Corder::create([
                'user_id' => auth()->guard('web')->user()->id,
                'trade_id' => Str::random(6),
                'trades_id' => $data->id,
                'trader_name' => $data->name,
                'country' => $data->country,
                'amount' => $request->amount,
                'commission' => $data->percentage,
                'win' => $data->win,
                'profit' => '',
                'status' => 'pending',
                'types' => $isDemo ? 'demo' : 'live',
                'is_demo' => $isDemo,
                'symbols' => json_encode($symbols),
                'is_auto_renew' => $request->is_auto_renew ?? 0,
            ]);

            DB::table('traders')->where('id', $data->id)->increment('total_copier', 1);
            Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->decrement($balanceColumn, $total_amount);

            $email = DB::table('admin_email')->where('id', 1)->first();
            try {
                $text = [
                    'greeting' => 'Hello Admin',
                    'subject' => 'User Notification',
                    'body' => 'A User with name  '.auth()->guard('web')->user()->first_name.'made a copy trade worth '.number_format($data->amount),
                    'data' => null,
                    'url' => null,
                    'thanks' => 'Thank you for choosing '.env('APP_NAME'),
                ];
                Notification::route('mail', $email->email)
                    ->notify(new DepositNotification($text));
            } catch (\Throwable $th) {
                // throw $th;
            }

            return response()->json(['status' => 'Your copy trading request has been successfully submitted. Trader will soon begin as soon as your request is approved by the trader']);
        }
    }
}
