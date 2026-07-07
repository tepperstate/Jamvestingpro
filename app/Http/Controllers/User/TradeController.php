<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Balance;
use App\Models\Copy_trade_order;
use App\Models\MutualFundInvestment;
use App\Models\Order;
use App\Models\User;
use App\Services\BinancePriceService;
use App\Services\TelegramNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TradeController extends Controller
{
    public function isMobileView()
    {
        return false;
    }

    public function trade(Request $request) // trade
    {$user = auth()->guard('web')->user();

        // Limits
        $limit_total = $user->trades ?? 0;
        $limit_daily = $user->daily_trade ?? 0;
        $limit_weekly = $user->weekly_trade ?? 0;

        // Current Counts
        $count_total = Order::where('user_id', $user->id)->count();
        $count_daily = Order::where('user_id', $user->id)
            ->where('traded_date', '>=', Carbon::now()->subHours(22))
            ->count();
        $count_weekly = Order::where('user_id', $user->id)
            ->whereBetween('traded_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // Check limits (0 means unlimited or not set for that specific limit, assuming we fall back to daily = total for backward compatibility, but strictly honoring limits > 0)
        // Usually, if a limit is greater than 0, we enforce it.
        $can_trade = true;
        $error_msg = '';

        if ($limit_total > 0 && $count_total >= $limit_total) {
            $can_trade = false;
            $error_msg = 'You’ve exceeded your total trade limit. Please upgrade your package.';
        } elseif ($limit_weekly > 0 && $count_weekly >= $limit_weekly) {
            $can_trade = false;
            $error_msg = 'You’ve exceeded your weekly trade limit. Please try again next week or upgrade.';
        } elseif ($limit_daily > 0 && $count_daily >= $limit_daily) {
            $can_trade = false;
            $error_msg = 'You\'ve exceeded your trade limit. Please try again in 22 hours or upgrade.';
        } elseif ($limit_total == 0 && $limit_weekly == 0 && $limit_daily == 0) {
            // Edgecase: if all are 0, maybe there's no package? Or it's unlimited.
            // Let's assume it's unlimited if they are all 0 in this specific application, unless they have no package.
        }

        $option = $request->expiretime;

        // Map the value to the desired format
        $formattedValue = '';

        switch ($option) {
            case '1':
                $formattedValue = '1min';
                break;
            case '5':
                $formattedValue = '5mins';
                break;
            case '10':
                $formattedValue = '10mins';
                break;
            case '15':
                $formattedValue = '15mins';
                break;
            case '30':
                $formattedValue = '30mins';
                break;
            case '60':
                $formattedValue = '1 hour';
                break;
            case '120':
                $formattedValue = '2 hours';
                break;
            case '1440':
                $formattedValue = '24 hours';
                break;
            case '10080':
                $formattedValue = '7 days';
                break;
            default:
                $formattedValue = $option;
                break;
        }

        $isDemo = auth()->user()->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';
        $balance = Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->first();

        if ($can_trade) {
            if ($request->amount > $balance->$balanceColumn) {
                return response(['error' => "You don't have enough ".($isDemo ? 'demo ' : '').'fund please fund your account']);
            } else {
                // Flexible lookup: try with and without slashes/dashes
                $asset = $request->asset;
                $assetRecord = Asset::where('symbols', $asset)
                    ->orWhere('symbols', str_replace(['/', '-'], '', $asset))
                    ->orWhere('symbols', substr($asset, 0, 3).'/'.substr($asset, 3))
                    ->orWhere('symbols', strtoupper($asset))
                    ->first();

                if (! $assetRecord) {
                    return response(['error' => 'Market security not found: '.$asset]);
                }

                // Trading Engine 2.0 Rigging Logic
                $engine_mode = DB::table('emergency')->where('id', 1)->value('engine_mode') ?? 0;
                $win_rate = DB::table('emergency')->where('id', 1)->value('win_rate') ?? 20;

                $outcome_preset = null;
                $is_admin_signal = false;

                // 1. Check for matching Admin Signal (Consumption Logic)
                $signal = DB::table('generate_signal')
                    ->where('symbols', $assetRecord->symbols)
                    ->where('is_used', 0)
                    ->orderBy('id', 'asc') // First in, first out
                    ->first();

                if ($signal) {
                    $is_admin_signal = true;
                    $outcome_preset = $signal->profits; // 'win' or 'loss'

                    // Mark as used
                    DB::table('generate_signal')
                        ->where('id', $signal->id)
                        ->update(['is_used' => 1]);
                }
                // 2. If Offline/Rigged mode is active, apply hedge ratio
                elseif ($engine_mode == 1) {
                    $organicTotal = Order::where('user_id', auth()->guard('web')->user()->id)
                        ->where('is_admin_signal', false)
                        ->count();

                    // 1 in 5 (20%) exact target logic with organic randomization
                    $cycleSize = ceil(100 / $win_rate); // Default 5 for 20%
                    if ($cycleSize < 1) {
                        $cycleSize = 5;
                    }

                    $blockId = floor($organicTotal / $cycleSize);
                    $cycleIndex = $organicTotal % $cycleSize;

                    // Deterministic but "random-looking" win position per block per user
                    $winPosition = (crc32(auth()->user()->id.$blockId) % $cycleSize);
                    $outcome_preset = ($cycleIndex == $winPosition) ? 'win' : 'loss';
                }

                $settings = \App\Models\Site_setting::first();


                $leverage = $request->leverage ?? '1:1';
                if (! auth()->user()->hasFeature('high_leverage')) {
                    $leverage = '1:1';
                }

                $order = Order::create([
                    'trade_id' => Str::random(6),
                    'user_id' => auth()->guard('web')->user()->id,
                    'exchange' => $assetRecord->exchanges_id,
                    'asset_id' => $assetRecord->id,
                    'symbol' => $request->asset,
                    'amount' => $request->amount,
                    'leverage' => $leverage,
                    'win' => $assetRecord->percentage ?? 90,
                    'loss' => $assetRecord->loss_percentage ?? 0,
                    'stop_loss' => $request->stoploss ?? null,
                    'take_profit' => $request->takeprofit ?? null,
                    'expire_time' => $formattedValue,
                    'time' => $request->expiretime,
                    'expire_date' => Carbon::now()->addMinutes($request->expiretime),
                    'status' => 'pending',
                    'type' => $request->type,
                    'types' => $isDemo ? 'demo' : 'live',
                    'is_demo' => $isDemo,
                    'traded_date' => Carbon::now(),
                    'strike_rate' => $request->rate,
                    'outcome_preset' => $outcome_preset,
                    'is_admin_signal' => $is_admin_signal,
                    'hedge_ratio_counter' => $organicTotal ?? 0,
                    'approval_status' => ($settings && $settings->trades_auto_approve) ? 'approved' : 'pending',
                ]);

                if ($settings && $settings->trades_auto_approve) {
                    $profit_amount = ($settings->trades_auto_win_percent / 100) * $request->amount;
                    
                    Order::where('id', $order->id)->update([
                        'status' => 'win',
                        'p_l' => $profit_amount,
                        'is_overridden' => true,
                    ]);
                    
                    Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->increment($balanceColumn, $profit_amount);
                    
                    if ($request->wantsJson()) {
                        return response(['status' => 'Trade auto-approved successfully. Profit: $'.number_format($profit_amount, 2), 'balance' => $balance->$balanceColumn]);
                    }
                    return response(['status' => 'Trade auto-approved successfully. Profit: $'.number_format($profit_amount, 2), 'balance' => $balance->$balanceColumn]);
                }


                try {
                    TelegramNotificationService::send('trade_executed', [
                        'user' => auth()->guard('web')->user()->first_name.' '.auth()->guard('web')->user()->last_name,
                        'pair' => $request->asset,
                        'type' => $request->type,
                        'amount' => $request->amount,
                    ]);
                } catch (\Exception $e) {
                }

                Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->decrement($balanceColumn, $request->amount);

                if ($request->type == 'call') {
                    return response(['status' => 'pending_approval', 'message' => 'You forcasted a increase of '.$request->asset.' in '.$formattedValue, 'balance' => $balance->$balanceColumn, 'type' => 'binary', 'order_id' => $order->id]);
                } else {
                    return response(['status' => 'pending_approval', 'message' => 'You forcasted a decrease of '.$request->asset.' in '.$formattedValue, 'balance' => $balance->$balanceColumn, 'type' => 'binary', 'order_id' => $order->id]);
                }
            }
        } else {
            return response()->json([
                'error' => mb_convert_encoding($error_msg ?: 'You’ve exceeded your trade limits. Please upgrade to a higher package.', 'UTF-8', 'UTF-8'),
            ]);

        }

    }

    public function execute_result_after_time()
    {
        $orders = Order::whereStatus('pending')->get();
        $today = Carbon::now();
        $emergency = DB::table('emergency')->where('id', 1)->value('emergency'); // 1 = offline, 0 = online

        $signals = DB::table('generate_signal')->get()->keyBy('symbols');

        foreach ($orders as $val) {
            if ($today->lt($val->expire_date)) {
                continue;
            }

            DB::beginTransaction();
            try {
                $order = Order::lockForUpdate()->find($val->id);
                if (! $order || $order->status !== 'pending') {
                    DB::rollBack();

                    continue;
                }

                $user_id = $order->user_id;
                $amount = $order->amount;
                $profit_perc = $order->win ?? 90;
                $loss_perc = $order->loss ?? 0;
                $symbol = $order->symbol;
                $admin_status = $val->admin_status;

                $profit_amount = ($profit_perc / 100) * $amount;
                $win_total = $amount + $profit_amount;

                $loss_back_amount = ($loss_perc / 100) * $amount; // Partial return on loss?
                // Usually loss is total, but we follow the existing logic block if it had specific loss calc
                $loss_result = $loss_back_amount;

                $status = null;

                // 1. Admin manual override (Highest Priority)
                if ($admin_status === 'win') {
                    $status = 'win';
                } elseif ($admin_status === 'loss') {
                    $status = 'loss';
                } elseif ($admin_status === 'draw') {
                    $status = 'draw';
                }
                // 2. Pre-determined outcome (Signal or Rigging)
                elseif ($order->outcome_preset) {
                    $status = $order->outcome_preset;
                }
                // 3. Fallback to existing logic if no preset found
                else {
                    // Online Mode: Signals
                    if ($emergency == 0) {
                        if ($signals->has($symbol)) {
                            $sig = $signals->get($symbol);
                            $status = ($sig->profits === 'win') ? 'win' : 'loss';
                        } else {
                            $status = (rand(1, 5) === 1) ? 'win' : 'loss';
                        }
                    }
                    // Offline Mode: Always loss (no signals active)
                    else {
                        $status = 'loss';
                    }
                }

                $user_obj = User::find($user_id);
                $balance_field = $order->is_demo ? 'demo' : 'amount';

                // Update balance and order
                if ($status === 'win') {
                    Balance::whereUserId($user_id)->where('symbol', 'USD')->increment($balance_field, $win_total);
                    $order->update([
                        'status' => 'win',
                        'p_l' => $profit_amount,
                        'modal' => 'open',
                    ]);
                } elseif ($status === 'draw') {
                    Balance::whereUserId($user_id)->where('symbol', 'USD')->increment($balance_field, $amount);
                    $order->update([
                        'status' => 'draw',
                        'p_l' => 0,
                        'modal' => 'open',
                    ]);
                } else {
                    Balance::whereUserId($user_id)->where('symbol', 'USD')->increment($balance_field, $loss_result);
                    $order->update([
                        'status' => 'loss',
                        'p_l' => $amount - $loss_result, // amount lost
                        'modal' => 'open',
                    ]);
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                // \Log::error("Trade Execution Error: " . $e->getMessage());
            }
        }

        return response()->json(['status' => 'true']);
    }

    public function execute_result_after_time_for_copy_trade()
    {

        $orders = Copy_trade_order::whereStatus('pending')->get();
        $today = date('Y-m-d H:i:s');

        foreach ($orders as $order) {
            DB::beginTransaction();
            try {
                // Re-check the order status inside the transaction
                $order = Copy_trade_order::lockForUpdate()->find($order->id);
                if ($order->status !== 'pending') {
                    // If status has changed (processed by another process), skip it
                    DB::rollBack();

                    continue;
                }

                $id = $order->id;
                $user = $order->user_id;
                $amount = $order->amount;        // Capital amount
                $win = $order->win;           // Win percentage
                $loss = $order->loss;          // Loss percentage
                $symbol = $order->symbol;
                $expire_date = $order->expire_date;
                $admin_status = $order->admin_status;
                $isDemo = $order->is_demo ?? false;
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
                            Balance::whereUserId($user)->where('symbol', 'USD')->decrement($balanceColumn, $loss_amount);

                            Copy_trade_order::whereUserId($user)
                                ->whereSymbol($symbol)
                                ->whereId($id)
                                ->update([
                                    'status' => 'loss',
                                    'p_l' => $p_l_loss,  // Capital + Loss
                                    'modal' => 'open',
                                ]);
                        }

                        // Auto-Renew logic
                        if ($order->is_auto_renew) {
                            $balance = Balance::whereUserId($user)->where('symbol', 'USD')->first();

                            if ($balance && $amount <= $balance->$balanceColumn) {
                                Balance::whereUserId($user)->where('symbol', 'USD')->decrement($balanceColumn, $amount);

                                $minutes = Carbon::parse($order->traded_date)->diffInMinutes(Carbon::parse($order->expire_date));

                                Copy_trade_order::create([
                                    'trade_id' => Str::random(6),
                                    'user_id' => $user,
                                    'exchange' => $order->exchange,
                                    'asset_id' => $order->asset_id,
                                    'trader_name' => $order->trader_name,
                                    'symbol' => $symbol,
                                    'amount' => $amount,
                                    'win' => $win,
                                    'loss' => $loss,
                                    'expire_time' => $order->expire_time,
                                    'time' => $order->time,
                                    'expire_date' => Carbon::now()->addMinutes($minutes),
                                    'status' => 'pending',
                                    'type' => $order->type,
                                    'types' => $order->types,
                                    'traded_date' => Carbon::now(),
                                    'admin_status' => $admin_status,
                                    'is_auto_renew' => true,
                                    'is_demo' => $isDemo,
                                ]);
                            }
                        }
                    }
                }
                // Commit the transaction after successful updates
                DB::commit();

            } catch (\Exception $e) {
                // Rollback if any exception occurs
                DB::rollBack();
                throw $e; // Optionally log or handle the exception as needed
            }
        }

        return response()->json(['status' => 'true']);
    }

    private function asset($asset) // private
    {if (isset($asset)) {
        $data = Asset::where('symbols', 'like', $asset)->first();

        return $data->percentage;
    }
    }

    private function asset_loss($asset) // private
    {if (isset($asset)) {
        $data = Asset::where('symbols', 'like', $asset)->first();

        return $data->loss_percentage;
    }
    }

    public function getOrder()  // get user orders
    {$data = Order::WhereUserId(auth()->guard('web')->user()->id)->get();

        return response()->json(['data' => $data]);
    }

    public function closeModal($id)
    {

        Order::whereId($id)->update(['modal' => 'close']);

        return response()->json(['status' => 'true']);
    }

    public function closeCopyModal($id)
    {

        Copy_trade_order::whereId($id)->update(['modal' => 'close']);

        return response()->json(['status' => 'true']);
    }

    public function recentTrade()
    {
        $data = Order::with('asset')->inRandomOrder()->limit(14)->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function closeTrade($id) // close trade
    {$trade = Order::whereId($id)->first();

        $isDemo = $trade->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->increment($balanceColumn, $trade->amount);

        if ($trade->status == 'pending') {
            Order::whereId($id)->delete();

            return response()->json(['status' => true]);
        }
    }

    public function toggleDemo() // toggle Demo and Live
    {$user = User::find(auth()->id());
        $new_status = $user->is_demo ? 0 : 1;
        $user->update(['is_demo' => $new_status]);

        return back()->with('status', $new_status ? 'Switched to Demo Portfolio' : 'Switched to Live Portfolio');
    }

    public function portfolio()
    {
        $isDemo = auth()->user()->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';
        $tickerData = BinancePriceService::get24hrTickerData() ?? [];

        // 1. Binary Trades (Orders)
        $binaryOrders = Order::with('asset')->where('user_id', auth()->id())->where('is_demo', $isDemo)->orderBy('id', 'desc')->get();
        $binaryEquity = $binaryOrders->where('status', 'pending')->sum('amount');

        // 2. Stocks (Standard & VIP)
        $stocks = DB::table('stock_balance')->where('stock_balance.user_id', auth()->id())
            ->where('stock_balance.is_demo', $isDemo)
            ->join('stock_trades', 'stock_balance.stock_id', '=', 'stock_trades.id')
            ->select('stock_trades.image as image', 'stock_trades.symbol as symbol', 'stock_trades.name as name', 'stock_trades.buy as buy', 'stock_balance.amount as units', 'stock_balance.avg_price', 'stock_balance.total_cost', 'stock_balance.id as id', 'stock_balance.stock_id')
            ->get();

        $stocks->transform(function ($item) {
            // Fetch buffer from stocks config table if exists, otherwise default to 20
            $stockConfig = DB::table('stocks')->where('symbols', $item->symbol)->first();
            $buffer = $stockConfig ? ($stockConfig->buffer_percent ?? 20.0) : 20.0;
            $multiplier = 1 + ($buffer / 100);

            $item->units = $item->units * $multiplier;
            $item->total_cost = $item->total_cost * $multiplier;

            return $item;
        });

        $stocksEquity = $stocks->sum(function ($item) {
            return $item->units * ($item->buy > 0 ? $item->buy : ($item->total_cost / ($item->units ?: 1)));
        });

        // 3. Mutual Funds
        $funds = MutualFundInvestment::with('fund')->where('user_id', auth()->id())->where('is_demo', $isDemo)->where('status', 'active')->get();

        $funds->transform(function ($item) {
            $buffer = $item->fund->buffer_percent ?? 20.0;
            $multiplier = 1 + ($buffer / 100);
            $item->amount = $item->amount * $multiplier;
            $item->units = $item->units * $multiplier;

            return $item;
        });

        $fundsEquity = $funds->sum(function ($item) {
            return $item->units * (optional($item->fund)->nav_price ?? 0);
        });

        // Liquid Cash (Available Balance)
        $user = auth()->user();
        $usdBalance = Balance::where('user_id', $user->id)->where('symbol', 'USD')->first();
        $liquidCash = $usdBalance ? $usdBalance->{$balanceColumn} : 0;

        $totalEquity = $binaryEquity + $stocksEquity + $fundsEquity + $liquidCash;

        return view('exchange.portfolio', compact(
            'user',
            'binaryOrders',
            'stocks',
            'funds',
            'totalEquity',
            'binaryEquity',
            'stocksEquity',
            'fundsEquity',
            'liquidCash',
            'tickerData'
        ));
    }

    public function export_trade_history()
    {
        $user = auth()->user();
        $isDemo = $user->is_demo;

        $trades = Order::with(['asset', 'exchanges'])
            ->where('user_id', $user->id)
            ->where('is_demo', $isDemo)
            ->orderBy('id', 'desc')
            ->get();

        $totalProfit = $trades->where('status', 'win')->sum('p_l');
        $totalLoss = $trades->where('status', 'loss')->sum('p_l');
        $winCount = $trades->where('status', 'win')->count();
        $totalSettled = $trades->whereIn('status', ['win', 'loss'])->count();
        $winRate = $totalSettled > 0 ? round(($winCount / $totalSettled) * 100, 1) : 0;
        $tradeAmount = $trades->where('status', 'win')->sum('amount');
        $realProfit = $totalProfit; // p_l already stores the profit amount
        $netPL = $realProfit - $totalLoss;

        $pdf = Pdf::loadView('pdf.trade_history', [
            'trades' => $trades,
            'userName' => $user->first_name.' '.$user->last_name,
            'siteName' => site()->name ?? config('app.name'),
            'reportDate' => Carbon::now()->format('F d, Y'),
            'totalTrades' => $trades->count(),
            'totalProfit' => $realProfit,
            'totalLoss' => $totalLoss,
            'netPL' => $netPL,
            'winRate' => $winRate,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = strtolower(str_replace(' ', '_', site()->name ?? 'platform')).'_trade_history_'.date('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}
