<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScreenshotController extends Controller
{
    public function index()
    {
        $users = DB::table('users')->select('id', 'first_name', 'last_name', 'email')->orderBy('first_name')->get();

        return view('admin.screenshot', compact('users'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'manual_username' => 'nullable|string|max:100',
            'type' => 'required|in:trades,deposits,withdrawals,signals,mutual_funds,vip_stocks,stocks',
            'count' => 'required|integer|min:1|max:50',
            'wins' => 'nullable|integer|min:0|max:50',
            'losses' => 'nullable|integer|min:0|max:50',
            'buy_count' => 'nullable|integer|min:0|max:50',
            'sell_count' => 'nullable|integer|min:0|max:50',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'manual_date' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $user = null;
        if ($request->user_id) {
            $user = DB::table('users')->where('id', $request->user_id)->first();
        } else {
            $user = (object) [
                'first_name' => $request->manual_username ?: 'Guest',
                'last_name' => '',
                'email' => 'manual-entry@system.internal',
            ];
        }

        $data = [];
        $count = (int) $request->count;
        $targetWins = (int) ($request->wins ?? 0);
        $targetLosses = (int) ($request->losses ?? 0);
        $targetBuys = (int) ($request->buy_count ?? 0);
        $targetSells = (int) ($request->sell_count ?? 0);
        $minAmt = (float) ($request->min_amount ?? 100);
        $maxAmt = (float) ($request->max_amount ?? 5000);

        switch ($request->type) {
            case 'trades':
                if ($request->user_id) {
                    $data = DB::table('orders')
                        ->where('user_id', $request->user_id)
                        ->leftJoin('exchanges', 'orders.exchange', '=', 'exchanges.id')
                        ->select('orders.*', 'exchanges.name as exchange_name')
                        ->orderByDesc('orders.id')
                        ->limit($count)
                        ->get()->toArray();
                }

                $currentWins = 0;
                $currentLosses = 0;
                foreach ($data as $row) {
                    if ($row->status === 'win') {
                        $currentWins++;
                    }
                    if ($row->status === 'loss') {
                        $currentLosses++;
                    }
                }

                while (count($data) < $count || ($currentWins < $targetWins) || ($currentLosses < $targetLosses)) {
                    if (count($data) >= $count && ($currentWins >= $targetWins) && ($currentLosses >= $targetLosses)) {
                        break;
                    }

                    $assets = ['BTC/USDT', 'ETH/USDT', 'EUR/USD', 'GBP/JPY', 'XRP/USDT', 'GOLD', 'AAPL', 'TSLA'];
                    $types = ['call', 'put'];

                    $status = 'running';
                    if ($currentWins < $targetWins) {
                        $status = 'win';
                        $currentWins++;
                    } elseif ($currentLosses < $targetLosses) {
                        $status = 'loss';
                        $currentLosses++;
                    } else {
                        $statuses = ['win', 'loss', 'running'];
                        $status = $statuses[array_rand($statuses)];
                    }

                    $asset = $assets[array_rand($assets)];
                    $type = $types[array_rand($types)];
                    $amount = rand($minAmt, $maxAmt);
                    $data[] = (object) [
                        'symbol' => $asset,
                        'type' => $type,
                        'amount' => $amount,
                        'payout' => $status == 'win' ? $amount * 1.8 : ($status == 'loss' ? 0 : $amount),
                        'status' => $status,
                        'created_at' => now()->subMinutes(rand(1, 1440))->toDateTimeString(),
                    ];

                    if (count($data) >= 50) {
                        break;
                    }
                }
                if (count($data) > 50) {
                    $data = array_slice($data, 0, 50);
                }
                break;

            case 'deposits':
                if ($request->user_id) {
                    $data = DB::table('deposits')
                        ->where('user_id', $request->user_id)
                        ->orderByDesc('id')
                        ->limit($count)
                        ->get()->toArray();
                }
                while (count($data) < $count) {
                    $methods = ['Bitcoin', 'Ethereum', 'USDT (TRC20)', 'Bank Transfer', 'Credit Card'];
                    $data[] = (object) [
                        'amount' => rand($minAmt, $maxAmt),
                        'type' => $methods[array_rand($methods)],
                        'status' => rand(0, 1) ? 'success' : 'pending',
                        'trx_id' => 'TRX'.strtoupper(bin2hex(random_bytes(4))),
                        'created_at' => now()->subHours(rand(1, 72))->toDateTimeString(),
                    ];
                }
                break;

            case 'withdrawals':
                if ($request->user_id) {
                    $data = DB::table('withdrawals')
                        ->where('user_id', $request->user_id)
                        ->orderByDesc('id')
                        ->limit($count)
                        ->get()->toArray();
                }
                while (count($data) < $count) {
                    $data[] = (object) [
                        'amount' => rand($minAmt, $maxAmt),
                        'wallet' => '0x'.bin2hex(random_bytes(20)),
                        'status' => rand(0, 1) ? 'success' : 'pending',
                        'trx_id' => 'WTH'.strtoupper(bin2hex(random_bytes(4))),
                        'created_at' => now()->subDays(rand(1, 5))->toDateTimeString(),
                    ];
                }
                break;

            case 'mutual_funds':
                while (count($data) < $count) {
                    $funds = ['Global Tech Fund', 'Sustainable Energy ETF', 'Emerging Markets Alpha', 'Blue Chip Growth', 'Real Estate REIT'];
                    $amt = rand($minAmt, $maxAmt);
                    $profit = $amt * (rand(5, 25) / 100);
                    $data[] = (object) [
                        'fund' => $funds[array_rand($funds)],
                        'invested' => $amt,
                        'profit' => $profit,
                        'growth' => rand(2, 12).'.'.rand(10, 99).'%',
                        'status' => 'completed',
                        'created_at' => $request->manual_date ?: now()->subDays(rand(1, 30))->toDateTimeString(),
                    ];
                }
                break;

            case 'vip_stocks':
                while (count($data) < $count) {
                    $stocks = ['TSLA', 'AAPL', 'NVDA', 'MSFT', 'AMZN', 'GOOGL', 'META'];
                    $amt = rand($minAmt, $maxAmt);
                    $growth = rand(8, 45);
                    $profit = $amt * ($growth / 100);
                    $data[] = (object) [
                        'asset' => $stocks[array_rand($stocks)],
                        'valuation' => $amt + $profit,
                        'profit' => $profit,
                        'growth' => $growth.'.'.rand(10, 99).'%',
                        'type' => 'VIP Allocation',
                        'created_at' => $request->manual_date ?: now()->subHours(rand(1, 48))->toDateTimeString(),
                    ];
                }
                break;

            case 'stocks':
                while (count($data) < $count) {
                    $stocks = ['META', 'NFLX', 'BRK.B', 'JPM', 'V', 'WMT', 'PG', 'KO'];
                    $amt = rand($minAmt, $maxAmt);
                    $randProfit = rand(15, 65);
                    $profit = $amt * ($randProfit / 100);
                    $data[] = (object) [
                        'asset' => $stocks[array_rand($stocks)],
                        'invested' => $amt,
                        'profit' => $profit,
                        'payout' => $amt + $profit,
                        'growth' => '+'.rand(1, 15).'.'.rand(10, 99).'%',
                        'status' => 'settled',
                        'created_at' => $request->manual_date ?: now()->subDays(rand(1, 14))->toDateTimeString(),
                    ];
                }
                break;
        }

        // Apply manual date to all entries if provided and not already set
        if ($request->manual_date) {
            foreach ($data as &$item) {
                if (is_object($item)) {
                    $item->created_at = $request->manual_date;
                }
            }
        }

        return response()->json([
            'user' => $user,
            'type' => $request->type,
            'data' => $data,
            'generated_at' => $request->manual_date ?: now()->toDateTimeString(),
        ]);
    }
}
