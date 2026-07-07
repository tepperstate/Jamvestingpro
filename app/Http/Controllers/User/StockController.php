<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Site_setting;
use App\Models\Stock_Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class StockController extends Controller
{
    public function isMobileView()
    {
        return false;
    }

    public function stock_trade(Request $request)
    {
        $market = $request->get('market', 'nasdaq');
        $type = $request->get('type', 'trending');

        $query = Stock_Trade::query();

        // ── Global: Exclude crypto trading pairs ──
        // Crypto pairs typically contain crypto identifiers, numbers, underscores, etc.
        $cryptoPatterns = ['%USDT%', '%BTC%', '%ETH%', '%BNB%', '%BUSD%', '%USDC%', '%DAI%', '%TUSD%', '%FDUSD%', '%USD1%', '%\_%', '%XRP%'];
        foreach ($cryptoPatterns as $pattern) {
            $query->where('symbol', 'NOT LIKE', $pattern);
        }

        // Also exclude known standalone crypto symbols that may have leaked in
        $cryptoSymbols = [
            'BTC', 'ETH', 'BNB', 'SOL', 'ADA', 'DOGE', 'XRP', 'DOT', 'AVAX', 'MATIC',
            'LINK', 'UNI', 'ATOM', 'NEAR', 'FTM', 'ALGO', 'XLM', 'VET', 'ICP', 'FIL',
            'SAND', 'MANA', 'AXS', 'THETA', 'EOS', 'AAVE', 'MKR', 'COMP', 'SNX', 'YFI',
            'SUSHI', 'CRV', 'BAL', 'LRC', 'ENJ', 'BAT', 'ZRX', 'KNC', 'STORJ', 'OMG',
            'ARB', 'OP', 'APT', 'SUI', 'SEI', 'TIA', 'INJ', 'ONDO', 'NEO', 'SHIB',
            'PEPE', 'FLOKI', 'WIF', 'BONK', 'LUNC', 'TRX', 'LTC', 'BCH', 'ETC',
            'TAO', 'BIO', 'NIGHT', 'RTC',
        ];

        $cryptoFutures = array_map(function ($sym) {
            return $sym.'U';
        }, $cryptoSymbols);
        $query->whereNotIn('symbol', array_merge($cryptoSymbols, $cryptoFutures));

        // Market Filtering
        if ($market == 'nasdaq') {
            $query->where('symbol', 'NOT LIKE', '%.%')->whereRaw('LENGTH(symbol) >= 4');
        } elseif ($market == 'nyse') {
            $query->where('symbol', 'NOT LIKE', '%.%')->whereRaw('LENGTH(symbol) <= 3');
        } elseif ($market == 'dow' || $market == 's&p500') {
            $query->where('symbol', 'NOT LIKE', '%.%');
        }

        // Category Sorting/Filtering
        if ($type == 'top_gainers') {
            $query->orderBy('changes_percentage', 'desc');
        } elseif ($type == 'top_losers') {
            $query->orderBy('changes_percentage', 'asc');
        } elseif ($type == 'most_active') {
            $query->orderBy('volume', 'desc');
        } elseif ($type == 'trending') {
            $query->inRandomOrder();
        } elseif ($type == '52_week_high') {
            $query->orderBy('buy', 'desc');
        } elseif ($type == '52_week_low') {
            $query->orderBy('buy', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $stock_trade_data = $query->paginate(20)->appends(request()->query());

        $portfolio = DB::table('stock_balance')->where('stock_balance.user_id', auth()->guard('web')->user()->id)
            ->where('stock_balance.is_demo', auth()->user()->is_demo)
            ->join('stock_trades', 'stock_balance.stock_id', '=', 'stock_trades.id')
            ->select('stock_trades.image as image', 'stock_trades.symbol as symbol', 'stock_trades.name as name', 'stock_trades.buy as buy', 'stock_trades.buy as asset_base_price', 'stock_balance.amount as units', 'stock_balance.avg_price', 'stock_balance.total_cost', 'stock_balance.id as id', 'stock_balance.stock_id')
            ->get();

        $portfolio->transform(function ($item) {
            $stockConfig = DB::table('stocks')->where('symbols', $item->symbol)->first();
            $buffer = $stockConfig ? ($stockConfig->buffer_percent ?? 20.0) : 20.0;
            $multiplier = 1 + ($buffer / 100);

            $item->units = $item->units * $multiplier;
            $item->total_cost = $item->total_cost * $multiplier;

            return $item;
        });

        $total_equity = $portfolio->sum(function ($item) {
            $currentPrice = $item->buy > 0 ? $item->buy : $item->asset_base_price;

            return $item->units * $currentPrice;
        });

        $settings = Site_setting::first();
        $polygonApiKey = $settings->polygon_api_key ?? env('POLYGON_API_KEY', '');
        $alphaVantageKey = $settings->alphavantage_api_key ?? env('ALPHAVANTAGE_API_KEY', 'KG3EIIA0Q6MCGONL');

        return view('exchange.stocks', [
            'stocks' => $stock_trade_data,
            'portfolio' => $portfolio,
            'total_equity' => $total_equity,
            'current_market' => $market,
            'current_type' => $type,
            'polygon_api_key' => $polygonApiKey,
            'alphavantage_api_key' => $alphaVantageKey,
        ]);
    }

    public function buy_stock(Request $request)
    {
        try {
            // Frontend sends 'id' and 'amount' (requested SHARES/UNITS)
            $stockId = $request->id;
            $requestedShares = (float) $request->amount;

            if ($requestedShares <= 0) {
                return response()->json(['message' => 'Invalid amount. Please enter a positive number.'], 422);
            }

            $stock = Stock_Trade::where('id', $stockId)->first();
            if (! $stock) {
                return response()->json(['message' => 'Stock security not found'], 422);
            }

            // Use 'buy' price for current price, fallback to 'amount' if 'buy' is 0
            $currentPrice = (float) ($stock->buy > 0 ? $stock->buy : $stock->amount);

            if ($currentPrice <= 0) {
                return response()->json(['message' => 'Selected stock has no valid market price'], 422);
            }

            $totalInvestmentUSD = $requestedShares * $currentPrice;

            $isDemo = auth()->user()->is_demo;
            $balanceColumn = $isDemo ? 'demo' : 'amount';

            $balance = Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->first();
            if (! $balance || $totalInvestmentUSD > $balance->$balanceColumn) {
                return response()->json(['message' => 'Insufficient '.($isDemo ? 'demo ' : '').'funds. Need $'.number_format($totalInvestmentUSD, 2).' but only have $'.number_format($balance ? $balance->$balanceColumn : 0, 2)], 422);
            } else {
                $volume = $requestedShares;

                $oldPos = DB::table('stock_balance')
                    ->whereUserId(auth()->guard('web')->user()->id)
                    ->whereSymbol($stock->symbol)
                    ->where('is_demo', $isDemo)
                    ->first();

                $newAmount = ($oldPos ? (float) $oldPos->amount : 0) + $volume;
                $newTotalCost = ($oldPos ? (float) $oldPos->total_cost : 0) + $totalInvestmentUSD;
                $newAvgPrice = $newAmount > 0 ? $newTotalCost / $newAmount : 0;

                Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->decrement($balanceColumn, $totalInvestmentUSD);

                if (! $oldPos) {
                    DB::table('stock_balance')->insert([
                        'user_id' => auth()->guard('web')->user()->id,
                        'stock_id' => $stockId,
                        'name' => $stock->name,
                        'image' => $stock->image,
                        'symbol' => $stock->symbol,
                        'is_demo' => $isDemo,
                        'amount' => $newAmount,
                        'avg_price' => $newAvgPrice,
                        'total_cost' => $newTotalCost,
                    ]);
                } else {
                    DB::table('stock_balance')
                        ->where('id', $oldPos->id)
                        ->update([
                            'amount' => $newAmount,
                            'avg_price' => $newAvgPrice,
                            'total_cost' => $newTotalCost,
                        ]);
                }
            }
            $statusMsg = 'Order for '.number_format($requestedShares).' shares of '.$stock->symbol.' placed successfully. Total cost: $'.number_format($totalInvestmentUSD, 2);

            if (request()->ajax()) {
                // Return updated portfolio and balance for dynamic refresh
                $updatedPortfolio = DB::table('stock_balance')->where('stock_balance.user_id', auth()->guard('web')->user()->id)
                    ->where('stock_balance.is_demo', auth()->user()->is_demo)
                    ->join('stock_trades', 'stock_balance.stock_id', '=', 'stock_trades.id')
                    ->select('stock_trades.image as image', 'stock_trades.symbol as symbol', 'stock_trades.name as name', 'stock_trades.buy as buy', 'stock_trades.buy as asset_base_price', 'stock_balance.amount as units', 'stock_balance.avg_price', 'stock_balance.total_cost', 'stock_balance.id as id', 'stock_balance.stock_id')
                    ->get();
                $updatedBalance = Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->first();
                $balCol = auth()->user()->is_demo ? 'demo' : 'amount';

                return response()->json([
                    'status' => $statusMsg,
                    'portfolio' => $updatedPortfolio,
                    'balance' => $updatedBalance ? $updatedBalance->$balCol : 0,
                ]);
            }

            return redirect()->back()->with('success', $statusMsg);
        } catch (\Throwable $e) {
            \Log::error('Stock buy error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['message' => 'An internal error occurred. Please try again.'], 500);
        }
    }

    public function sell_stock(Request $request) // sell stock
    {try {
        // Frontend sends 'id' and 'amount' (requested SHARES/UNITS to sell)
        $stockId = $request->id;
        $requestedSharesToSell = (float) $request->amount;

        if ($requestedSharesToSell <= 0) {
            return response()->json(['message' => 'Invalid amount. Please enter a positive number.'], 422);
        }

        $stockDetails = Stock_Trade::where('id', $stockId)->first();
        if (! $stockDetails) {
            return response()->json(['message' => 'Stock security details missing'], 422);
        }

        $portfolioItem = DB::table('stock_balance')
            ->whereUserId(auth()->guard('web')->user()->id)
            ->where('stock_id', $stockId)
            ->where('is_demo', auth()->user()->is_demo)
            ->first();

        if (! $portfolioItem) {
            return response()->json(['message' => 'No active position for this security'], 422);
        }

        if ($requestedSharesToSell > $portfolioItem->amount) {
            return response()->json(['message' => 'Insufficient units in your portfolio. You have '.number_format($portfolioItem->amount).' units available.'], 422);
        }

        $currentPrice = (float) ($stockDetails->buy > 0 ? $stockDetails->buy : $stockDetails->amount);
        if ($currentPrice <= 0) {
            return response()->json(['message' => 'Invalid market price'], 422);
        }

        $totalProceedsUSD = $requestedSharesToSell * $currentPrice;
        $isDemo = $portfolioItem->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $newAmount = $portfolioItem->amount - $requestedSharesToSell;
        $costOfSoldShares = $requestedSharesToSell * $portfolioItem->avg_price;
        $newTotalCost = $portfolioItem->total_cost - $costOfSoldShares;

        Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->increment($balanceColumn, $totalProceedsUSD);

        DB::table('stock_balance')
            ->where('id', $portfolioItem->id)
            ->update([
                'amount' => max(0, $newAmount),
                'total_cost' => max(0, $newTotalCost),
                // avg_price stays the same
            ]);

        if ($newAmount <= 0) {
            DB::table('stock_balance')->where('id', $portfolioItem->id)->delete();
        }

        $statusMsg = 'Sold '.number_format($requestedSharesToSell).' shares of '.$stockDetails->symbol.' successfully. Total proceeds: $'.number_format($totalProceedsUSD, 2);

        if (request()->ajax()) {
            // Return updated portfolio and balance for dynamic refresh
            $updatedPortfolio = DB::table('stock_balance')->where('stock_balance.user_id', auth()->guard('web')->user()->id)
                ->where('stock_balance.is_demo', auth()->user()->is_demo)
                ->join('stock_trades', 'stock_balance.stock_id', '=', 'stock_trades.id')
                ->select('stock_trades.image as image', 'stock_trades.symbol as symbol', 'stock_trades.name as name', 'stock_trades.buy as buy', 'stock_trades.buy as asset_base_price', 'stock_balance.amount as units', 'stock_balance.avg_price', 'stock_balance.total_cost', 'stock_balance.id as id', 'stock_balance.stock_id')
                ->get();
            $updatedBalance = Balance::whereUserId(auth()->guard('web')->user()->id)->where('symbol', 'USD')->first();
            $balCol = auth()->user()->is_demo ? 'demo' : 'amount';

            return response()->json([
                'status' => $statusMsg,
                'portfolio' => $updatedPortfolio,
                'balance' => $updatedBalance ? $updatedBalance->$balCol : 0,
            ]);
        }

        return redirect()->back()->with('success', $statusMsg);
    } catch (\Throwable $e) {
        \Log::error('Stock sell error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

        return response()->json(['message' => 'An internal error occurred. Please try again.'], 500);
    }
    }

    public function fetchStockPortfolio($id)
    {
        if (request()->ajax()) {
            $portfolio = DB::table('stock_balance')
                ->whereUserId(auth()->guard('web')->user()->id)
                ->where('stock_id', $id)
                ->where('is_demo', auth()->user()->is_demo)
                ->first();

            return response()->json(['data' => $portfolio]);
        }

        // Dashboard logic for the page view
        $balance = Balance::where('user_id', auth()->user()->id)->where('symbol', 'usd')->first();
        $usd = $balance ? $balance->amount : 0;

        $portfolio = DB::table('stock_balance')->where('stock_balance.user_id', auth()->guard('web')->user()->id)
            ->where('stock_balance.is_demo', auth()->user()->is_demo)
            ->join('stock_trades', 'stock_balance.stock_id', '=', 'stock_trades.id')
            ->select('stock_trades.image as image', 'stock_trades.symbol as symbol', 'stock_trades.name as name', 'stock_trades.buy as buy', 'stock_trades.buy as asset_base_price', 'stock_balance.amount as units', 'stock_balance.avg_price', 'stock_balance.total_cost', 'stock_balance.id as id', 'stock_balance.stock_id')
            ->get();

        $portfolio->transform(function ($item) {
            $stockConfig = DB::table('stocks')->where('symbols', $item->symbol)->first();
            $buffer = $stockConfig ? ($stockConfig->buffer_percent ?? 20.0) : 20.0;
            $multiplier = 1 + ($buffer / 100);

            $item->units = $item->units * $multiplier;
            $item->total_cost = $item->total_cost * $multiplier;

            return $item;
        });

        $total_equity = $portfolio->sum(function ($item) {
            $currentPrice = $item->buy > 0 ? $item->buy : $item->asset_base_price;

            return $item->units * $currentPrice;
        });

        $trade = Order::whereUserId(auth()->guard('web')->user()->id)->where('is_demo', auth()->user()->is_demo)->orderBy('id', 'desc')->limit(10)->get();

        $viewName = $this->isMobileView() ? 'mobile.pages.portfolio' : 'exchange.stocks';

        return view($viewName, [
            'portfolio' => $portfolio,
            'total_equity' => $total_equity,
            'usd' => $usd,
            'trade' => $trade,
            'stocks' => Stock_Trade::orderBy('id', 'desc')->paginate(20), // For desktop view if needed
        ]);
    }

    public function single_stock($id)
    {

        $stock_Trade = Stock_Trade::find($id);
        $stock_balance = DB::table('stock_balance')
            ->whereUserId(auth()->guard('web')->user()->id)
            ->whereSymbol($stock_Trade->symbol)
            ->where('is_demo', auth()->user()->is_demo)
            ->exists();

        $stock_query = null;
        if ($stock_balance) {
            $stock_query = DB::table('stock_balance')
                ->whereUserId(auth()->guard('web')->user()->id)
                ->whereSymbol($stock_Trade->symbol)
                ->where('is_demo', auth()->user()->is_demo)
                ->first();
        }

        if ($stock_Trade->is_vip) {
            $viewName = $this->isMobileView() ? 'mobile.exchange.vip_stock_manage' : 'exchange.vip_stock_manage';

            return view($viewName, [
                'data' => $stock_Trade,
                'price' => $stock_Trade->buy,
                'stock_query' => $stock_query,
                'symbol' => $stock_Trade->symbol,
            ]);
        }

        $viewName = $this->isMobileView() ? 'mobile.exchange.stock_d' : 'exchange.stock_d';

        try {
            // Add a timeout to prevent page hangs
            $response = Http::timeout(5)->get('https://www.alphavantage.co/query', [
                'function' => 'GLOBAL_QUOTE',
                'symbol' => $stock_Trade->symbol,
                'apikey' => 'AS54YFT2ODC6BYBS',
            ]);

            $data = $response->successful() ? $response->json() : null;

            // Extract price with a safe fallback to the database 'buy' price
            $price = $data['Global Quote']['04. low'] ?? $stock_Trade->buy;

            return view($viewName, [
                'data' => $stock_Trade,
                'price' => $price,
                'stock_query' => $stock_query ?? null,
                'symbol' => $stock_Trade->symbol,
            ]);

        } catch (\Throwable $th) {
            // Fallback to database data on any failure
            return view($viewName, [
                'data' => $stock_Trade,
                'price' => $stock_Trade->buy,
                'stock_query' => $stock_query ?? null,
                'symbol' => $stock_Trade->symbol,
            ]);
        }
    }
}
