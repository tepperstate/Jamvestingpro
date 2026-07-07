<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Balance;
use App\Models\Deposit;
use App\Models\Doc;
use App\Models\Email;
use App\Models\Noti;
use App\Models\OnboardingResponse;
use App\Models\Order;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Trader;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function kyc()
    {
        return view('admin.kyc', [
            'kyc' => Doc::orderBy('id', 'DESC')->paginate(20),
        ]);
    }

    public function approved_Kyc(Request $request)
    {

        Doc::where(['id' => $request->id])->update([
            'status' => 'approved',
        ]);

        User::whereId($request->user_id)->update([
            'indentify_verified' => 1,
            'residency_verified' => 1,
            'kyc_status' => 'approved',
        ]);

        return response()->json(['status' => 'success', 'message' => 'KYC approved successfully']);
    }

    public function delete_kyc(Request $request)
    {

        Doc::where(['id' => $request->id])->delete();

        User::whereId($request->user_id)->update([
            'indentify_verified' => 0,
            'residency_verified' => 0,
            'kyc_status' => 'rejected',
        ]);

        return response()->json(['status' => 'success', 'message' => 'KYC rejected successfully']);
    }

    public function updateUser(Request $request)
    {
        if ($request->status == 1) {
            User::where(['id' => $request->id])->update([
                'status' => 0,
            ]);
        } else {
            User::where(['id' => $request->id])->update([
                'status' => 1,
            ]);
        }
    }

    public function update_user_level(Request $request)
    {
        User::where('id', $request->user_id)->update([
            'level' => $request->level,
        ]);

        return back()->with('status', 'User withdrawal level updated');
    }

    public function getSingleUser($user_id)
    {
        $user = User::findOrFail($user_id);
        $b = Asset::all();

        $trade = Order::where('user_id', $user->id)->orderBy('id', 'desc')->get();
        // Prioritize USD balance for the main display
        $c = Balance::where('user_id', $user->id)->where('symbol', 'USD')->first();
        if (! $c) {
            $c = Balance::where('user_id', $user->id)->first();
        }
        if (! $c) {
            $c = Balance::create([
                'user_id' => $user->id,
                'symbol' => 'USD',
                'amount' => 0,
                'demo' => 0,
            ]);
        }

        $total_deposit = Deposit::where(['user_id' => $user->id, 'status' => 'success'])->sum('amount');
        $total_trade = Order::where(['user_id' => $user->id])->sum('amount');
        $total_withdrawal = Withdrawal::where(['user_id' => $user->id, 'status' => 'pending'])->sum('amount');

        $signal = DB::table('purchase_signal')->whereUserId($user->id)
            ->join('signals', 'purchase_signal.signal_id', '=', 'signals.id')
            ->select('purchase_signal.id as id', 'signals.day as day', 'purchase_signal.signal_id as signal_id', 'purchase_signal.name as name', 'signals.image as image', 'purchase_signal.amount as amount', 'signals.min as min', 'signals.max as max', 'signals.day as day', 'signals.used as used')->get();

        $bot = DB::table('purchase_bot')->whereUserId($user->id)
            ->join('bots', 'purchase_bot.bot_id', '=', 'bots.id')
            ->select('purchase_bot.bot_id as id', 'purchase_bot.name as name', 'bots.day as day', 'purchase_bot.amount as amount')->get();

        $message = Email::whereUserId($user->id)->orderBy('id', 'desc')->limit(5)->get();
        $stock = DB::table('stock_balance')->whereUserId($user->id)->orderBy('id', 'desc')->limit(30)->get();

        $noti = Noti::whereUserId($user->id)->orderBy('id', 'desc')->limit(50)->get();

        $depp_message = DB::table('deposit_message')->where('user_id', $user->id)->first();
        $with_message = DB::table('withdrawal_message')->where('user_id', $user->id)->first();

        $trader = Trader::orderBy('id', 'desc')->get();
        $package = Package::orderBy('id', 'asc')->get();
        $level = DB::table('level')->orderBy('id', 'asc')->get();
        $question = DB::table('questions')->where('user_id', $user->id)->first();

        $security = DB::table('security')->where('user_id', $user->id)->first();

        $count_deposit = Deposit::where(['user_id' => $user->id])->count();
        $count_withdrawal = Withdrawal::where(['user_id' => $user->id])->count();
        $payment = Payment::where('user_id', $user->id)->orderBy('id', 'desc')->get();

        $deposit_history = Deposit::where(['user_id' => $user->id])->orderBy('id', 'desc')->get();
        $withdrawal_history = Withdrawal::where(['user_id' => $user->id])->orderBy('id', 'desc')->get();

        $onboarding_responses = OnboardingResponse::where('user_id', $user->id)
            ->with('question')
            ->get();

        return view('admin.user_details', [
            'deposit_history' => $deposit_history,
            'withdrawal_history' => $withdrawal_history,
            'count_withdrawal' => $count_withdrawal,
            'payment' => $payment,
            'count_deposit' => $count_deposit,
            'security' => $security,
            'level' => $level,
            'question' => $question,
            'package' => $package,
            'trader' => $trader,
            'bate' => $b,
            'user' => $user,
            'data' => $trade,
            'c' => $c,
            'total_deposit' => $total_deposit,
            'total_trade' => $total_trade,
            'total_withdrawal' => $total_withdrawal,
            'signal' => $signal,
            'bot' => $bot,
            'message' => $message,
            'stock' => $stock,
            'noti' => $noti,
            'deposit_message' => $depp_message,
            'withdrawal_message' => $with_message,
            'onboarding_responses' => $onboarding_responses,
        ]);
    }

    public function userLock(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->emergency == 'on') {
            User::whereId($request->user)->update([
                'emergency' => 'off',
            ]);
        } else {
            User::whereId($request->user)->update([
                'emergency' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function updateAdmin(Request $request)
    {
        if ($request->status == 1) {
            Admin::where(['id' => $request->id])->update([
                'status' => 0,
            ]);
        } else {
            Admin::where(['id' => $request->id])->update([
                'status' => 1,
            ]);
        }
    }

    public function store(Request $request)
    {

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => hash::make($request->password),
            'status' => 1,
        ]);

        return back()->with('status','New admin added');
    }
}
