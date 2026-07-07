<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CryptoController extends Controller
{
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

    public function update_usdt(Request $request)
    {
        $data = DB::table('manuel_deposit_usd')->where('id', 1)->first();

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

            DB::table('manuel_deposit_usd')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Usdt address added');
        }

        DB::table('manuel_deposit_usd')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Usdt address added');
    }

    public function update_solana(Request $request)
    {
        $data = DB::table('manuel_deposit_solana')->where('id', 1)->first();

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

            DB::table('manuel_deposit_solana')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Solana address added');
        }

        DB::table('manuel_deposit_solana')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Solana address added');
    }

    public function update_eth(Request $request)
    {
        $data = DB::table('manuel_deposit_eth')->where('id', 1)->first();

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

            DB::table('manuel_deposit_eth')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'Eth address added');
        }

        DB::table('manuel_deposit_eth')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'Eth address added');
    }

    public function update_doge(Request $request)
    {
        $data = DB::table('manuel_dogecoin')->where('id', 1)->first();

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

            DB::table('manuel_dogecoin')->where('id', 1)->update([
                'address' => $request->address,
                'image' => $newfilename,
            ]);

            return back()->with('status', 'DogeCoin address added');
        }

        DB::table('manuel_dogecoin')->where('id', 1)->update([
            'address' => $request->address,
            'image' => $data->image,
        ]);

        return back()->with('status', 'DogeCoin address added');
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

        return back()->with('status', 'Btc address added');
    }

    public function update_ltc(Request $request)
    {
        $data = DB::table('manuel_litcoin')->where('id', 1)->first();

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
            'image' => $data->image,
        ]);

        return back()->with('status', 'Dash address added');
    }

    public function add_buy() // buy crypto page
    {$data = DB::table('buys')->orderByDesc('id')->paginate(20);

        return view('admin.buy', [
            'data' => $data,
        ]);
    }

    public function store_crypto(Request $request)   // store crypto
    {$this->validate($request, [
            'name' => 'required|unique:buys,name',
        ]);
        $newfilename = 'default_crypto.png';
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
        }

        DB::table('buys')->insert([
            'name' => $request->name,
            'image' => $newfilename,
            'url' => $request->url,
            'min' => $request->min,
            'max' => $request->max,
        ]);

        return back()->with('status', 'New cryto site added');
    }

    public function show_crypto($id) // show single crypto page
    {$data = DB::table('buys')->find($id);

        return view('admin.show_crypto', [
            'data' => $data,
        ]);
    }

    public function edit_crypto(Request $request) // edit crypto
    {$data = DB::table('buys')->find($request->id);
        $updateData = [
            'name' => $request->name,
            'url' => $request->url,
            'min' => $request->min,
            'max' => $request->max,
        ];

        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $updateData['image'] = $newfilename;
        }

        DB::table('buys')->where('id', $request->id)->update($updateData);

        return back()->with('status', 'edited successfully');
    }

    public function delete_crypto($id)
    {
        DB::table('buys')->where('id',$id)->delete();

        return back()->with('status','delete successfully');
    }
}
