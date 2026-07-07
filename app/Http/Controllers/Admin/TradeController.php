<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TradeController extends Controller
{
    public function stock()
    {
        $data = Stock_Trade::orderBy('id', 'desc')->paginate(20);
        $cash = DB::table('cash_app')->first();
        $bank = DB::table('bank')->where('id', 1)->first();

        return view('admin.stock', [
            'data' => $data,
            'cash' => $cash,
            'message' => $message,
            'bank' => $bank,
        ]);
    }

    public function wallet_deposit()
    {

        $cash = DB::table('cash_app')->first();
        $bank = DB::table('bank')->where('id', 1)->first();
        $btc = DB::table('manuel_deposit')->where('id', 1)->first();
        $usd = DB::table('manuel_deposit_usd')->where('id', 1)->first();
        $eth = DB::table('manuel_deposit_eth')->where('id', 1)->first();
        $solana = DB::table('manuel_deposit_solana')->where('id', 1)->first();

        $qrCode_btc = QrCode::size(200)->generate($btc->address);
        $qrCode_usd = QrCode::size(200)->generate($usd->address);
        $qrCode_eth = QrCode::size(200)->generate($eth->address);
        $qrCode_solana = QrCode::size(200)->generate($solana->address);

        return view('admin.wallet_deposit', [
            'btc_address' => $btc->address,
            'eth_address' => $eth->address,
            'usd_address' => $usd->address,
            'solana_address' => $btc->address,

            'cash' => $cash,
            'bank' => $bank,
            'btc' => $qrCode_btc,
            'usd' => $qrCode_usd,
            'eth' => $qrCode_eth,
            'solana' => $qrCode_solana,
        ]);

    }

    public function update_btc(Request $request)
    {
        $data = DB::table('manuel_deposit')->where('id', 1)->first();

        if ($request->hasFile('upload')) {
            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_deposit')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Btc address added');

        }

        DB::table('manuel_deposit')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Btc address added');
    }

    public function update_bch(Request $request)
    {
        $data = DB::table('manuel_bitcoin_cash')->where('id', 1)->first();

        if ($request->hasFile('upload')) {
            $request->validate([
                'upload' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);

            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_bitcoin_cash')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Bch address added');

        }

        DB::table('manuel_bitcoin_cash')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status','Btc address added');
    }
}
