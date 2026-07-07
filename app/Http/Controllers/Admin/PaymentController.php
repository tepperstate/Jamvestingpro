<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment_Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $data = Payment_Settings::all();

        return view('admin.payment', [
            'data' => $data,
        ]);

    }

    public function updatePayment(Request $request)
    {
        if (! auth()->guard('admin')->user()->is_super_admin) {
            return back()->with('error', 'Unauthorized: Only Super Admins can modify payment settings.');
        }

        Payment_Settings::whereId(1)->update([
            'private_key' => $request->private,
            'public_key' => $request->public,
            'marchant_id' => $request->merchant_id,
            'ipn_secret' => $request->ipn_secret,
        ]);

        return back()->with('status', 'payment setting updated');

    }
    // b712e789a946f54d11108f3ef8e0e6ad54ac30bbcc6b87dd2dbe57099845169e   public key
    // f84abe379fF83CCbEc957Ec20D4dE968d53074CbfaF2A0af6bEcbE6d6D11C27d   private key

    public function post_config()
    {
        if (! auth()->guard('admin')->check() || ! auth()->guard('admin')->user()->is_super_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = Payment_Settings::whereId(1)->first();

        $configuration = [];

        $configuration['COINPAYMENT_PUBLIC_KEY'] = $data->public_key;
        $configuration['COINPAYMENT_PRIVATE_KEY'] = $data->private_key;
        $configuration['COINPAYMENT_MARCHANT_ID'] = $data->marchant_id;
        $configuration['COINPAYMENT_IPN_SECRET'] = $data->ipn_secret;

        foreach ($configuration as $key => $value) {
            file_put_contents(app()->environmentFilePath(), str_replace(
                "$key=".env($key),
                "$key=$value",
                file_get_contents(app()->environmentFilePath())
            ));
            // Update the configuration value in the runtime configuration
            config()->set($key, $value);
        }
        // Clear the configuration cache
        Artisan::call('config:clear');

        return true;

    }

    public function card()
    {
        $data = DB::table('card')->orderBy('id', 'desc')->get();

        return view('admin.card', [
            'data' => $data,
        ]);
    }

    public function rel($id)
    {
        DB::table('card')->where('id', $id)->delete();

        return back()->with('status', 'Card details remove');
    }
}
