<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RewardsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $balance = $user->balance;
        $redemptions = CouponRedemption::where('user_id', $user->id)
            ->with('coupon')
            ->orderByDesc('id')
            ->get();

        return view('exchange.rewards', compact('user', 'balance', 'redemptions'));
    }

    public function redeem(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);

        $user = Auth::user();
        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

        if (! $coupon) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Invalid promo code! Please enter a valid code.']);
            }

            return back()->with('error', 'Invalid promo code! Please enter a valid code.');
        }

        if (! $coupon->isValid()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This coupon has expired or reached its maximum uses.']);
            }

            return back()->with('error', 'This coupon has expired or reached its maximum uses.');
        }

        // Check if user already redeemed this coupon
        $already = CouponRedemption::where('coupon_id', $coupon->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($already) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You have already redeemed this coupon.']);
            }

            return back()->with('error', 'You have already redeemed this coupon.');
        }

        DB::transaction(function () use ($coupon, $user) {
            // Credit bonus balance
            $balance = $user->balance;
            $balance->bonus_balance = ($balance->bonus_balance ?? 0) + $coupon->bonus_amount;
            $balance->save();

            // Record redemption
            CouponRedemption::create([
                'coupon_id' => $coupon->id,
                'user_id' => $user->id,
                'bonus_credited' => $coupon->bonus_amount,
            ]);

            // Increment usage
            $coupon->increment('times_used');
        });

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Coupon redeemed! $'.number_format($coupon->bonus_amount, 2).' bonus credited to your trading balance.']);
        }

        return back()->with('success', 'Coupon redeemed! $'.number_format($coupon->bonus_amount, 2).' bonus credited to your trading balance.');
    }
}
