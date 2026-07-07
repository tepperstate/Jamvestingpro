<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Deposit;
use App\Models\Stock_Trade;
use Illuminate\Support\Facades\DB;

class VipStockController extends Controller
{
    private const VIP_DEPOSIT_THRESHOLD = 150000;

    public function index()
    {
        if (! auth()->user()->hasFeature('vip_stocks')) {
            return redirect()->route('user.upgrade')->with('error', 'VIP Stock trading is exclusive to our premium account holders.');
        }

        $user = auth()->user();
        $usdBalance = Balance::where('user_id', $user->id)
            ->where('symbol', 'USD')
            ->first();

        $isDemo = $user->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';
        $currentBalance = $usdBalance ? $usdBalance->$balanceColumn : ($user->balance ? $user->balance->$balanceColumn : 0);

        // Calculate total successful deposits for VIP progress
        $totalDeposits = Deposit::where('user_id', $user->id)
            ->where('status', 'success')
            ->sum('amount');

        $isUnlocked = $totalDeposits >= self::VIP_DEPOSIT_THRESHOLD;

        $vipStocks = Stock_Trade::where('is_vip', true)
            ->when(! $isUnlocked, function ($q) {
                return $q->limit(3); // Preview limit
            })
            ->orderBy('buy', 'desc')
            ->paginate(20);

        $portfolio = collect();
        $totalEquity = 0;

        if ($isUnlocked) {
            $portfolio = DB::table('stock_balance')
                ->where('stock_balance.user_id', $user->id)
                ->where('stock_balance.is_demo', $isDemo)
                ->join('stock_trades', 'stock_balance.stock_id', '=', 'stock_trades.id')
                ->where('stock_trades.is_vip', true)
                ->select(
                    'stock_trades.image as image',
                    'stock_trades.symbol as symbol',
                    'stock_trades.name as name',
                    'stock_trades.buy as buy',
                    'stock_balance.amount as units',
                    'stock_balance.id as id',
                    'stock_balance.stock_id'
                )
                ->get();

            $portfolio->transform(function ($item) {
                // Fetch buffer from stocks config table if exists, otherwise default to 20
                $stockConfig = DB::table('stocks')->where('symbols', $item->symbol)->first();
                $buffer = $stockConfig ? ($stockConfig->buffer_percent ?? 20.0) : 20.0;
                $multiplier = 1 + ($buffer / 100);

                $item->units = $item->units * $multiplier;

                return $item;
            });

            $totalEquity = $portfolio->sum(function ($item) {
                return $item->units * ($item->buy > 0 ? $item->buy : 1);
            });
        }

        return view('exchange.vip_stocks', compact(
            'isUnlocked', 'currentBalance', 'totalDeposits', 'vipStocks', 'portfolio', 'totalEquity'
        ));
    }
}
