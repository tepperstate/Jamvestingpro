<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\MarginPair;
use App\Models\MarginPosition;
use App\Models\Noti;
use App\Models\Site_setting;
use App\Models\SpotOrder;
use App\Models\Stock_Trade;
use App\Services\BinancePriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarginController extends Controller
{
    public function index()
    {
        $pairs = MarginPair::where('status', 'active')->get();
        $defaultPair = $pairs->first();
        $positions = MarginPosition::where('user_id', Auth::id())
            ->where('status', 'open')
            ->get();

        // ── Live price + 24h change sync via centralized service ──
        try {
            $data = BinancePriceService::fetchAll();
            $priceMap = $data['priceMap'];
            $changeMap = $data['changeMap'];

            if ($priceMap || $changeMap) {
                foreach ($pairs as $pair) {
                    $sym = $pair->symbol;
                    $livePrice = $priceMap[$sym] ?? null;
                    $liveChange = $changeMap[$sym] ?? 0;
                    $base = preg_replace('/(USDT|USDC|BTC|ETH|BNB)$/', '', $sym);

                    Stock_Trade::updateOrCreate(
                        ['symbol' => $sym],
                        [
                            'name' => $base,
                            'buy' => $livePrice ?? $pair->mark_price,
                            'sell' => ($livePrice ?? $pair->mark_price) * 0.999,
                            'changes' => $liveChange,
                            'volume' => rand(100000, 9000000),
                            'image' => strtolower($sym).'.png',
                            'is_vip' => false,
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Margin live price sync failed: '.$e->getMessage());
        }

        $assets = $pairs;

        $user = Auth::user();
        $orders = SpotOrder::where('user_id', $user->id)
            ->where('is_demo', $user->is_demo)
            ->latest()
            ->paginate(15);

        $usdBalance = Balance::where('user_id', $user->id)->where('symbol', 'usd')->first();
        $holdings = DB::table('stock_balance')
            ->where('user_id', $user->id)
            ->where('is_demo', $user->is_demo)
            ->get()
            ->keyBy('symbol');

        $tickerData = BinancePriceService::get24hrTickerData() ?? [];

        $viewName = $this->isMobileView() ? 'mobile.exchange.margin' : 'exchange.margin';

        return view($viewName, compact('pairs', 'assets', 'defaultPair', 'positions', 'orders', 'usdBalance', 'holdings', 'tickerData'));
    }

    public function history()
    {
        $positions = MarginPosition::where('user_id', Auth::id())
            ->whereIn('status', ['closed', 'liquidated'])
            ->orderBy('id', 'desc')
            ->get();

        $viewName = $this->isMobileView() ? 'mobile.exchange.margin_history' : 'exchange.margin_history';

        return view($viewName, compact('positions'));
    }

    public function openPosition(Request $request)
    {
        $request->validate([
            'pair_id' => 'required|exists:margin_pairs,id',
            'direction' => 'required|in:long,short',
            'leverage' => 'required|integer|min:1',
            'quantity' => 'required|numeric|min:0.0001',
            'collateral' => 'required|numeric|min:1',
            'entry_price' => 'required|numeric',
        ]);

        $pair = MarginPair::findOrFail($request->pair_id);

        if ($request->leverage > $pair->max_leverage) {
            return redirect()->back()->with('error', 'Leverage exceeds maximum allowed.');
        }

        $borrowed = $request->collateral * ($request->leverage - 1);
        $liquidation_price = $request->direction === 'long'
            ? $request->entry_price * (1 - (1 / $request->leverage) + ($pair->maintenance_margin / 100))
            : $request->entry_price * (1 + (1 / $request->leverage) - ($pair->maintenance_margin / 100));

        $settings = Site_setting::first();
        $isDemo = Auth::user()->is_demo ? 1 : 0;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $userBalance = DB::table('balances')
            ->where('user_id', Auth::id())
            ->where('symbol', 'usd')
            ->first();

        if (! $userBalance || $request->collateral > $userBalance->$balanceColumn) {
            return redirect()->back()->with('error', 'Insufficient balance.');
        }

        if ($settings && $settings->margin_auto_approve) {
            $profit = $request->collateral * ($settings->margin_auto_win_percent / 100);

            // Directly credit the profit since we bypass deducting the collateral
            DB::table('balances')
                ->where('user_id', Auth::id())
                ->where('symbol', 'usd')
                ->increment($balanceColumn, $profit);

            MarginPosition::create([
                'user_id' => Auth::id(),
                'margin_pair_id' => $pair->id,
                'trade_id' => 'MRG'.strtoupper(uniqid()),
                'direction' => $request->direction,
                'leverage' => $request->leverage,
                'entry_price' => $request->entry_price,
                'quantity' => $request->quantity,
                'collateral' => $request->collateral,
                'borrowed' => $borrowed,
                'liquidation_price' => $liquidation_price,
                'interest_accrued' => 0,
                'unrealized_pnl' => $profit,
                'realized_pnl' => $profit,
                'margin_ratio' => 1 / $request->leverage,
                'status' => 'closed',
                'outcome_preset' => 'win',
                'is_demo' => $isDemo,
            ]);

            Noti::create([
                'user_id' => Auth::id(),
                'message' => 'Margin Auto-Approved: Your trade closed with $'.number_format($profit, 2).' profit.',
                'status' => 'unread',
            ]);

            return redirect()->back()->with('success', 'Margin trade auto-approved successfully. Profit: $'.number_format($profit, 2));
        }

        // Deduct collateral for non-auto-approved trades
        DB::table('balances')
            ->where('user_id', Auth::id())
            ->where('symbol', 'usd')
            ->decrement($balanceColumn, $request->collateral);

        $position = MarginPosition::create([
            'user_id' => Auth::id(),
            'margin_pair_id' => $pair->id,
            'trade_id' => 'MRG'.strtoupper(uniqid()),
            'direction' => $request->direction,
            'leverage' => $request->leverage,
            'entry_price' => $request->entry_price,
            'quantity' => $request->quantity,
            'collateral' => $request->collateral,
            'borrowed' => $borrowed,
            'liquidation_price' => $liquidation_price,
            'interest_accrued' => 0,
            'unrealized_pnl' => 0,
            'realized_pnl' => 0,
            'margin_ratio' => 1 / $request->leverage,
            'status' => 'open',
            'approval_status' => 'pending',
            'is_demo' => $isDemo,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'pending_approval', 'position_id' => $position->id, 'type' => 'margin']);
        }

        return redirect()->back()->with('success', 'Margin position pending approval.');
    }

    public function closePosition(Request $request)
    {
        $position = MarginPosition::where('id', $request->position_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $markPrice = $position->marginPair->mark_price;
        $priceDiff = $markPrice - $position->entry_price;
        if ($position->direction === 'short') {
            $priceDiff = $position->entry_price - $markPrice;
        }

        $pnl = $priceDiff * $position->quantity;

        // Admin rigging logic
        if ($position->admin_status == 'win') {
            $pnl = abs($pnl) > 0 ? abs($pnl) : $position->collateral * 0.5;
        } elseif ($position->admin_status == 'loss') {
            $pnl = -abs($pnl);
            if ($pnl == 0) {
                $pnl = -($position->collateral * 0.5);
            }
        } elseif ($position->admin_status == 'liquidated') {
            $pnl = -$position->collateral;
            $position->status = 'liquidated';
        } else {
            $position->status = 'closed';
        }

        $position->realized_pnl = $pnl;
        $position->save();

        return redirect()->back()->with('success', 'Position closed.');
    }
}
