<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    //             'exchanges_id' => $request->market,
    //             'symbols' => $asset->symbols,
    //             'profits' => $asset->profits,
    //             'buy' => $this->getRandomTradeType(),
    //             'amount' => $request->amount,
    //             'time' => $request->time,
    //             'created_at'=>Carbon::now()

    //         ];

    //     // Insert all data into the generate_signal table
    //     if (!empty($data)) {
    //         DB::table('generate_signal')->insert($data);
    //     }

    //     return back()->with('status' , 'Signals generated successfully');
    // }

    //  public function getRandomAssets(Request $request){
    //     // Retrieve the number of wins and losses from the request
    //     $winCount = $request->win; // e.g., 2
    //     $lossCount = $request->loss; // e.g., 4

    //     // Get random win and loss assets
    //     $winAssets = Asset::where('exchanges_id', $request->market)
    //                     ->where('profits', 'win')
    //                     ->inRandomOrder()
    //                     ->take($winCount)
    //                     ->get();

    //     $lossAssets = Asset::where('exchanges_id', $request->market)
    //                     ->where('profits', 'loss')
    //                     ->inRandomOrder()
    //                     ->take($lossCount)
    //                     ->get();

    //     $data = [];
    //     // Prepare win assets for insertion
    //     foreach ($winAssets as $asset) {
    //         // Check if this data already exists
    //         $exists = DB::table('generate_signal')->where([
    //             ['symbols', '=', $asset->symbols],
    //         ])->exists();

    //         // Insert only if it doesn't exist
    //         if (!$exists) {
}
