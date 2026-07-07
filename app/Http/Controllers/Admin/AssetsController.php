<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock_Trade;
use App\Services\BinancePriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AssetsController extends Controller
{
    public function index()
    {
        $data = DB::table('assets')->orderBy('id', 'DESC')->get();
        $cat = DB::table('exchanges')->orderBy('id', 'DESC')->get();

        return view('admin.add', [
            'data' => $data,
            'cat' => $cat,
        ]);
    }

    public function assetDetails()
    {
        $data = DB::table('assets')->orderBy('id', 'DESC')->get();
        $cat = DB::table('exchanges')->orderBy('id', 'DESC')->get();

        return view('admin.add_details', [
            'data' => $data,
            'cat' => $cat,
        ]);
    }

    public function viewSingleAsset($id, $eid)
    {
        $data = DB::table('assets')->where('id', $id)->first();

        return view('admin.edit_deal', [
            'data' => $data,
        ]);
    }

    public function viewSingleCat($id)
    {
        $data = DB::table('exchanges')->where('id', $id)->first();

        return view('admin.edit_cat', [
            'data' => $data,
        ]);
    }

    public function editCat(Request $request)
    {

        $id = $request->id;

        $data = DB::table('exchanges')->where('id', $id)->update([
            'name' => $request->name,

        ]);

        return back()->with('status', 'category updated');

    }

    public function delete_cat($id)
    {
        DB::table('exchanges')->where('id', $id)->delete();

        return back()->with('status', 'category deleted');

    }

    public function editAssets(Request $request)
    {

        $id = $request->id;
        $data = DB::table('assets')->where('id', $id)->first();
        if ($request->hasFile('image1')) {
            $request->validate([
                'image1' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        // Real MIME from file content
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];

                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }

                        // Check file name for forbidden extensions and double extensions
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }

                        // Extra security: Check image signature via getimagesize (detect fake images)
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);
            $filename = $request->file('image1');
            $image1 = 'image1'.time().'.'.$filename->getClientOriginalExtension();
            $request->file('image1')->storeAs('image', $image1, 'public');
        } else {
            $image1 = $data->image1;
        }

        if ($request->hasFile('image2')) {
            $request->validate([
                'image2' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        // Real MIME from file content
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];

                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }

                        // Check file name for forbidden extensions and double extensions
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }

                        // Extra security: Check image signature via getimagesize (detect fake images)
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);
            $filename = $request->file('image2');
            $image2 = 'image2'.time().'.'.$filename->getClientOriginalExtension();
            $request->file('image2')->storeAs('image', $image2, 'public');
        } else {
            $image2 = $data->image2;
        }

        $data = DB::table('assets')->where('id', $id)->update([
            'symbols' => $request->name,
            'profits' => strtolower($request->win),
            'image1' => $image1,
            'image2' => $image2,
            'percentage' => $request->per,
            'loss_percentage' => $request->loss,
            'mirror_symbol' => $request->mirror_symbol,
        ]);

        return back()->with('status', 'assets updated');

    }

    public function deleteAssets($id, $eid)
    {
        $id = $id;
        DB::table('assets')->where('id', $id)->delete();

        return back()->with('status', 'assets deleted');
    }

    public function mass_import(Request $request)
    {
        $symbols = explode(',', $request->symbols);
        $exchange_id = $request->cat;
        $percentage = $request->percentage ?? 80;
        $loss_percentage = $request->loss_percentage ?? 80;

        foreach ($symbols as $symbol) {
            $symbol = trim($symbol);
            if (! $symbol) {
                continue;
            }

            DB::table('assets')->updateOrInsert(
                ['symbols' => $symbol, 'exchanges_id' => $exchange_id],
                [
                    'percentage' => $percentage,
                    'loss_percentage' => $loss_percentage,
                    'profits' => 'win',
                    'type' => 'forex', // Default
                    'created_at' => now(),
                ]
            );
        }

        return back()->with('status', 'Mass asset synchronization complete.');
    }

    public function autonomous_discovery(Request $request)
    {
        $exchange_id = $request->cat;
        $percentage = $request->percentage ?? 85;
        $loss_percentage = $request->loss_percentage ?? 85;

        // Major ticker clusters based on exchange
        $forex = ['EURUSD', 'GBPUSD', 'USDJPY', 'USDCHF', 'AUDUSD', 'USDCAD', 'NZDUSD', 'EURGBP', 'EURJPY', 'GBPJPY'];
        $crypto = ['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'SOLUSDT', 'ADAUSDT', 'XRPUSDT', 'DOTUSDT', 'DOGEUSDT', 'AVAXUSDT', 'LINKUSDT'];
        $stocks = ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'TSLA', 'META', 'NVDA', 'NFLX', 'BRK.B', 'JPM', 'UNH', 'V', 'XOM', 'MA', 'JNJ', 'PG', 'HD', 'COST', 'WMT', 'ADBE', 'CRM', 'PEP', 'KO'];

        $symbols = [];
        if ($exchange_id == 1) {
            $symbols = $forex;
        } elseif ($exchange_id == 2) {
            $symbols = $crypto;
        } elseif ($exchange_id == 3) {
            try {
                $resp = Http::timeout(5)->get('https://dumbstockapi.com/stock?exchanges=NASDAQ&format=json');
                $stocks_data = $resp->successful() ? $resp->json() : [];
                $symbols = collect($stocks_data)->pluck('ticker')->shuffle()->take(15)->toArray();
            } catch (\Exception $e) {
                $symbols = [];
            }

            if (empty($symbols)) {
                $symbols = ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'TSLA', 'META', 'NVDA', 'NFLX', 'BRK.B', 'JPM'];
                shuffle($symbols);
            }
        } else {
            // Fallback or other categories
            $symbols = array_merge($forex, $crypto, $stocks);
        }

        $count = 0;
        foreach ($symbols as $symbol) {
            $exists = DB::table('assets')->where('symbols', $symbol)->where('exchanges_id', $exchange_id)->exists();
            if (! $exists) {
                DB::table('assets')->insert([
                    'exchanges_id' => $exchange_id,
                    'symbols' => $symbol,
                    'percentage' => $percentage,
                    'loss_percentage' => $loss_percentage,
                    'profits' => 'win',
                    'type' => ($exchange_id == 1 ? 'forex' : ($exchange_id == 2 ? 'crypto' : 'stock')),
                    'created_at' => now(),
                ]);
                $count++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Discovery complete. Identified and synchronized $count new trading hubs.",
            'count' => $count,
        ]);
    }

    public function syncFromBinance(Request $request)
    {
        $info = BinancePriceService::getSpotExchangeInfo();
        if (! $info || ! isset($info['symbols'])) {
            return response()->json(['status' => false, 'message' => 'Failed to fetch Spot exchange info from Binance.']);
        }

        $symbols = $info['symbols'];
        $count = 0;

        $insertData = [];
        $existingSymbols = Stock_Trade::pluck('symbol')->toArray();
        $existingSet = array_flip($existingSymbols);

        foreach ($symbols as $sym) {
            if ($sym['status'] === 'TRADING') {
                $symbolName = $sym['symbol'];
                $baseAsset = $sym['baseAsset'];

                if (! isset($existingSet[$symbolName])) {
                    $insertData[] = [
                        'name' => $baseAsset,
                        'symbol' => $symbolName,
                        'is_vip' => false,
                        'image' => strtolower($baseAsset).'.png',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $count++;
                    $existingSet[$symbolName] = true;
                }

                // Chunk insert to avoid memory issues
                if (count($insertData) >= 1000) {
                    Stock_Trade::insert($insertData);
                    $insertData = [];
                }
            }
        }

        if (count($insertData) > 0) {
            Stock_Trade::insert($insertData);
        }

        return response()->json([
            'status' => true,
            'message' => "Successfully synchronized {$count} new spot trading pairs from Binance.",
            'count' => $count,
        ]);
    }

    public function addAssets(Request $request)
    {

        if ($request->hasFile('image1')) {
            $request->validate([
                'image1' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        // Real MIME from file content
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];

                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }

                        // Check file name for forbidden extensions and double extensions
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }

                        // Extra security: Check image signature via getimagesize (detect fake images)
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);
        }

        $filename = $request->file('image1');
        $image1 = 'image1'.time().'.'.$filename->getClientOriginalExtension();

        $request->file('image1')->storeAs('image', $image1, 'public');

        if ($request->hasFile('image2')) {
            $request->validate([
                'image2' => [
                    'required',
                    'file',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        // Real MIME from file content
                        $realMime = $value->getMimeType();
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];

                        if (! in_array($realMime, $allowedMimes)) {
                            $fail('The uploaded file is not a valid image (MIME mismatch).');
                        }

                        // Check file name for forbidden extensions and double extensions
                        $originalName = $value->getClientOriginalName();
                        if (preg_match('/\.(php|phar|phtml|php\d+|asp|jsp|exe|sh|bat|cmd|js)$/i', $originalName)) {
                            $fail('Invalid file extension detected.');
                        }

                        // Extra security: Check image signature via getimagesize (detect fake images)
                        if (! @getimagesize($value->getRealPath())) {
                            $fail('The uploaded file is not a real image (failed signature check).');
                        }
                    },
                ],
            ]);
            $filename = $request->file('image2');
            $image2 = 'image2'.time().'.'.$filename->getClientOriginalExtension();
            $request->file('image2')->storeAs('image', $image2, 'public');
        } else {
            $image2 = null;
        }
        DB::table('assets')->insert([
            'exchanges_id' => $request->cat,
            'symbols' => $request->symbol,
            'image1' => $image1,
            'image2' => $image2,
            'profits' => strtolower($request->profit),
            'type' => $request->type,
            'percentage' => $request->percentage,
            'loss_percentage' => $request->loss_percentage,
            'mirror_symbol' => $request->mirror_symbol,
        ]);

        return back()->with('status', 'new asset added');
    }

    public function addCat(Request $request)
    {

        DB::table('exchanges')->insert([
            'name' => $request->cat,
        ]);

        return back()->with('status', 'new category added');
    }

    // public function assetDetails(){
    //    $data =  DB::table('forexs')->orderBy('id', 'DESC')->get()->toArray();
    //    $data2 =  DB::table('cryptos')->orderBy('id', 'DESC')->get()->toArray();
    //    $data3 =  DB::table('stocks')->orderBy('id', 'DESC')->get()->toArray();

    //    $d = array_merge($data,$data2,$data3);

    //    return view('admin.add_details',[
    //       'data'=>$d,
    //    ]);
    // }

    // public function viewSingleAsset($id,$eid){
    //   if($eid == 1){
    //      $type = 'Forex';
    //      $data = DB::table('forexs')->where("id",$id)->first();
    //   }else if($eid == 2){
    //      $type = 'Crypto';
    //      $data = DB::table('cryptos')->where("id",$id)->first();
    //   }else{
    //      $type = "Stock";
    //      $data = DB::table('stocks')->where("id",$id)->first();
    //   }

    //   return view('admin.edit_deal',[
    //       'data'=>$data,
    //       'type'=>$type,
    //    ]);
    // }
    // public function editAssets(Request $request){

    //    $id = $request->id;
    //    $exchange_id  = $request->exc_id;

    //    if($exchange_id == 1){
    //       $data = DB::table('forexs')->where("id",$id)->update([
    //          'symbols'=>$request->name,
    //          'profits'=> strtolower($request->win),
    //          'percentage'=>$request->per,
    //       ]);
    //    }else if($exchange_id == 2){
    //       $data = DB::table('cryptos')->where("id",$id)->update([
    //          'symbols'=>$request->name,
    //          'profits'=> strtolower($request->win),
    //          'percentage'=>$request->per,
    //       ]);
    //    }else{
    //       $data = DB::table('stocks')->where("id",$id)->update([
    //          'symbols'=>$request->name,
    //          'profits'=> strtolower($request->win),
    //          'percentage'=>$request->per,
    //       ]);
    //    }

    //    return back()->with('status','assets updated');

    // }

    // public function deleteAssets($id,$eid){
    //    $id = $id;
    //    $exchange_id  = $eid;

    //    if($exchange_id == 1){
    //       $data = DB::table('forexs')->where("id",$id)->delete();
    //    }else if($exchange_id == 2){
    //       $data = DB::table('cryptos')->where("id",$id)->delete();
    //    }else{
    //       $data = DB::table('stocks')->where("id",$id)->delete();
    //    }

    //    return back()->with('status','assets deleted');
    // }

    // public function addAssets(Request $request){
    //    if($request->type == "forex"){

    //          DB::table('forexs')->insert([
    //             'exchanges_id'=>1,
    //             'symbols'=>$request->symbol,
    //             'profits'=>strtolower($request->profit),
    //             'percentage'=>$request->percentage,

    //          ]);

    //          return back()->with('status','new asset added');
    //    }
    //    if($request->type == "crypto"){

    //       DB::table('cryptos')->insert([
    //          'exchanges_id'=>2,
    //          'symbols'=>$request->symbol,
    //          'profits'=>strtolower($request->profit),
    //          'percentage'=>$request->percentage,

    //       ]);

    //       return back()->with('status','new asset added');
    //    }

    //    if($request->type == "stocks"){

    //       DB::table('stocks')->insert([
    //          'exchanges_id'=>3,
    //          'symbols'=>$request->symbol,
    //          'profits'=>strtolower($request->profit),
    //          'percentage'=>$request->percentage,
    //       ]);

    //       return back()->with('status','new asset added');
    //    }

    // }

    public function vidoes()
    {
        $data = DB::table('vidoes')->get();

        return view('admin.vidoes', [
            'data' => $data,
        ]);
    }

    public function upload_videos(Request $request)
    {

        $this->validate($request, [
            'title' => 'required',
            'link' => 'required',
        ]);

        if ($request->id) {
            DB::table('vidoes')->where('id', $request->id)->update([
                'title' => $request->title,
                'vidoes' => $request->link,
            ]);

            return back()->with('status', 'video edited');
        } else {
            DB::table('vidoes')->insert([
                'title' => $request->title,
                'vidoes' => $request->link,
            ]);

            return back()->with('status', 'new video added');
        }

    }

    public function delete_vidoes($id)
    {
        DB::table('vidoes')->where('id', $id)->delete();

        return back()->with('status', 'vidoes deleted');
    }

    public function edit_vidoes($id)
    {
        $data = DB::table('vidoes')->where('id', $id)->first();

        return view('admin.edit_videos', [
            'data' => $data,
        ]);

    }

    public function sync_logos(Request $request)
    {
        $symbol = $request->symbol;
        $params = [];
        if ($symbol) {
            $params['--symbol'] = $symbol;
            $msg = "Identity synchronization initiated for $symbol.";
        } else {
            $msg = 'Global asset logo synchronization sequence commenced in the background.';
        }

        // Trigger the artisan command
        Artisan::call('assets:fetch-logos', $params);

        return response()->json([
            'status' => true,
            'message' => $msg,
        ]);
    }

    public function massDelete(Request $request)
    {
        $ids = $request->ids;
        if (! is_array($ids) || empty($ids)) {
            return response()->json(['status' => false, 'message' => 'No assets selected.']);
        }

        try {
            DB::table('assets')->whereIn('id', $ids)->delete();

            return response()->json(['status' => true, 'message' => count($ids).' assets deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error deleting assets: '.$e->getMessage()]);
        }
    }

    public function massEditProfitLoss(Request $request)
    {
        $ids = $request->ids;
        if (! is_array($ids) || empty($ids)) {
            return response()->json(['status' => false, 'message' => 'No assets selected.']);
        }

        $updateData = [];
        if ($request->filled('profit_percentage')) {
            $updateData['percentage'] = $request->profit_percentage;
        }
        if ($request->filled('loss_percentage')) {
            $updateData['loss_percentage'] = $request->loss_percentage;
        }

        if (empty($updateData)) {
            return response()->json(['status' => false, 'message' => 'No percentages provided.']);
        }

        try {
            DB::table('assets')->whereIn('id', $ids)->update($updateData);

            return response()->json(['status' => true, 'message' => count($ids).' assets updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error updating assets: '.$e->getMessage()]);
        }
    }
}
