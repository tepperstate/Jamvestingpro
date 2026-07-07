<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BotController extends Controller
{
    public function bots()
    {
        $data = Bot::orderBy('id', 'desc')->get();

        return view('admin.bot', [
            'data' => $data,
        ]);
    }

    public function addBot(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|max:2000',
        ]);
        $filename = $request->file('image');
        $newfilename = time().'.'.$filename->getClientOriginalExtension();

        $request->file('image')->storeAs('image', $newfilename, 'public');

        DB::table('bots')->insert([
            'name' => $request->name,
            'image' => $newfilename,
            'amount' => $request->amount,
            'buffer_percent' => $request->buffer_percent ?? 20.00,
            'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
            'min' => $request->min,
            'max' => $request->max,
            'day' => $request->daily,
            'total' => 0,
            'win' => $request->win,
            'loss' => $request->loss,
            'used' => rand(10000, 50000),
        ]);

        return back()->with('status', 'bot updated');
    }

    public function single_bots(Bot $bot) // single bot
    {return view('admin.single_bot', [
            'data' => $bot,
        ]);
    }

    public function edit_bot(Request $request)  // edit bot
    {$site = Bot::where('id', $request->id)->first();

        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'required|mimes:png|max:2000',
            ]);
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();

            $request->file('image')->storeAs('image', $newfilename, 'public');

            Bot::where('id', $request->id)->update([
                'name' => $request->name,
                'image' => $newfilename,
                'amount' => $request->amount,
                'buffer_percent' => $request->buffer_percent ?? 20.00,
                'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
                'min' => $request->min,
                'max' => $request->max,
                'day' => $request->daily,
                'total' => 0,
                'win' => $request->win,
                'loss' => $request->loss,
            ]);
        } else {
            Bot::where('id', $request->id)->update([
                'name' => $request->name,
                'image' => $site->image,
                'amount' => $request->amount,
                'buffer_percent' => $request->buffer_percent ?? 20.00,
                'per_withdrawal_percent' => $request->per_withdrawal_percent ?? 5.00,
                'min' => $request->min,
                'max' => $request->max,
                'day' => $request->daily,
                // 'total'=>$request->total,
                'win' => $request->win,
                'loss' => $request->loss,
            ]);
        }

        return redirect()->route('bots')->with('status', 'Bot updated');
    }

    public function userBots($id)  // user bot trades
    {$user = User::whereId($id)->first();

        return view('admin.user_bot', [
            'data' => DB::table('bot_generated_result')->whereUserId($user->id)->orderBy('id', 'desc')->get(),
            'user' => $user,
        ]);
    }
}
