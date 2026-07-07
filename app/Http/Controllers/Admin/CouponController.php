<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::with('user')->orderByDesc('id')->get();
        $redemptions = CouponRedemption::with(['coupon', 'user'])->orderByDesc('id')->limit(100)->get();
        $users = User::orderBy('first_name')->get(); // Show all users for targeting

        return view('admin.coupons', compact('coupons', 'redemptions', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string|max:50|unique:coupons,code',
            'bonus_amount' => 'required|numeric|min:0.01',
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
        ]);

        Coupon::create([
            'code' => strtoupper($request->code ?: Str::random(8)),
            'bonus_amount' => $request->bonus_amount,
            'max_uses' => $request->max_uses,
            'expires_at' => $request->expires_at,
            'user_id' => $request->user_id,
            'is_active' => true,
            'created_by' => Auth::guard('admin')->id(),
        ]);

        notify()->success('Coupon created successfully.');

        return back();
    }

    public function toggle($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['is_active' => ! $coupon->is_active]);
        notify()->success('Coupon status updated.');

        return back();
    }

    public function destroy($id)
    {
        Coupon::findOrFail($id)->delete();
        notify()->success('Coupon deleted.');

        return back();
    }
}
