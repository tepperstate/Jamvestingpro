<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StakingPlan;
use App\Services\BinancePriceService;
use Illuminate\Http\Request;

class StakingPlanController extends Controller
{
    public function index()
    {
        $plans = StakingPlan::orderBy('id', 'desc')->get();

        return view('admin.staking_plans.index', compact('plans'));
    }

    public function syncPlans()
    {
        $popularStaking = [
            ['name' => 'Bitcoin Core Staking', 'symbol' => 'BTC', 'base_apy' => 2.5, 'lock_days' => 60],
            ['name' => 'Ethereum 2.0 Staking', 'symbol' => 'ETH', 'base_apy' => 4.5, 'lock_days' => 30],
            ['name' => 'Solana Validation', 'symbol' => 'SOL', 'base_apy' => 7.2, 'lock_days' => 14],
            ['name' => 'Cardano Pools', 'symbol' => 'ADA', 'base_apy' => 3.5, 'lock_days' => 15],
            ['name' => 'Polkadot Staking', 'symbol' => 'DOT', 'base_apy' => 12.0, 'lock_days' => 28],
            ['name' => 'Avalanche Validator', 'symbol' => 'AVAX', 'base_apy' => 8.5, 'lock_days' => 21],
            ['name' => 'BNB Vault', 'symbol' => 'BNB', 'base_apy' => 5.0, 'lock_days' => 30],
            ['name' => 'Chainlink Node', 'symbol' => 'LINK', 'base_apy' => 6.5, 'lock_days' => 45],
            ['name' => 'Polygon Staking', 'symbol' => 'MATIC', 'base_apy' => 7.0, 'lock_days' => 30],
            ['name' => 'Cosmos Hub Staking', 'symbol' => 'ATOM', 'base_apy' => 14.5, 'lock_days' => 21],
        ];

        foreach ($popularStaking as $plan) {
            StakingPlan::firstOrCreate(
                ['name' => $plan['name'], 'symbol' => $plan['symbol']],
                [
                    'apy_percentage' => $plan['base_apy'] + 10, // Adding extra 10%
                    'min_amount' => rand(5000, 25000),
                    'max_amount' => 500000,
                    'lock_days' => $plan['lock_days'],
                    'status' => 'active',
                    'image' => strtolower($plan['symbol']).'.png',
                    'buffer_percent' => 0,
                    'per_withdrawal_percent' => 0,
                    'lock_period_days' => 0,
                    'daily_roi_percent' => 0,
                    'actual_invested' => 0,
                    'projected_return' => 0,
                    'total_deposit_dashboard' => 0,
                ]
            );
        }

        return back()->with('status', 'Successfully populated Staking plans with +10% APY.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'apy_percentage' => 'required|numeric',
            'min_amount' => 'required|numeric',
            'max_amount' => 'required|numeric',
            'lock_days' => 'required|integer',
        ]);

        $data = $request->except('_token');
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $data['image'] = $newfilename;
        }

        StakingPlan::create($data);

        return back()->with('status', 'Staking Plan created successfully.');
    }

    public function update(Request $request)
    {
        $plan = StakingPlan::findOrFail($request->id);

        $data = $request->except(['_token', 'id']);
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $data['image'] = $newfilename;
        }

        $plan->update($data);

        return back()->with('status', 'Staking Plan updated successfully.');
    }

    public function destroy($id)
    {
        StakingPlan::findOrFail($id)->delete();

        return back()->with('status', 'Staking Plan deleted.');
    }

    public function syncFromBinance(BinancePriceService $binanceService)
    {
        $assets = ['BTC', 'ETH', 'BNB', 'SOL', 'XRP', 'ADA', 'DOT', 'MATIC', 'LINK', 'AVAX', 'ATOM', 'LTC', 'BCH', 'UNI', 'FIL', 'NEAR'];
        $durations = [30, 60, 90, 120];
        $priceMap = $binanceService::getPriceMap() ?? [];

        $count = 0;
        foreach ($assets as $symbol) {
            $binanceSymbol = $symbol.'USDT';
            if (array_key_exists($binanceSymbol, $priceMap)) {
                foreach ($durations as $days) {
                    $exists = StakingPlan::where('symbol', $symbol)->where('lock_days', $days)->exists();
                    if (! $exists) {
                        StakingPlan::create([
                            'name' => "Premium $symbol Staking ($days Days)",
                            'symbol' => $symbol,
                            'apy_percentage' => rand(15, 45),
                            'min_amount' => rand(5000, 25000),
                            'max_amount' => 100000,
                            'lock_days' => $days,
                            'status' => 'active',
                            'description' => "Auto-generated staking plan for $symbol.",
                            'buffer_percent' => 0,
                            'per_withdrawal_percent' => 0,
                            'lock_period_days' => 0,
                            'daily_roi_percent' => 0,
                            'actual_invested' => 0,
                            'projected_return' => 0,
                            'total_deposit_dashboard' => 0,
                        ]);
                        $count++;
                    }
                }
            }
        }

        return back()->with('status', "Auto-populated $count staking plans from Binance.");
    }
}
