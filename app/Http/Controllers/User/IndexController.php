<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Balance;
use App\Models\DcaPlan; // Correct DB facade
use App\Models\DcaSubscription;
use App\Models\DualInvestmentProduct;
use App\Models\DualInvestmentSubscription;
use App\Models\FuturesPosition;
use App\Models\LaunchpadParticipation;
use App\Models\LaunchpadProject;
use App\Models\LiquidityPool;
use App\Models\LiquidityPosition;
use App\Models\LoanPlan;
use App\Models\LoanPosition;
use App\Models\MarginPair;
use App\Models\MarginPosition;
use App\Models\MutualFund;
use App\Models\Noti;
use App\Models\Order;
use App\Models\P2pChatMessage;
use App\Models\P2pListing;
use App\Models\P2pOrder;
use App\Models\Stock_Trade;
use App\Models\User;
use App\Services\AssetLogoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IndexController extends Controller
{
    public function fixMutualFundMetadata()
    {
        // Run migration manually if not already run
        Artisan::call('migrate', ['--force' => true]);

        $funds = MutualFund::all();
        $updated = 0;

        foreach ($funds as $fund) {
            $name = $fund->name;
            $description = $fund->description;
            $searchableText = $name.' '.$description;

            $symbol = 'FUND';
            $category = 'Global';
            $risk_level = 'medium';

            // Extract symbol from parentheses
            if (preg_match('/\(([^)]+)\)/', $name, $matches)) {
                $symbol = $matches[1];
            }

            // Categorization Logic
            if (stripos($searchableText, 'Tech') !== false || stripos($searchableText, 'Software') !== false || stripos($searchableText, 'Information') !== false || stripos($searchableText, 'Innovation') !== false || stripos($searchableText, 'New Economy') !== false) {
                $category = 'Tech';
                $risk_level = 'high';
            } elseif (stripos($searchableText, 'Health') !== false || stripos($searchableText, 'Science') !== false || stripos($searchableText, 'Medicine') !== false || stripos($searchableText, 'Biotech') !== false) {
                $category = 'Health';
                $risk_level = 'high';
            } elseif (stripos($searchableText, 'Real Estate') !== false || stripos($searchableText, 'REIT') !== false) {
                $category = 'Real Estate';
                $risk_level = 'medium';
            } elseif (stripos($searchableText, 'Small Cap') !== false || stripos($searchableText, 'Small-Cap') !== false || stripos($searchableText, 'small-') !== false || stripos($searchableText, 'Micro Cap') !== false) {
                $category = 'Small Cap';
                $risk_level = 'high';
            } elseif (stripos($searchableText, 'Balanced') !== false || stripos($searchableText, 'Allocation') !== false || stripos($searchableText, 'Wellington') !== false || stripos($searchableText, 'mix of stocks and bonds') !== false) {
                $category = 'Balanced';
                $risk_level = 'medium';
            } elseif (stripos($searchableText, 'Index') !== false || stripos($searchableText, '500') !== false || stripos($searchableText, 'S&P') !== false || stripos($searchableText, 'Total Stock Market') !== false) {
                $category = 'Index';
                $risk_level = 'medium';
            } elseif (stripos($searchableText, 'Bond') !== false || stripos($searchableText, 'Income') !== false || stripos($searchableText, 'Fixed') !== false || stripos($searchableText, 'Treasury') !== false) {
                $category = 'Bonds';
                $risk_level = 'low';
            } elseif (stripos($searchableText, 'Growth') !== false || stripos($searchableText, 'Aggressive') !== false || stripos($searchableText, 'Appreciation') !== false || stripos($searchableText, 'Blue Chip') !== false) {
                $category = 'Growth';
                $risk_level = 'high';
            } elseif (stripos($searchableText, 'Value') !== false || stripos($searchableText, 'Undervalued') !== false || stripos($searchableText, 'Dividend') !== false) {
                $category = 'Value';
                $risk_level = 'medium';
            }

            $fund->update([
                'symbol' => $symbol,
                'category' => $category,
                'risk_level' => $risk_level,
            ]);
            $updated++;
        }

        return "Successfully updated $updated Mutual Funds with enhanced metadata.";
    }

    public function fixMutualFundLogos()
    {
        $funds = MutualFund::all();
        $updated = 0;

        $logoMap = [
            'Index' => 'mutual_fund_index_logo.png',
            'Tech' => 'mutual_fund_tech_logo.png',
            'Health' => 'mutual_fund_health_logo.png',
            'Real Estate' => 'mutual_fund_real_estate_logo.png',
            'Bonds' => 'mutual_fund_bonds_logo.png',
            'Growth' => 'mutual_fund_growth_logo.png',
            'Value' => 'mutual_fund_value_logo.png',
            'Small Cap' => 'mutual_fund_small_cap_logo.png',
            'Balanced' => 'mutual_fund_balanced_logo.png',
            'Global' => 'mutual_fund_global_logo.png',
        ];

        foreach ($funds as $fund) {
            $category = $fund->category;
            $image = $logoMap[$category] ?? 'mutual_fund_global_logo.png';

            $fund->update(['image' => $image]);
            $updated++;
        }

        return "Successfully mapped $updated Mutual Funds to vibrant logos based on category.";
    }

    public function index() // email verification
    {return view('exchange.verification');
    }

    public function allStock()
    {
        $data = Stock_Trade::orderBy('id', 'desc')->get();

        return response()->json(['data' => $data]);
    }

    public function assets()
    {
        $data = Asset::all();

        return response()->json(['data' => $data]);
    }

    public function onOffOtp(Request $request)
    {
        $user = User::whereId($request->user)->first();

        if ($user->otp_enabled == '1') {
            User::whereId($request->user)->update([
                'otp_enabled' => '0',
            ]);
        } else {
            User::whereId($request->user)->update([
                'otp_enabled' => '1',
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function verify($email) // email verification
    {$email = User::whereEmail($email)->update([
            'email_verified' => 1,
        ]);

        return view('verify');
    }

    public function trade_history()
    {
        $isDemo = auth()->user()->is_demo;
        $data = Order::with(['asset', 'exchanges'])
            ->where('user_id', auth()->guard('web')->user()->id)
            ->where('is_demo', $isDemo)
            ->orderBy('id', 'desc')
            ->paginate(20);

        $win = Order::where('user_id', auth()->guard('web')->user()->id)
            ->where('is_demo', $isDemo)
            ->where('status', 'win')
            ->sum('p_l');

        $loss = Order::where('user_id', auth()->guard('web')->user()->id)
            ->where('is_demo', $isDemo)
            ->where('status', 'loss')
            ->sum('p_l');

        $trade_amount = Order::where('user_id', auth()->guard('web')->user()->id)
            ->where('is_demo', $isDemo)
            ->where('status', 'win')
            ->sum('amount');

        $real_win = $win - $trade_amount;
        $orderCount = $data->total();

        $viewName = $this->isMobileView() ? 'mobile.exchange.trade_history' : 'exchange.trade_history';

        return view($viewName, [
            'transactions' => $data,
            'win' => $real_win,
            'loss' => $loss,
            'orderCount' => $orderCount,
        ]);
    }

    public function formatNumber($number)
    {
        if ($number >= 1000000000) {
            return number_format($number / 1000000000, 1).'b';
        } elseif ($number >= 1000000) {
            return number_format($number / 1000000, 1).'m';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 1).'k';
        }

        return $number;
    }

    public function home($id = null)
    {
        $isDemo = auth()->user()->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $balance = Balance::where('user_id', auth()->user()->id)->where('symbol', 'usd')->first();
        $usd = $balance ? $balance->$balanceColumn : 0;
        $lock = DB::table('lock_message')->first() ?? (object) ['title' => 'Notice', 'message' => 'System update in progress.'];

        $margin = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->where('status', 'pending')->sum('amount') ?? 0;
        $orderEquity = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->where('status', 'pending')->sum('p_l') ?? 0;
        $equity = $usd + $orderEquity;

        $totalWins = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->where('status', 'win')->sum('p_l');
        $totalLosses = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->where('status', 'loss')->sum('p_l');
        $sumPL = $totalWins - $totalLosses;

        $todayWins = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->whereDate('created_at', Carbon::today())->where('status', 'win')->sum('p_l');
        $todayLosses = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->whereDate('created_at', Carbon::today())->where('status', 'loss')->sum('p_l');
        $today = $todayWins - $todayLosses;

        $data = Order::whereUserId(auth()->guard('web')->user()->id)->where('is_demo', $isDemo)->orderBy('id', 'desc')->limit(10)->get();
        $message = Cache::remember('system_message_1', 60, function () {
            return DB::table('system_message')->where('id', 1)->first();
        });
        $assets = Cache::remember('assets_top_50', 60, function () {
            return DB::table('assets')->limit(50)->get();
        });
        $asset = $assets;
        $cat = Cache::remember('exchanges_top_4', 60, function () {
            return DB::table('exchanges')->limit(4)->orderBy('id', 'desc')->get();
        });

        // Fix: Ensure default_asset is a symbols string, and calculate asset types for the view
        if ($id) {
            $check_id = DB::table('assets')->where('id', $id)->first();
            $default_asset = $check_id ? $check_id->symbols : 'TSLA';
            $exchanges_id = $check_id ? $check_id->exchanges_id : 2;
        } else {
            $default_asset = 'TSLA';
            $check_asset = DB::table('assets')->where('symbols', 'TSLA')->first();
            $exchanges_id = $check_asset ? $check_asset->exchanges_id : 2;
        }

        $symbol = $default_asset;
        $isForex = ($exchanges_id == 1);
        $isCrypto = ($exchanges_id == 2 || str_contains($symbol, 'USDT'));

        $viewName = $this->isMobileView() ? 'mobile.exchange.index' : 'exchange.index';

        return view($viewName, [
            'default_asset' => $default_asset,
            'symbol' => $symbol,
            'isForex' => $isForex,
            'isCrypto' => $isCrypto,
            'cat' => $cat,
            'assets' => $assets,
            'usd' => $usd,
            'trade' => $data,
            'message' => $message,
            'margin' => $margin,
            'equity' => $equity,
            'sumPL' => $sumPL,
            'today' => $today,
            'asset' => $asset,
            'lock' => $lock,
            'exchanges_id' => $exchanges_id,
        ]);
    }

    public function dashboard()
    {
        $isDemo = auth()->user()->is_demo;
        $balanceColumn = $isDemo ? 'demo' : 'amount';

        $balance = Balance::where('user_id', auth()->user()->id)->where('symbol', 'usd')->first();
        $usd = $balance ? $balance->$balanceColumn : 0;
        $lock = DB::table('lock_message')->first() ?? (object) ['title' => 'Notice', 'message' => 'System update in progress.'];

        $margin = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->where('status', 'pending')->sum('amount') ?? 0;
        $orderEquity = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->where('status', 'pending')->sum('p_l') ?? 0;
        $equity = $usd + $orderEquity;

        $totalWins = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->where('status', 'win')->sum('p_l');
        $totalLosses = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->where('status', 'loss')->sum('p_l');
        $sumPL = $totalWins - $totalLosses;

        $todayWins = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->whereDate('created_at', Carbon::today())->where('status', 'win')->sum('p_l');
        $todayLosses = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->whereDate('created_at', Carbon::today())->where('status', 'loss')->sum('p_l');
        $today = $todayWins - $todayLosses;

        // Calculate Strike Rate
        $totalOrders = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->whereIn('status', ['win', 'loss'])->count();
        $winOrders = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->where('status', 'win')->count();
        $strike_rate = $totalOrders > 0 ? round(($winOrders / $totalOrders) * 100, 1) : 0;

        $data = Order::with('asset')->whereUserId(auth()->guard('web')->user()->id)->where('is_demo', $isDemo)->orderBy('id', 'desc')->limit(10)->get();
        $message = Cache::remember('system_message_1', 60, function () {
            return DB::table('system_message')->where('id', 1)->first();
        });

        $requestedSymbols = ['TSLA', 'GOOGL', 'AMZN', 'NVDA', 'META', 'SPOT'];
        $assets = Cache::remember('dashboard_assets_top_50', 60, function () use ($requestedSymbols) {
            return DB::table('assets')
                ->orderByRaw("CASE WHEN symbols IN ('".implode("','", $requestedSymbols)."') THEN 0 ELSE 1 END")
                ->orderBy('id', 'asc')
                ->limit(50)
                ->get();
        });
        $asset = $assets;

        $cat = Cache::remember('exchanges_top_4', 60, function () {
            return DB::table('exchanges')->limit(4)->orderBy('id', 'desc')->get();
        });
        $viewName = $this->isMobileView() ? 'mobile.pages.dashboard' : 'exchange.dashboard';

        $notifications = Noti::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->limit(6)->get();

        return view($viewName, [
            'cat' => $cat,
            'assets' => $assets,
            'usd' => $usd,
            'trade' => $data,
            'message' => $message,
            'margin' => $margin,
            'equity' => $equity,
            'sumPL' => $sumPL,
            'today' => $today,
            'notifications' => $notifications,
            'asset' => $asset,
            'lock' => $lock,
            'strike_rate' => $strike_rate,
        ]);
    }

    public function getAssetBy()
    { // get asset by search
        $asset = DB::table('assets')->get();

        return response()->json(['status' => true, 'data' => $asset]);
    }

    public function getAssetBySearch($id)
    { // get asset by search
        // $asset =  Asset::where('symbols', 'like', "%" . $id . "%")->select('symbols', 'exchanges_id', 'buy', 'sell','image1','image2','percentage')->distinct()->get();

        $input = strtoupper($id);

        if (strlen($input) > 3) {
            $firstSymbol = substr($input, 0, 3);
            $secondSymbol = substr($input, 3);

            $asset = Asset::query()
                ->where(function ($query) use ($firstSymbol, $secondSymbol) {
                    $query->where('symbols', 'LIKE', "%$firstSymbol%")
                        ->where('symbols', 'LIKE', "%$secondSymbol%");
                })
                ->orWhere('symbols', 'LIKE', "%$input%")
                ->select('symbols', 'exchanges_id', 'buy', 'sell', 'image1', 'image2', 'percentage')
                ->distinct()
                ->get();
        } else {
            $asset = Asset::where('symbols', 'LIKE', "%$input%")
                ->select('symbols', 'exchanges_id', 'buy', 'sell', 'image1', 'image2', 'percentage')
                ->distinct()
                ->get();
        }

        $all_asset = Asset::with('exchange')->select('symbols', 'exchanges_id', 'buy', 'sell', 'image1', 'image2', 'percentage')->distinct('symbols')->get();

        if (! $asset) {
            return response()->json(['status' => true, 'data' => $all_asset]);
        } else {
            return response()->json(['status' => true, 'data' => $asset]);
        }
    }

    public function getAssetById($id)
    {
        $asset = Asset::with('exchange')->select('symbols', 'id', 'exchanges_id', 'buy', 'sell', 'image1', 'image2', 'percentage', 'mirror_symbol')->distinct()->where('exchanges_id', $id)->get();
        $all_asset = Asset::with('exchange')->select('symbols', 'exchanges_id', 'id', 'buy', 'sell', 'percentage', 'mirror_symbol')->distinct('symbols')->get();

        $asset->map(function ($item) {
            $item->logo_url = AssetLogoService::getLogoUrl($item->symbols, $item->exchanges_id == 1 ? 'forex' : ($item->exchanges_id == 3 ? 'stock' : 'crypto'), $item->image1 ?? $item->image2 ?? '');

            return $item;
        });
        $all_asset->map(function ($item) {
            $item->logo_url = AssetLogoService::getLogoUrl($item->symbols, $item->exchanges_id == 1 ? 'forex' : ($item->exchanges_id == 3 ? 'stock' : 'crypto'), $item->image1 ?? $item->image2 ?? '');

            return $item;
        });

        if (! $asset || $asset->isEmpty()) {
            return response()->json(['status' => true, 'data' => $all_asset]);
        } else {
            return response()->json(['status' => true, 'data' => $asset]);
        }
    }

    public function trade_js()
    {
        $isDemo = auth()->user()->is_demo;
        $data = Order::with(['asset', 'user', 'exchanges'])->whereUserId(auth()->guard('web')->user()->id)->where('is_demo', $isDemo)->orderBy('id', 'desc')->limit(6)->get();
        $count_trade = Order::where('user_id', auth()->user()->id)->where('is_demo', $isDemo)->where('status', 'pending')->count();

        return response([
            'status' => true,
            'data' => $data,
            'count_trade' => $count_trade,
        ], 200);
    }

    public function user_balance()
    {
        try {
            $user = auth()->user();
            if (! $user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            $isDemo = $user->is_demo;
            $balanceColumn = $isDemo ? 'demo' : 'amount';
            $balance = Balance::where('user_id', $user->id)
                ->where('symbol', 'USD')
                ->first();

            $currentBalance = $balance ? ($balance->$balanceColumn ?? 0) : 0;

            // Unifying Total Platform Balance directly with the USD Wallet (Purchasing Power)
            $equity = $currentBalance;

            return response()->json([
                'data' => number_format($equity, 2, '.', ''),
                'demo' => number_format($balance->demo ?? 0, 2, '.', ''),
                'available' => number_format($balance->amount ?? 0, 2, '.', ''),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Balance fetch failed',
                'data' => '0.00',
                'demo' => '0.00',
                'available' => '0.00',
            ], 200); // Return 200 with fallback data to stop reload loops
        }
    }

    public function futures()
    {
        $positions = FuturesPosition::where('user_id', auth()->id())->get();

        return view('user.futures', compact('positions'));
    }

    public function futuresTrade(Request $request)
    {
        return back()->with('success', 'Trade placed successfully');
    }

    public function margin()
    {
        $positions = MarginPosition::with('marginPair')->where('user_id', auth()->id())->orderBy('id', 'desc')->get();
        $pairs = MarginPair::where('status', 'active')->get();
        if ($pairs->isEmpty()) {
            // Seed a default pair if none exists for demo purposes
            $pairs->push(MarginPair::firstOrCreate(
                ['symbol' => 'BTCUSDT'],
                ['max_leverage' => 100, 'borrow_rate_hourly' => 0.001, 'maintenance_margin' => 5, 'mark_price' => 65000, 'status' => 'active']
            ));
        }

        return view('user.margin', compact('positions', 'pairs'));
    }

    public function marginTrade(Request $request)
    {
        $request->validate([
            'pair_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'leverage' => 'required|numeric|min:1',
            'direction' => 'required|in:long,short',
        ]);

        $pair = MarginPair::find($request->pair_id);
        if (! $pair) {
            return back()->with('error', 'Pair not found');
        }

        $user = auth()->user();
        $balanceColumn = $user->is_demo ? 'demo' : 'amount';
        $balance = Balance::where('user_id', $user->id)->where('symbol', 'USD')->first();

        if (! $balance || $balance->{$balanceColumn} < $request->amount) {
            return back()->with('error', 'Insufficient balance');
        }

        $balance->decrement($balanceColumn, $request->amount);
        $markPrice = $pair->mark_price > 0 ? $pair->mark_price : 100;

        MarginPosition::create([
            'user_id' => $user->id,
            'margin_pair_id' => $pair->id,
            'trade_id' => Str::random(6),
            'direction' => $request->direction,
            'leverage' => $request->leverage,
            'collateral' => $request->amount,
            'borrowed' => $request->amount * $request->leverage,
            'entry_price' => $markPrice,
            'quantity' => ($request->amount * $request->leverage) / $markPrice,
            'interest_accrued' => 0,
            'unrealized_pnl' => 0,
            'realized_pnl' => 0,
            'liquidation_price' => $request->direction === 'long'
                ? $markPrice * (1 - (1 / $request->leverage) + ($pair->maintenance_margin / 100))
                : $markPrice * (1 + (1 / $request->leverage) - ($pair->maintenance_margin / 100)),
            'margin_ratio' => 1 / $request->leverage,
            'status' => 'open',
            'is_demo' => $user->is_demo,
        ]);

        return back()->with('success', 'Margin trade placed successfully');
    }

    public function p2pMarket()
    {
        $listings = P2pListing::with('user')->where('status', 'active')->get();
        $myOrders = P2pOrder::with('listing')->where(function ($q) {
            $q->where('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->orderBy('id', 'desc')->get();
        $viewName = $this->isMobileView() ? 'mobile.exchange.p2p' : 'exchange.p2p';

        return view($viewName, compact('listings', 'myOrders'));
    }

    public function p2pCreateListing(Request $request)
    {
        $request->validate([
            'type' => 'required|in:buy,sell',
            'asset' => 'required|string',
            'currency' => 'required|string',
            'price' => 'required|numeric',
            'amount' => 'required|numeric',
            'min_order' => 'required|numeric',
            'max_order' => 'required|numeric',
            'payment_methods' => 'required|array',
        ]);

        $listing = new P2pListing;
        $listing->user_id = auth()->id();
        $listing->type = $request->type;
        $listing->asset = $request->asset;
        $listing->currency = $request->currency;
        $listing->price = $request->price;
        $listing->amount = $request->amount;
        $listing->min_order = $request->min_order;
        $listing->max_order = $request->max_order;
        $listing->payment_methods = $request->payment_methods;
        $listing->terms = $request->terms;
        $listing->completion_rate = 100.00;
        $listing->total_trades = 0;
        $listing->status = 'active';
        $listing->save();

        return back()->with('success', 'Listing created successfully.');
    }

    public function p2pPlaceOrder(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:p2p_listings,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $listing = P2pListing::findOrFail($request->listing_id);
        $total_fiat = $request->amount * $listing->price;

        $order = new P2pOrder;
        $order->order_id = strtoupper(uniqid('P2P-'));
        $order->listing_id = $listing->id;
        $order->buyer_id = $listing->type === 'sell' ? auth()->id() : $listing->user_id;
        $order->seller_id = $listing->type === 'buy' ? auth()->id() : $listing->user_id;
        $order->amount = $request->amount;
        $order->price = $listing->price;
        $order->total_fiat = $total_fiat;
        $order->escrow_status = 'held';
        $order->status = 'pending';
        $order->is_demo = auth()->user()->is_demo;
        $order->expires_at = now()->addMinutes(15);
        $order->save();

        $listing->amount -= $request->amount;
        if ($listing->amount <= 0) {
            $listing->status = 'completed';
        }
        $listing->save();

        return back()->with('success', 'Order placed. Please proceed to payment.');
    }

    public function p2pConfirmPayment(Request $request)
    {
        $order = P2pOrder::where('id', $request->order_id)->where(function ($q) {
            $q->where('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->firstOrFail();

        if ($order->buyer_id == auth()->id()) {
            $order->payment_confirmed_by_buyer = true;
            $order->status = 'paid';
        } elseif ($order->seller_id == auth()->id()) {
            $order->payment_confirmed_by_seller = true;
            if ($order->payment_confirmed_by_buyer) {
                $order->escrow_status = 'released';
                $order->status = 'completed';
                $order->completed_at = now();
            }
        }
        $order->save();

        return back()->with('success', 'Payment status updated.');
    }

    public function p2pDispute(Request $request)
    {
        $order = P2pOrder::where('id', $request->order_id)->where(function ($q) {
            $q->where('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->firstOrFail();

        $order->status = 'disputed';
        $order->escrow_status = 'disputed';
        $order->dispute_reason = $request->dispute_reason;
        $order->save();

        return back()->with('success', 'Dispute opened. An admin will review the transaction.');
    }

    public function p2pChat($order_id)
    {
        $order = P2pOrder::where('id', $order_id)->where(function ($q) {
            $q->where('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->firstOrFail();

        $messages = P2pChatMessage::with('sender')->where('p2p_order_id', $order->id)->orderBy('created_at', 'asc')->get();

        return view('exchange.p2p_chat', compact('order', 'messages'));
    }

    public function p2pChatSend(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:p2p_orders,id',
            'message' => 'required|string|max:1000',
        ]);

        $order = P2pOrder::where('id', $request->order_id)->where(function ($q) {
            $q->where('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->firstOrFail();

        $chat = new P2pChatMessage;
        $chat->p2p_order_id = $order->id;
        $chat->sender_id = auth()->id();
        $chat->message = $request->message;
        $chat->save();

        return back();
    }

    public function liquidityIndex()
    {
        $pools = LiquidityPool::where('status', 'active')->get();
        $positions = LiquidityPosition::with('pool')->where('user_id', auth()->id())->get();

        return view('user.liquidity', compact('pools', 'positions'));
    }

    public function liquidityDeposit(Request $request)
    {
        $request->validate([
            'pool_id' => 'required|exists:liquidity_pools,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $pool = LiquidityPool::findOrFail($request->pool_id);

        if ($request->amount < $pool->min_deposit) {
            return back()->with('error', 'Minimum deposit is $'.number_format($pool->min_deposit, 2));
        }

        // In a real system, we'd deduct from wallet. Here we simulate the deposit.
        $position = LiquidityPosition::where('user_id', auth()->id())
            ->where('liquidity_pool_id', $pool->id)
            ->where('status', 'active')
            ->first();

        if ($position) {
            $position->amount_deposited += $request->amount;
            $position->current_value += $request->amount;
            $position->save();
        } else {
            LiquidityPosition::create([
                'user_id' => auth()->id(),
                'liquidity_pool_id' => $pool->id,
                'amount_deposited' => $request->amount,
                'lp_tokens' => $request->amount / $pool->pool_token_price,
                'current_value' => $request->amount,
                'status' => 'active',
                'start_date' => now(),
                'unlock_date' => now()->addDays($pool->lock_days),
            ]);
        }

        // Inflate pool TVL artificially
        $pool->tvl += $request->amount;
        $pool->save();

        return back()->with('success', 'Successfully added liquidity to '.$pool->name);
    }

    public function liquidityWithdraw(Request $request)
    {
        $request->validate([
            'position_id' => 'required|exists:liquidity_positions,id',
        ]);

        $position = LiquidityPosition::where('id', $request->position_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($position->unlock_date && now()->isBefore($position->unlock_date)) {
            return back()->with('error', 'Position is locked until '.$position->unlock_date->format('Y-m-d'));
        }

        $position->status = 'withdrawn';
        $position->save();

        // Deflate pool TVL
        $pool = $position->liquidityPool;
        if ($pool) {
            $pool->tvl -= $position->current_value;
            $pool->save();
        }

        return back()->with('success', 'Liquidity successfully withdrawn from '.($pool->name ?? 'pool'));
    }

    public function launchpad()
    {
        $projects = LaunchpadProject::whereIn('status', ['active', 'upcoming'])->orderBy('id', 'desc')->get();
        $completedProjects = LaunchpadProject::where('status', 'completed')->orderBy('id', 'desc')->get();
        $participations = LaunchpadParticipation::with('project')->where('user_id', auth()->id())->get();

        return view('exchange.launchpad', compact('projects', 'completedProjects', 'participations'));
    }

    public function launchpadParticipate(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
        ]);

        $user = auth()->user();
        $balanceColumn = $user->is_demo ? 'demo' : 'amount';
        $balance = Balance::where('user_id', $user->id)->where('symbol', 'usd')->first();

        if (! $balance || $balance->{$balanceColumn} < $request->amount) {
            return back()->with('error', 'Insufficient balance. You cannot buy more than your available funds.');
        }

        $project = LaunchpadProject::findOrFail($id);

        // Deduct balance
        $balance->decrement($balanceColumn, $request->amount);

        $part = new LaunchpadParticipation;
        $part->user_id = $user->id;
        $part->launchpad_project_id = $project->id;
        $part->amount_invested = $request->amount;
        $part->tokens_allocated = $request->amount / $project->price_per_token;
        $part->current_value = $request->amount;
        $part->pnl = 0;
        $part->status = 'vesting';
        $part->vesting_end_date = Carbon::now()->addDays($project->vesting_days);
        $part->save();

        $project->raised_amount += $request->amount;
        $project->tokens_sold += $part->tokens_allocated;
        $project->save();

        return back()->with('success', 'Successfully participated in ICO');
    }

    public function loans()
    {
        $plans = LoanPlan::where('status', 'active')->orderBy('id', 'desc')->get();
        $positions = LoanPosition::with('plan')->where('user_id', auth()->id())->get();
        if ($this->isMobileView()) {
            return view('mobile.user.loans', compact('plans', 'positions'));
        }

        return view('user.loans', compact('plans', 'positions'));
    }

    public function loanBorrow(Request $request, $id)
    {
        // Basic mock for borrowing
        $plan = LoanPlan::findOrFail($id);
        $pos = new LoanPosition;
        $pos->user_id = auth()->id();
        $pos->loan_plan_id = $plan->id;
        $pos->loan_id = 'LOAN-'.strtoupper(uniqid());
        $pos->collateral_amount = $request->collateral_amount;

        // Assume 1:1 if collateral_price is missing for demo, ideally we fetch it
        $collateral_value = $request->collateral_amount * ($plan->collateral_price > 0 ? $plan->collateral_price : 1);
        $pos->collateral_value = $collateral_value;
        $pos->loan_amount = $request->loan_amount;
        $pos->current_ltv = ($request->loan_amount / $collateral_value) * 100;
        $pos->interest_accrued = 0;
        $pos->total_repaid = 0;
        $pos->remaining_balance = $request->loan_amount;
        $pos->liquidation_price = 0; // Mock calculation
        $pos->admin_status = 'healthy';
        $pos->status = 'active';
        $pos->start_date = Carbon::now();
        $pos->maturity_date = Carbon::now()->addDays($plan->duration_days);
        $pos->save();

        return back()->with('success', 'Loan acquired successfully');
    }

    public function dualInvestment()
    {
        $products = DualInvestmentProduct::where('status', 'active')->get();
        $subscriptions = DualInvestmentSubscription::with('dualInvestmentProduct')->where('user_id', auth()->id())->orderBy('id', 'desc')->get();
        if ($this->isMobileView()) {
            return view('mobile.user.dual_investment', compact('products', 'subscriptions'));
        }

        return view('user.dual_investment', compact('products', 'subscriptions'));
    }

    public function buyDualInvestment(Request $request)
    {
        $request->validate([
            'dual_product_id' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);

        $product = DualInvestmentProduct::find($request->dual_product_id);

        DualInvestmentSubscription::create([
            'user_id' => auth()->id() ?? 1,
            'dual_product_id' => $product->id,
            'amount' => $request->amount,
            'expected_return' => $request->amount * (1 + ($product->apy / 100)),
            'status' => 'active',
        ]);

        return back()->with('status', 'Subscribed to Dual Investment successfully.');
    }

    public function dca()
    {
        $plans = DcaPlan::where('status', 'active')->get();

        return view('user.dca', compact('plans'));
    }

    public function dcaSubscribe(Request $request)
    {
        $request->validate([
            'dca_plan_id' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);

        $plan = DcaPlan::find($request->dca_plan_id);

        DcaSubscription::create([
            'user_id' => auth()->id() ?? 1,
            'dca_plan_id' => $plan->id,
            'amount_per_purchase' => $request->amount,
            'next_execution' => Carbon::now()->addDay()->setHour($plan->execution_hour ?? 9),
            'status' => 'active',
        ]);

        return back()->with('status', 'DCA Bot started successfully.');
    }

    public function execute_result_after_time()
    {
        return response()->json(['status' => 'success', 'message' => 'Executed']);
    }

    public function execute_result_after_time_for_copy_trade()
    {
        return response()->json(['status' => 'success', 'message' => 'Executed copy trade']);
    }

    public function how()
    {
        return back()->with('status', 'How it works section is under construction.');
    }
}
