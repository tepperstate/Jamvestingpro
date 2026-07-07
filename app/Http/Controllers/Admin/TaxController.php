<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxController extends Controller
{
    public function index()
    {
        $data = DB::table('tax_proof')->orderBy('id', 'desc')->get();

        return view('admin.tax_proof', [
            'data' => $data,
        ]);
    }

    public function approve_one($id)
    {
        DB::table('tax_proof')->where('id', $id)->update([
            'status_one' => '1',
        ]);

        return back()->with('status', 'Tax approved');

    }

    public function approve_two($id)
    {
        DB::table('tax_proof')->where('id', $id)->update([
            'status_two' => '1',
        ]);

        return back()->with('status', 'Tax approved');

    }

    public function delete($id)
    {
        DB::table('tax_proof')->where('id', $id)->delete();

        return back()->with('status', 'Tax clearance recode deleted');
    }

    public function onOfftax(Request $request)
    {

        $user = User::whereId($request->user)->first();

        if ($user->tax == '0') {
            User::whereId($request->user)->update([
                'tax' => '1',
            ]);
        } else {
            User::whereId($request->user)->update([
                'tax' => '0',
            ]);
        }

        return response()->json(['status' => true]);
    }
}
