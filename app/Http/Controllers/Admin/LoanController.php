<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanPlan;
use App\Models\LoanPosition;
use App\Services\BinancePriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    public function plansIndex()
    {
        $plans = LoanPlan::orderBy('id', 'desc')->get();

        return view('admin.loan_plans', compact('plans'));
    }

    public function storePlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'collateral_asset' => 'required|string',
            'loan_asset' => 'required|string',
            'max_ltv' => 'required|numeric',
            'liquidation_ltv' => 'required|numeric',
            'interest_rate_daily' => 'required|numeric',
            'duration_days' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Validation failed: '.$validator->errors()->first());
        }

        $plan = new LoanPlan;
        $plan->name = $request->name;
        $plan->collateral_asset = $request->collateral_asset;
        $plan->loan_asset = $request->loan_asset;
        $plan->max_ltv = $request->max_ltv;
        $plan->liquidation_ltv = $request->liquidation_ltv;
        $plan->interest_rate_daily = $request->interest_rate_daily;
        $plan->min_collateral = $request->min_collateral ?? 0;
        $plan->max_loan = $request->max_loan ?? 0;
        $plan->collateral_price = $request->collateral_price ?? 0;
        $plan->duration_days = $request->duration_days;
        $plan->status = $request->status ?? 'active';

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/loans'), $imageName);
            $plan->image = 'uploads/loans/'.$imageName;
        }

        $plan->save();

        return back()->with('success', 'Loan plan created successfully.');
    }

    public function updatePlan(Request $request, $id)
    {
        $plan = LoanPlan::findOrFail($id);

        if ($request->has('collateral_price')) {
            $plan->collateral_price = $request->collateral_price;
        }
        if ($request->has('liquidation_ltv')) {
            $plan->liquidation_ltv = $request->liquidation_ltv;
        }
        if ($request->has('status')) {
            $plan->status = $request->status;
        }

        $plan->save();

        return back()->with('success', 'Loan plan updated successfully.');
    }

    public function destroyPlan($id)
    {
        LoanPlan::findOrFail($id)->delete();

        return back()->with('success', 'Loan plan deleted.');
    }

    public function positionsIndex()
    {
        $positions = LoanPosition::with(['user', 'plan'])->orderBy('id', 'desc')->get();

        return view('admin.loan_positions', compact('positions'));
    }

    public function updatePosition(Request $request, $id)
    {
        $position = LoanPosition::findOrFail($id);

        if ($request->has('current_ltv')) {
            $position->current_ltv = $request->current_ltv;
        }
        if ($request->has('admin_status')) {
            $position->admin_status = $request->admin_status;
        }
        if ($request->has('status')) {
            $position->status = $request->status;
        }

        $position->save();

        return back()->with('success', 'Loan position updated successfully.');
    }

    public function syncFromBinance(BinancePriceService $binanceService)
    {
        $assets = ['BTC', 'ETH', 'BNB', 'SOL', 'XRP', 'ADA'];
        $priceMap = $binanceService::getPriceMap() ?? [];
        $count = 0;

        foreach ($assets as $symbol) {
            $binanceSymbol = $symbol.'USDT';
            if (array_key_exists($binanceSymbol, $priceMap)) {
                $exists = LoanPlan::where('collateral_asset', $symbol)->where('loan_asset', 'USDT')->exists();
                if (! $exists) {
                    LoanPlan::create([
                        'name' => "$symbol Backed Loan",
                        'collateral_asset' => $symbol,
                        'loan_asset' => 'USDT',
                        'max_ltv' => 65.00,
                        'liquidation_ltv' => 80.00,
                        'interest_rate_daily' => 0.015,
                        'duration_days' => 30,
                        'status' => 'active',
                        'buffer_percent' => 0,
                        'per_withdrawal_percent' => 0,
                        'min_collateral' => 0,
                        'max_loan' => 0,
                        'collateral_price' => 0,
                    ]);
                    $count++;
                }
            }
        }

        return back()->with('success', "Auto-populated $count loan plans from Binance.");
    }
}
