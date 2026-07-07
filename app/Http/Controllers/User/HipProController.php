<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\HipPlan;
use Illuminate\Http\Request;

class HipProController extends Controller
{
    public function dashboard()
    {
        $vehicles = HipPlan::all()->groupBy('vehicle_type');
        $usdBalance = Balance::where('user_id', auth()->id())->where('symbol', 'USD')->first();
        $balance = $usdBalance ? $usdBalance->amount : 0;

        return view('user.hip-pro.dashboard', compact('vehicles', 'balance'));
    }

    public function deployCapital(Request $request, $vehicle, $tier_level)
    {
        $plan = HipPlan::where('vehicle_type', $vehicle)
            ->where('tier_level', $tier_level)
            ->firstOrFail();

        $user = auth()->user();
        $usdBalance = Balance::where('user_id', $user->id)->where('symbol', 'USD')->first();
        $balanceAmount = $usdBalance ? $usdBalance->amount : 0;

        // Perform balance check
        if ($balanceAmount < $plan->min_investment) {
            return response()->json(['status' => 'error', 'message' => 'Insufficient balance to deploy into this institutional tier.']);
        }

        // Logic for subscribing to the plan would go here.
        // Deduct balance
        if ($usdBalance) {
            $usdBalance->amount -= $plan->min_investment;
            $usdBalance->save();
        }

        return response()->json(['status' => 'success', 'message' => "Successfully deployed \${$plan->min_investment} into {$vehicle} - {$tier_level}."]);
    }
}
