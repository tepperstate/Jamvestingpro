<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SystemCoin;
use App\Models\UserWallet;
use App\Services\SwapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwapController extends Controller
{
    protected $swapService;

    public function __construct(SwapService $swapService)
    {
        $this->swapService = $swapService;
    }

    public function index()
    {
        $coins = SystemCoin::where('is_active', true)->get();
        $userWallets = UserWallet::where('user_id', auth()->id())->get()->keyBy('coin_symbol');
        $swapHistory = DB::table('swaps')->where('user_id', auth()->id())->orderByDesc('id')->limit(20)->get();

        $view = $this->isMobileView() ? 'mobile.exchange.swap' : 'exchange.swap';

        return view($view, compact('coins', 'userWallets', 'swapHistory'));
    }

    public function execute(Request $request)
    {
        $request->validate([
            'from_currency' => 'required|string',
            'to_currency' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            $swap = $this->swapService->executeSwap(
                auth()->id(),
                $request->from_currency,
                $request->to_currency,
                $request->amount
            );

            return back()->with('success', 'Swap completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
