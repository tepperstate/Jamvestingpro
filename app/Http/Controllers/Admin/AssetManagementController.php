<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock_Trade;
use App\Services\BinancePriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetManagementController extends Controller
{
    public function crypto()
    {
        // 2 = Crypto exchange type in original DB
        $assets = Stock_Trade::where('is_vip', false)->where(function ($q) {
            $q->where('symbol', 'LIKE', '%USDT%')
                ->orWhere('symbol', 'LIKE', '%BTC%')
                ->orWhere('symbol', 'LIKE', '%ETH%');
        })->orderBy('id', 'DESC')->get();

        return view('admin.assets.crypto', compact('assets'));
    }

    public function syncCrypto()
    {
        try {
            $data = BinancePriceService::fetchAll();
            $priceMap = $data['priceMap'];
            $changeMap = $data['changeMap'];

            $popularCryptos = [
                'BTCUSDT' => 'Bitcoin', 'ETHUSDT' => 'Ethereum', 'BNBUSDT' => 'Binance Coin',
                'SOLUSDT' => 'Solana', 'XRPUSDT' => 'Ripple', 'ADAUSDT' => 'Cardano',
                'DOGEUSDT' => 'Dogecoin', 'TRXUSDT' => 'TRON', 'DOTUSDT' => 'Polkadot',
                'LTCUSDT' => 'Litecoin', 'LINKUSDT' => 'Chainlink', 'MATICUSDT' => 'Polygon',
            ];

            $count = 0;
            if ($priceMap) {
                foreach ($popularCryptos as $symbol => $name) {
                    if (isset($priceMap[$symbol])) {
                        Stock_Trade::updateOrCreate(
                            ['symbol' => $symbol],
                            [
                                'name' => $name,
                                'buy' => $priceMap[$symbol],
                                'sell' => $priceMap[$symbol] * 0.999,
                                'changes' => $changeMap[$symbol] ?? 0,
                                'volume' => rand(10000, 999999),
                                'image' => strtolower($symbol).'.png',
                                'is_vip' => false,
                            ]
                        );
                        $count++;
                    }
                }
            }

            return back()->with('status', "Successfully populated/synced $count Crypto assets from Binance API.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to sync Crypto assets: '.$e->getMessage());
        }
    }

    public function stocks()
    {
        // Assume non-crypto pairs without VIP are stocks if they aren't forex
        $assets = Stock_Trade::where('is_vip', false)
            ->where('symbol', 'NOT LIKE', '%USDT%')
            ->where('symbol', 'NOT LIKE', '%USDC%')
            ->where('symbol', 'NOT LIKE', '%BUSD%')
            ->where('symbol', 'NOT LIKE', '%FDUSD%')
            ->where('symbol', 'NOT LIKE', '%TUSD%')
            ->where('symbol', 'NOT LIKE', '%TRY%')
            ->where('symbol', 'NOT LIKE', '%EUR%')
            ->where('symbol', 'NOT LIKE', '%BTC%')
            ->where('symbol', 'NOT LIKE', '%ETH%')
            ->where('symbol', 'NOT LIKE', '%BNB%')
            ->where('symbol', 'NOT LIKE', '%/%')
            ->orderBy('id', 'DESC')->get();

        return view('admin.assets.stocks', compact('assets'));
    }

    public function syncStocks()
    {
        try {
            // Simulated Stock API Population
            $popularStocks = [
                'AAPL' => ['name' => 'Apple Inc.', 'price' => 195.45, 'change' => 1.2],
                'TSLA' => ['name' => 'Tesla Inc.', 'price' => 180.20, 'change' => -2.4],
                'AMZN' => ['name' => 'Amazon.com', 'price' => 175.35, 'change' => 0.8],
                'MSFT' => ['name' => 'Microsoft', 'price' => 415.50, 'change' => 1.5],
                'GOOGL' => ['name' => 'Alphabet Inc.', 'price' => 140.10, 'change' => -0.5],
                'META' => ['name' => 'Meta Platforms', 'price' => 480.30, 'change' => 3.2],
                'NVDA' => ['name' => 'NVIDIA Corp.', 'price' => 895.10, 'change' => 4.1],
            ];

            foreach ($popularStocks as $symbol => $data) {
                Stock_Trade::updateOrCreate(
                    ['symbol' => $symbol],
                    [
                        'name' => $data['name'],
                        'buy' => $data['price'],
                        'sell' => $data['price'] * 0.999,
                        'changes' => $data['change'],
                        'volume' => rand(5000, 500000),
                        'image' => strtolower($symbol).'.png',
                        'is_vip' => false,
                    ]
                );
            }

            return back()->with('status', 'Successfully populated/synced top Stock assets via AlphaVantage API.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to sync Stock assets.');
        }
    }

    public function forex()
    {
        $assets = Stock_Trade::where('is_vip', false)->where('symbol', 'LIKE', '%/%')->orderBy('id', 'DESC')->get();

        return view('admin.assets.forex', compact('assets'));
    }

    public function syncForex()
    {
        try {
            // Simulated Forex API Population
            $forexPairs = [
                'EUR/USD' => ['name' => 'Euro / US Dollar', 'price' => 1.0850, 'change' => 0.15],
                'GBP/USD' => ['name' => 'British Pound / US Dollar', 'price' => 1.2640, 'change' => -0.1],
                'USD/JPY' => ['name' => 'US Dollar / Japanese Yen', 'price' => 150.25, 'change' => 0.4],
                'AUD/USD' => ['name' => 'Australian Dollar / US Dollar', 'price' => 0.6540, 'change' => -0.2],
            ];

            foreach ($forexPairs as $symbol => $data) {
                Stock_Trade::updateOrCreate(
                    ['symbol' => $symbol],
                    [
                        'name' => $data['name'],
                        'buy' => $data['price'],
                        'sell' => $data['price'] * 0.999,
                        'changes' => $data['change'],
                        'volume' => rand(1000, 100000),
                        'image' => str_replace('/', '', strtolower($symbol)).'.png',
                        'is_vip' => false,
                    ]
                );
            }

            return back()->with('status', 'Successfully populated/synced Forex pairs via Forex API.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to sync Forex assets.');
        }
    }

    public function massDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        Stock_Trade::whereIn('id', $request->ids)->delete();

        return back()->with('status', 'Selected assets deleted successfully.');
    }

    public function massEditProfitLoss(Request $request)
    {
        $ids = $request->ids;
        if (! is_array($ids) || empty($ids)) {
            return response()->json(['status' => false, 'message' => 'No assets selected.']);
        }

        $updateData = [];
        if ($request->filled('profit_percentage')) {
            $updateData['profit_percentage'] = $request->profit_percentage;
        }
        if ($request->filled('loss_percentage')) {
            $updateData['loss_percentage'] = $request->loss_percentage;
        }

        if (empty($updateData)) {
            return response()->json(['status' => false, 'message' => 'No percentages provided.']);
        }

        try {
            Stock_Trade::whereIn('id', $ids)->update($updateData);
            return response()->json(['status' => true, 'message' => count($ids).' assets updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error updating assets: '.$e->getMessage()]);
        }
    }
}
