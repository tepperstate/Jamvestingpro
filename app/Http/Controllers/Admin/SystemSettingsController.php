<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemSettingsController extends Controller
{
    public function updateDash(Request $request)
    {
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

            DB::table('manuel_dash')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Dash address added');
        }

        DB::table('manuel_dash')->where('id', 1)->update([
            'address' => $request->address,
        ]);

        return back()->with('status', 'Dash address added');
    }

    public function storeTrader(Request $request)
    {
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('image')->storeAs('image', $newfilename, 'public');
            Trader::create([
                'image' => $newfilename,
                'name' => $request->name,
                'country' => $request->country,
                'percentage' => $request->percentage,
                'win' => 'win',
                'total_copier' => $request->total_copier,
                'total_trade' => '1',
                'twitter' => '',
                'facebook' => '',
                'instagram' => '',
                'linkedin' => '',
                'equity' => '0',
                'min_loss' => $request->min_loss,
                'min_win' => $request->min_win,
                'max_win' => $request->max_win,
                'action' => '',
                'days' => '3000',
                'des' => '0',
                'buffer_percent' => $request->buffer_percent ?? 20.00,
                'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
            ]);
        }

        return back()->with('status', 'New Traded added');
    }

    public function trade(Request $request) // trade
    {$option = $request->expiretime;

        $formattedValue = '';

        switch ($option) {
            case '1':
                $formattedValue = '1min';
                break;
            case '5':
                $formattedValue = '5mins';
                break;
            case '10':
                $formattedValue = '10mins';
                break;
            case '15':
                $formattedValue = '15mins';
                break;
            case '30':
                $formattedValue = '30mins';
                break;
            case '60':
                $formattedValue = '1 hour';
                break;
            case '120':
                $formattedValue = '2 hours';
                break;
            case '1440':
                $formattedValue = '24 hours';
                break;
            case '10080':
                $formattedValue = '7 days';
                break;
        }
    }
}
