<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function updateLitcoin(Request $request)
    {
        if ($request->hasFile('upload')) {
            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_litcoin')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'LTC address added');

        }

        DB::table('manuel_litcoin')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Btc address added');
    }

    public function update_dash(Request $request)
    {
        $data = DB::table('manuel_dash')->where('id', 1)->first();

        if ($request->hasFile('upload')) {
            $filename = $request->file('upload');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('upload')->storeAs('image', $newfilename, 'public');

            DB::table('manuel_dash')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'wallet setting updated');
        }

        DB::table('manuel_dash')->where('id', 1)->update([
            'address' => $request->address,
        ]);

        return back()->with('status', 'wallet setting updated');
    }

    public function site_template(Request $request)
    {

        DB::table('templates')->where('id', 1)->update([
            'template' => $request->data,
        ]);

    }

    public function onOffGoogle(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->is_2fa_enabled == '1') {
            User::whereId($request->user)->update([
                'is_2fa_enabled' => '0',
            ]);
        } else {
            User::whereId($request->user)->update([
                'is_2fa_enabled' => '1',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function onOffOtp(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->otp_enabled == '1') {
            User::whereId($request->user)->update([
                'otp_enabled' => '0',
            ]);
        } else {
            User::whereId($request->user)->update([
                'otp_enabled' => '1',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function onOffEmail(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->email_verified == '1') {
            User::whereId($request->user)->update([
                'email_verified' => '0',
            ]);
        } else {
            User::whereId($request->user)->update([
                'email_verified' => '1',
            ]);

            $text = [
                'greeting' => 'Hello '.$user->first_name,
                'subject' => 'Account Verification',
                'body' => 'Your account has been verified you can now enjoy our seemless trading experience',
                'data' => null,
                'url' => null,
                'thanks' => 'Thank you for choosing '.env('APP_NAME'),
            ];

            try {
                Notification::route('mail', $user->email)->notify(new DepositNotification($text));

            } catch (\Throwable $th) {
                // throw $th;
            }
        }

        return response()->json(['status' => true]);
    }

    public function update_engine_settings(Request $request)
    {
        DB::table('emergency')->where('id', 1)->update([
            'engine_mode' => $request->engine_mode,
            'win_rate' => $request->win_rate,
        ]);

        return back()->with('status', 'Trading engine settings updated successfully');
    }
}
