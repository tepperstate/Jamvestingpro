<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MutualFund;
use App\Models\MutualFundInvestment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MutualFundController extends Controller
{
    public function index()
    {
        $funds = MutualFund::withCount('activeInvestments')
            ->orderBy('id', 'desc')
            ->get();

        $totalAUM = MutualFund::sum('total_aum');
        $totalInvestors = MutualFundInvestment::where('status', 'active')->distinct('user_id')->count('user_id');

        $activeInvestments = MutualFundInvestment::with(['user', 'fund'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.mutual_funds', compact('funds', 'totalAUM', 'totalInvestors', 'activeInvestments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_investment' => 'required|numeric|min:0',
            'risk_level' => 'required|in:low,medium,high',
            'annual_return' => 'required|numeric',
            'nav_price' => 'required|numeric|min:0.01',
        ]);

        $data = $request->only(['name', 'description', 'min_investment', 'risk_level', 'annual_return', 'nav_price', 'buffer_percent', 'per_withdrawal_percent']);
        $data['buffer_percent'] = $data['buffer_percent'] ?? 20.00;
        $data['per_withdrawal_percent'] = $data['per_withdrawal_percent'] ?? 5.00;
        $data['inception_date'] = Carbon::now();
        $data['status'] = 'active';

        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $data['image'] = $newfilename;
        } else {
            $data['image'] = 'default_investment.png';
        }

        MutualFund::create($data);

        return back()->with('status', 'Mutual Fund created successfully.');
    }

    public function update(Request $request)
    {
        $fund = MutualFund::findOrFail($request->id);

        $data = $request->only(['name', 'description', 'min_investment', 'risk_level', 'annual_return', 'nav_price', 'status', 'buffer_percent', 'per_withdrawal_percent']);
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

        $fund->update($data);

        return back()->with('status', 'Fund updated successfully.');
    }

    public function destroy($id)
    {
        MutualFund::findOrFail($id)->delete();

        return back()->with('status', 'Fund deleted.');
    }

    public function simulate(Request $request)
    {
        $fund = MutualFund::findOrFail($request->id);

        // Simulate NAV change
        $changePercent = $request->change_percent ?? mt_rand(-500, 1500) / 100;
        $newNav = $fund->nav_price * (1 + ($changePercent / 100));
        $newAum = MutualFundInvestment::where('fund_id', $fund->id)
            ->where('status', 'active')
            ->sum('units') * $newNav;

        $fund->update([
            'nav_price' => round($newNav, 4),
            'total_aum' => round($newAum, 2),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'NAV updated to $'.number_format($newNav, 4)." ({$changePercent}% change)",
                'nav' => $newNav,
                'aum' => $newAum,
            ]);
        }

        return back()->with('status', 'Performance simulated: NAV → $'.number_format($newNav, 4));
    }

    public function investors($id)
    {
        $fund = MutualFund::findOrFail($id);
        $investors = MutualFundInvestment::with('user')
            ->where('fund_id', $id)
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.mutual_fund_investors', compact('fund', 'investors'));
    }

    public function updateInvestment(Request $request)
    {
        $inv = MutualFundInvestment::with('fund')->findOrFail($request->id);

        $data = $request->only(['status']);

        if ($request->filled('gain_loss_adjustment')) {
            $adjustment = (float) $request->gain_loss_adjustment;
            if ($inv->fund && $inv->fund->nav_price > 0) {
                // Current value based on NAV
                $currentValue = $inv->units * $inv->fund->nav_price;
                $newValue = max(0, $currentValue + $adjustment);

                // Adjust units so the new current value matches the gain/loss
                $data['units'] = $newValue / $inv->fund->nav_price;
            }
        }

        $inv->update($data);

        return back()->with('status', 'Investment updated successfully to simulate P/L.');
    }
}
