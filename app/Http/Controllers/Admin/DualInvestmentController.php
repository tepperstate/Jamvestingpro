<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DualInvestmentProduct;
use App\Models\DualInvestmentSubscription;
use App\Services\BinancePriceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DualInvestmentController extends Controller
{
    public function index()
    {
        $products = DualInvestmentProduct::withCount('dualInvestmentSubscriptions')->orderBy('id', 'desc')->get();

        return view('admin.dual_products', compact('products'));
    }

    public function syncProducts()
    {
        $popularPlans = [
            ['name' => 'BTC Sell High', 'underlying_asset' => 'BTC', 'deposit_asset' => 'BTC', 'direction' => 'up', 'duration_days' => 7, 'base_apy' => 25],
            ['name' => 'ETH Sell High', 'underlying_asset' => 'ETH', 'deposit_asset' => 'ETH', 'direction' => 'up', 'duration_days' => 14, 'base_apy' => 30],
            ['name' => 'BTC Buy Low', 'underlying_asset' => 'BTC', 'deposit_asset' => 'USDT', 'direction' => 'down', 'duration_days' => 7, 'base_apy' => 20],
            ['name' => 'ETH Buy Low', 'underlying_asset' => 'ETH', 'deposit_asset' => 'USDT', 'direction' => 'down', 'duration_days' => 14, 'base_apy' => 28],
            ['name' => 'SOL Sell High', 'underlying_asset' => 'SOL', 'deposit_asset' => 'SOL', 'direction' => 'up', 'duration_days' => 3, 'base_apy' => 45],
            ['name' => 'BNB Buy Low', 'underlying_asset' => 'BNB', 'deposit_asset' => 'USDT', 'direction' => 'down', 'duration_days' => 10, 'base_apy' => 22],
        ];

        // Ensure we have current prices
        $data = BinancePriceService::fetchAll();
        $priceMap = $data['priceMap'] ?? [];

        foreach ($popularPlans as $plan) {
            $currentPrice = $priceMap[$plan['underlying_asset'].'USDT'] ?? 50000; // Fallback
            $strikeModifier = $plan['direction'] === 'up' ? 1.05 : 0.95; // 5% out of the money
            $strikePrice = $currentPrice * $strikeModifier;

            DualInvestmentProduct::firstOrCreate(
                ['name' => $plan['name'], 'duration_days' => $plan['duration_days']],
                [
                    'underlying_asset' => $plan['underlying_asset'],
                    'deposit_asset' => $plan['deposit_asset'],
                    'direction' => $plan['direction'],
                    'strike_price' => round($strikePrice, 2),
                    'apy' => $plan['base_apy'] + 10, // Added extra 10%
                    'min_amount' => 10,
                    'max_amount' => 100000,
                    'settlement_date' => Carbon::now()->addDays($plan['duration_days']),
                    'status' => 'active',
                    'buffer_percent' => 0,
                    'per_withdrawal_percent' => 0,
                ]
            );
        }

        return back()->with('status', 'Successfully populated popular Dual Investment plans with +10% APY.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'underlying_asset' => 'required|string',
            'deposit_asset' => 'required|string',
            'direction' => 'required|in:up,down',
            'strike_price' => 'required|numeric',
            'apy' => 'required|numeric',
            'duration_days' => 'required|integer|min:1',
            'min_amount' => 'required|numeric',
            'max_amount' => 'required|numeric',
            'settlement_date' => 'required|date',
        ]);

        $data = $request->except('_token');
        $data['status'] = 'subscribing';
        $data['buffer_percent'] = $request->buffer_percent ?? 20.00;
        $data['per_withdrawal_percent'] = $request->per_withdrawal_percent ?? 5.00;

        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $data['image'] = $newfilename;
        }

        DualInvestmentProduct::create($data);

        return back()->with('status', 'Dual Investment Product created successfully.');
    }

    public function update(Request $request)
    {
        $product = DualInvestmentProduct::findOrFail($request->id);

        $data = $request->except('_token');
        if (! isset($data['buffer_percent'])) {
            $data['buffer_percent'] = 20.00;
        }
        if (! isset($data['per_withdrawal_percent'])) {
            $data['per_withdrawal_percent'] = 5.00;
        }

        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $data['image'] = $newfilename;
        }

        $product->update($data);

        return back()->with('status', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        DualInvestmentProduct::findOrFail($id)->delete();

        return back()->with('status', 'Product deleted.');
    }

    public function subscriptions()
    {
        $subscriptions = DualInvestmentSubscription::with(['user', 'product'])->orderBy('id', 'desc')->paginate(20);

        return view('admin.dual_subscriptions', compact('subscriptions'));
    }

    public function updateSubscription(Request $request)
    {
        $sub = DualInvestmentSubscription::findOrFail($request->id);

        $data = $request->only(['admin_status', 'actual_return', 'settlement_asset', 'settlement_amount']);
        $sub->update($data);

        return back()->with('status', 'Subscription rigged/updated successfully.');
    }

    public function syncFromBinance(BinancePriceService $binanceService)
    {
        $topAssets = ['BTC', 'ETH', 'BNB', 'SOL'];
        $priceMap = $binanceService::getPriceMap() ?? [];

        foreach ($topAssets as $asset) {
            $symbol = $asset.'USDT';
            $currentPrice = $priceMap[$symbol] ?? 100.0;

            // Create UP product
            DualInvestmentProduct::updateOrCreate(
                ['name' => "Buy Low $asset", 'direction' => 'up', 'underlying_asset' => $asset],
                [
                    'deposit_asset' => 'USDT',
                    'strike_price' => $currentPrice * 0.95,
                    'apy' => rand(4000, 12000) / 100, // 40-120% APY
                    'duration_days' => rand(1, 14),
                    'min_amount' => 100,
                    'max_amount' => 100000,
                    'settlement_date' => Carbon::now()->addDays(rand(1, 14)),
                    'status' => 'active',
                    'buffer_percent' => 0,
                    'per_withdrawal_percent' => 0,
                ]
            );

            // Create DOWN product
            DualInvestmentProduct::updateOrCreate(
                ['name' => "Sell High $asset", 'direction' => 'down', 'underlying_asset' => $asset],
                [
                    'deposit_asset' => $asset,
                    'strike_price' => $currentPrice * 1.05,
                    'apy' => rand(4000, 12000) / 100,
                    'duration_days' => rand(1, 14),
                    'min_amount' => 0.1,
                    'max_amount' => 100,
                    'settlement_date' => Carbon::now()->addDays(rand(1, 14)),
                    'status' => 'active',
                    'buffer_percent' => 0,
                    'per_withdrawal_percent' => 0,
                ]
            );
        }

        return back()->with('status', 'Dual Investment products synced from Binance successfully.');
    }
}
