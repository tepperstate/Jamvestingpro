<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (Auth::guard('web')->attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            $user = Auth::guard('web')->user();

            return response()->json([
                'success' => true,
                'user' => $user,
                'status' => 'login',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The provided credentials do not match our records.',
        ], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'country' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $defaultPackage = DB::table('default_package')->where('id', 1)->first();

        $data = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'package_plan' => $defaultPackage ? $defaultPackage->plan : 'Starter',
            'package_id' => 1,
            'withdrawal' => 'on',
            'password' => Hash::make($request->password),
            'traded' => 0,
            'highest_investment' => 0,
            'status' => false,
            'user_id' => substr(sha1(uniqid(rand(), true)), 0, 10),
            'currency' => $request->currency ?? 'USD',
            'is_demo' => 0,
        ]);

        Auth::guard('web')->login($data);

        $coins = ['USD', 'BTC', 'XLM', 'ETH', 'SOL', 'LTC', 'ADA', 'LINK'];
        $names = ['Dollar', 'Bitcoin', 'Stellar', 'Ethereum', 'Solana', 'Litecoin', 'Cardano', 'Chainlink'];

        foreach ($coins as $index => $symbol) {
            Balance::create([
                'user_id' => $data->id,
                'demo' => $symbol === 'USD' ? 10000 : 0,
                'amount' => 0,
                'bitcoin' => 0,
                'referral' => 0,
                'name' => $names[$index],
                'symbol' => $symbol,
                'bonus' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'user' => $data,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true]);
    }
}
