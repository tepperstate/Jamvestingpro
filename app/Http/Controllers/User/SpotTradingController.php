<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Noti;
use App\Models\Site_setting;
use App\Models\SpotOrder;
use App\Models\Stock_Trade;
use App\Models\UserWallet;
use App\Services\BinancePriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpotTradingController extends Controller
{
    public function index()
    {
        // Dynamic asset fetch from database populated by Binance Sync command
        $assets = Stock_Trade::where('is_vip', false)
            ->where(function ($q) {
                $patterns = ['%USDT%', '%BTC%', '%ETH%', '%BNB%', '%BUSD%', '%USDC%', '%DAI%', '%TUSD%', '%FDUSD%', '%USD1%', '%\_%', '%XRP%'];
                foreach ($patterns as $pattern) {
                    $q->orWhere('symbol', 'LIKE', $pattern);
                }
            })
            ->orderBy('id', 'asc')
            ->get();

        // Dynamically fetch live prices and 24h changes via centralized service (IN-MEMORY ONLY)
        try {
            $data = BinancePriceService::fetchAll();
            $priceMap = $data['priceMap'];
            $changeMap = $data['changeMap'];

            if ($priceMap) {
                foreach ($assets as $asset) {
                    if (isset($priceMap[$asset->symbol])) {
                        $livePrice = $priceMap[$asset->symbol];
                        $asset->buy = $livePrice;
                        $asset->sell = $livePrice * 0.999;
                        if ($changeMap && isset($changeMap[$asset->symbol])) {
                            $asset->changes = $changeMap[$asset->symbol];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch live prices: '.$e->getMessage());
        }
        $orders = SpotOrder::where('user_id', auth()->id())
            ->where('is_demo', auth()->user()->is_demo)
            ->latest()
            ->paginate(15);

        $user = auth()->user();
        $usdBalance = Balance::where('user_id', $user->id)->where('symbol', 'usd')->first();
        // Since we are moving away from stock_balance to UserWallet for crypto
        $holdings = UserWallet::where('user_id', $user->id)
            ->get()
            ->keyBy('coin_symbol');

        $tickerData = BinancePriceService::get24hrTickerData() ?? [];

        $viewName = $this->isMobileView() ? 'mobile.exchange.spot_trading' : 'exchange.spot_trading';

        return view($viewName, compact('assets', 'orders', 'usdBalance', 'holdings', 'tickerData'));
    }

    public function placeOrder(Request $request)
    {
        $user = auth()->user();
        $isDemo = $user->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $stock = Stock_Trade::findOrFail($request->id);
        $amount = (float) $request->amount;
        $type = $request->type; // buy or sell
        $currentPrice = (float) ($stock->buy > 0 ? $stock->buy : 0);
        $totalUsd = $amount * $currentPrice;

        // Margin & order type parameters
        $marginMode = in_array($request->margin_mode, ['spot', 'cross', 'isolated']) ? $request->margin_mode : 'spot';
        $leverage = in_array((int) $request->leverage, [1, 3, 10]) ? (int) $request->leverage : 1;
        $orderType = in_array($request->order_type, ['limit', 'market', 'stop-limit', 'stop_loss', 'take_profit', 'trailing_stop', 'oco']) ? $request->order_type : 'limit';

        $triggerPrice = $request->stop_price ?? $request->trigger_price ?? null;
        $limitPrice = $request->limit_price ?? null;
        $trailingDelta = $request->trailing_delta ?? null;

        // For leveraged trading, the effective buying power is multiplied by leverage
        // but the actual deducted amount is the margin (totalUsd / leverage for margin trades)
        $marginRequired = $leverage > 1 ? ($totalUsd / $leverage) : $totalUsd;

        if ($type === 'buy') {
            $balance = Balance::where('user_id', $user->id)->where('symbol', 'usd')->first();
            if (! $balance || $marginRequired > $balance->$balanceColumn) {
                $label = $leverage > 1 ? 'margin of $'.number_format($marginRequired, 2).' ('.$leverage.'x leverage)' : '$'.number_format($totalUsd, 2);

                return response()->json(['error' => 'Insufficient funds. Need '.$label], 400);
            }

            DB::transaction(function () use ($user, $stock, $amount, $currentPrice, $totalUsd, $isDemo, $balanceColumn, $marginRequired, $marginMode, $leverage, $orderType, $triggerPrice, $limitPrice, $trailingDelta) {
                $settings = Site_setting::first();

                if ($settings && $settings->spot_auto_approve) {
                    $profit = $marginRequired * ($settings->spot_auto_win_percent / 100);
                    $returnAmount = $marginRequired + $profit;

                    // Deduct initial investment then return investment + profit
                    Balance::where('user_id', $user->id)->where('symbol', 'usd')->decrement($balanceColumn, $marginRequired);
                    Balance::where('user_id', $user->id)->where('symbol', 'usd')->increment($balanceColumn, $returnAmount);

                    $order = SpotOrder::create([
                        'user_id' => $user->id,
                        'symbol' => $stock->symbol,
                        'type' => 'buy',
                        'amount' => $amount,
                        'price' => $currentPrice,
                        'total_usd' => $totalUsd,
                        'status' => 'approved',
                        'is_demo' => $isDemo,
                        'margin_mode' => $marginMode,
                        'leverage' => $leverage,
                        'order_type' => $orderType,
                        'trigger_price' => $triggerPrice,
                        'stop_price' => $triggerPrice,
                        'limit_price' => $limitPrice,
                        'trailing_delta' => $trailingDelta,
                    ]);

                    Noti::create([
                        'user_id' => $user->id,
                        'message' => 'Spot Auto-Approved: Your '.$stock->symbol.' trade closed with $'.number_format($profit, 2).' profit.',
                        'status' => 'unread',
                    ]);
                } else {
                    Balance::where('user_id', $user->id)->where('symbol', 'usd')->decrement($balanceColumn, $marginRequired);

                    SpotOrder::create([
                        'user_id' => $user->id,
                        'symbol' => $stock->symbol,
                        'type' => 'buy',
                        'amount' => $amount,
                        'price' => $currentPrice,
                        'total_usd' => $totalUsd,
                        'status' => 'pending',
                        'is_demo' => $isDemo,
                        'margin_mode' => $marginMode,
                        'leverage' => $leverage,
                        'order_type' => $orderType,
                        'trigger_price' => $triggerPrice,
                        'stop_price' => $triggerPrice,
                        'limit_price' => $limitPrice,
                        'trailing_delta' => $trailingDelta,
                    ]);
                }
            });
        } else {
            // Sell — check portfolio holdings in UserWallet
            // Extract base asset, e.g., BTC from BTCUSDT
            $baseAsset = str_replace(['USDT', 'USD'], '', strtoupper($stock->symbol));
            $holding = UserWallet::where('user_id', $user->id)
                ->where('coin_symbol', $baseAsset)
                ->first();

            if (! $holding || $holding->balance < $amount) {
                return response()->json(['error' => 'Insufficient holdings. You have '.($holding ? $holding->balance : 0).' '.$baseAsset.'.'], 400);
            }

            SpotOrder::create([
                'user_id' => $user->id,
                'symbol' => $stock->symbol,
                'type' => 'sell',
                'amount' => $amount,
                'price' => $currentPrice,
                'total_usd' => $totalUsd,
                'status' => 'pending',
                'is_demo' => $isDemo,
                'margin_mode' => $marginMode,
                'leverage' => $leverage,
                'order_type' => $orderType,
                'trigger_price' => $triggerPrice,
                'stop_price' => $triggerPrice,
                'limit_price' => $limitPrice,
                'trailing_delta' => $trailingDelta,
            ]);
        }

        $modeLabel = $marginMode === 'spot' ? '' : ' ('.ucfirst($marginMode).' '.$leverage.'x)';
        $typeLabel = str_replace('_', ' ', ucfirst($orderType)).' '.ucfirst($type);

        return response()->json(['status' => $typeLabel.' order for '.number_format($amount).' units of '.$stock->symbol.$modeLabel.' submitted. Awaiting approval.']);
    }
}
