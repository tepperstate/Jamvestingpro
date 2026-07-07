<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserCodeController extends Controller
{
    public function update_code_name(Request $request)
    {
        User::where('id', $request->user_id)->update([
            'code_one' => $request->code_one,
            'code_two' => $request->code_two,
            'code_three' => $request->code_three,
        ]);

        return back()->with('status', 'Code name updated');
    }

    public function update_code_generate(Request $request)
    {
        User::where('id', $request->user_id)->update([
            'upgrade_code' => Str::random(6),
            'tax_code' => Str::random(6),
            'demorage' => Str::random(6),
        ]);

        return back()->with('status', 'Code generated');
    }

    public function upgrade_code_check(Request $request)
    {
        $data = User::where('id', $request->user)->first();

        if ($data->upgrade_code_check == 'on') {
            User::where('id', $request->user)->update([
                'upgrade_code_check' => 'off',
            ]);
        } else {
            User::where('id', $request->user)->update([
                'upgrade_code_check' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function tax_code_check(Request $request)
    {
        $data = User::where('id', $request->user)->first();
        if ($data->tax_code_check == 'on') {
            User::where('id', $request->user)->update([
                'tax_code_check' => 'off',
            ]);
        } else {
            User::where('id', $request->user)->update([
                'tax_code_check' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function demorage_check(Request $request)
    {
        $data = User::where('id', $request->user)->first();
        if ($data->demorage_check == 'on') {
            User::where('id', $request->user)->update([
                'demorage_check' => 'off',
            ]);
        } else {
            User::where('id', $request->user)->update([
                'demorage_check' => 'on',
            ]);
        }

        return response()->json(['status' => true]);

    }

    public function generate_user_codes(Request $request)
    {
        $userId = $request->user_id;
        $codes = [];

        if ($request->has('generate_clearance') || $request->has('generate_all')) {
            $codes['upgrade_code'] = Str::random(6);
        }
        if ($request->has('generate_tax') || $request->has('generate_all')) {
            $codes['tax_code'] = Str::random(6);
        }
        if ($request->has('generate_liquidation') || $request->has('generate_all')) {
            $codes['demorage'] = Str::random(6);
        }

        if (! empty($codes)) {
            User::where('id', $userId)->update($codes);
        }

        $user = User::select('id', 'upgrade_code', 'tax_code', 'demorage', 'upgrade_code_check', 'tax_code_check', 'demorage_check')->find($userId);

        return response()->json(['status' => true, 'user' => $user, 'message' => 'Codes generated successfully']);
    }

    public function toggle_user_code_check(Request $request)
    {
        $userId = $request->user_id;
        $field = $request->field; // upgrade_code_check, tax_code_check, demorage_check

        $allowed = ['upgrade_code_check', 'tax_code_check', 'demorage_check'];
        if (! in_array($field, $allowed)) {
            return response()->json(['status' => false, 'message' => 'Invalid field']);
        }

        $user = User::find($userId);
        $newVal = $user->$field == 'on' ? 'off' : 'on';
        User::where('id', $userId)->update([$field => $newVal]);

        return response()->json(['status' => true, 'value' => $newVal, 'message' => 'Toggle updated']);
    }
}
