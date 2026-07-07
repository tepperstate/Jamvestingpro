<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Welcome;
use App\Models\Balance;
use App\Models\Referral;
use App\Models\User;
use App\Notifications\DepositNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class IndexController extends Controller
{
    public function start_page(Request $request)
    {
        $type = $request->type;
        $custodianName = $request->custodianName;

        session(['accountType' => $type]);
        session(['custodianName' => $custodianName]);

        return response()->json(['status' => 'Action type selection successful']);
    }

    public function signup(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'phone' => 'required',
            'country' => 'required',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = User::create([
            'type' => session('accountType'),
            'custodia' => session('custodianName'),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'package_plan' => DB::table('default_package')->where('id', 1)->first()->plan,
            'package_id' => 1,
            'withdrawal' => 'on',
            'image' => null,
            'password' => Hash::make($request->password),
            'show_password' => $request->password,
            'traded' => 0,
            'highest_investment' => 0,
            'status' => false,
            'user_id' => substr(sha1(uniqid(rand(), true)), 0, 10),
            'currency' => $request->currency,
            'is_demo' => 0,
        ]);

        auth()->login($data);

        if ($request->invitation_code) {
            $inviter = User::where('user_id', $request->invitation_code)->first();
            if ($inviter) {
                Referral::create([
                    'user_id' => $inviter->id,
                    'referral_id' => $data->id,
                    'referral_name' => $request->first_name,
                    'balance' => '0',
                ]);
            }
        }

        Balance::create([
            'user_id' => $data->id,
            'demo' => 10000,
            'amount' => 0,
            'bitcoin' => 0,
            'referral' => 0,
            'name' => 'Dollar',
            'symbol' => 'USD',
            'image' => null,
            'bonus' => 0,
        ]);

        Balance::create([
            'user_id' => $data->id,
            'demo' => 0,
            'amount' => 0,
            'bitcoin' => 0,
            'referral' => 0,
            'name' => 'Bitcoin',
            'symbol' => 'BTC',
            'image' => null,
            'bonus' => 0,
        ]);

        Balance::create([
            'user_id' => $data->id,
            'demo' => 0,
            'amount' => 0,
            'bitcoin' => 0,
            'referral' => 0,
            'name' => 'Stellar',
            'symbol' => 'XLM',
            'image' => null,
            'bonus' => 0,
        ]);

        Balance::create([
            'user_id' => $data->id,
            'demo' => 0,
            'amount' => 0,
            'bitcoin' => 0,
            'referral' => 0,
            'name' => 'Ethereum',
            'symbol' => 'ETH',
            'image' => null,
            'bonus' => 0,
        ]);

        Balance::create([
            'user_id' => $data->id,
            'demo' => 0,
            'amount' => 0,
            'bitcoin' => 0,
            'referral' => 0,
            'name' => 'Solana',
            'symbol' => 'SOL',
            'image' => null,
            'bonus' => 0,
        ]);

        Balance::create([
            'user_id' => $data->id,
            'demo' => 0,
            'amount' => 0,
            'bitcoin' => 0,
            'referral' => 0,
            'name' => 'Litecoin',
            'symbol' => 'LTC',
            'image' => null,
            'bonus' => 0,
        ]);

        Balance::create([
            'user_id' => $data->id,
            'demo' => 0,
            'amount' => 0,
            'bitcoin' => 0,
            'referral' => 0,
            'name' => 'Cardano',
            'symbol' => 'ADA',
            'image' => null,
            'bonus' => 0,
        ]);

        Balance::create([
            'user_id' => $data->id,
            'demo' => 0,
            'amount' => 0,
            'bitcoin' => 0,
            'referral' => 0,
            'name' => 'Chainlink',
            'symbol' => 'LINK',
            'image' => null,
            'bonus' => 0,
        ]);

        $text = [
            'greeting' => $request->first_name,
            'subject' => 'Your Registration was successful',
            'body' => 'Your registration on '.env('APP_NAME').' was successful, click on the button below to verify your account',
            'data' => 'Click Here',
            'url' => url('/verify', ['data' => $request->email]),
            'thanks' => 'Thank you for choosing '.env('APP_NAME'),
        ];
        try {
            Notification::route('mail', $request->email)->notify(new DepositNotification($text));
            Mail::to($request->email)->send(new Welcome);
        } catch (\Throwable $th) {
            // throw $th;
        }

        return response()->json(['status' => 'Your registration was successful, you can now login to access your trading account']);
    }

    public function resend()
    {
        $text = [
            'greeting' => auth()->guard('web')->user()->first_name,
            'subject' => 'Your Registration was successful',
            'body' => 'Your registration on '.env('APP_NAME').' was successfull, click on the button below to verify your account',
            'data' => 'Click Here',
            'url' => url('/dashboard/verify', ['data' => auth()->guard('web')->user()->email]),
            'thanks' => 'Thank you for choosing '.env('APP_NAME'),
        ];

        Notification::route('mail', auth()->guard('web')->user()->email)
            ->notify(new DepositNotification($text));

        return back()->with('status', 'Verification link was resend');
    }

    public function start()
    {
        return view('start');
    }
}
