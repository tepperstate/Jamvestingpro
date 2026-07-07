<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MobileAdminController extends Controller
{
    public function dashboard()
    {
        $total_capital = \App\Models\Balance::where('symbol', 'USD')->sum('amount');
        $admin_count = \App\Models\Admin::count();
        $total_users = \App\Models\User::count();
        $recent_users = \App\Models\User::orderBy('id', 'desc')->limit(10)->get();

        return view('mobile-admin.dashboard', compact('total_capital', 'admin_count', 'total_users', 'recent_users'));
    }

    public function users()
    {
        $total_users = \App\Models\User::count();
        $active_users = \App\Models\User::where('status', 'active')->count(); // Assuming 'status' column exists, otherwise will just be total
        $users = \App\Models\User::orderBy('created_at', 'desc')->paginate(15);
        
        return view('mobile-admin.users', compact('users', 'total_users', 'active_users'));
    }

    public function trades()
    {
        $total_volume = \App\Models\Order::sum('amount');
        $active_trades = \App\Models\Order::where('status', 'pending')->count();
        $orders = \App\Models\Order::with('user')->orderBy('id', 'desc')->paginate(15);
        
        return view('mobile-admin.trades', compact('orders', 'total_volume', 'active_trades'));
    }

    public function signals()
    {
        $signals = \App\Models\Signal::orderBy('id', 'desc')->get();
        return view('mobile-admin.signals', compact('signals'));
    }

    public function menu()
    {
        return view('mobile-admin.menu');
    }

    public function kyc()
    {
        $kyc = \App\Models\Doc::with('user')->orderBy('id', 'desc')->paginate(15);
        $total_pending = \App\Models\Doc::where('status', 'pending')->count();
        return view('mobile-admin.kyc', compact('kyc', 'total_pending'));
    }

    public function deposits()
    {
        $deposits = \App\Models\Deposit::with('user')->orderBy('id', 'desc')->paginate(15);
        $total_pending = \App\Models\Deposit::where('status', 'pending')->count();
        return view('mobile-admin.deposits', compact('deposits', 'total_pending'));
    }

    public function withdrawals()
    {
        $withdrawals = \App\Models\Withdrawal::with('user')->orderBy('id', 'desc')->paginate(15);
        $total_pending = \App\Models\Withdrawal::where('status', 'pending')->count();
        return view('mobile-admin.withdrawals', compact('withdrawals', 'total_pending'));
    }

    public function support()
    {
        $tickets = \App\Models\SupportTicket::with('user')->orderBy('id', 'desc')->paginate(15);
        $open_tickets = \App\Models\SupportTicket::where('status', 'open')->count();
        return view('mobile-admin.support', compact('tickets', 'open_tickets'));
    }

    // Phase 4: Advanced Modules
    public function copy_trading()
    {
        $traders = \App\Models\Trader::orderBy('id', 'desc')->paginate(15);
        $active_traders = \App\Models\Trader::count();
        return view('mobile-admin.copy_trading', compact('traders', 'active_traders'));
    }

    public function futures()
    {
        $positions = \App\Models\FuturesPosition::with('user')->orderBy('id', 'desc')->paginate(15);
        $active_positions = \App\Models\FuturesPosition::where('status', 'open')->count();
        return view('mobile-admin.futures', compact('positions', 'active_positions'));
    }

    public function bots()
    {
        $bots = \App\Models\Bot::orderBy('id', 'desc')->paginate(15);
        $total_bots = \App\Models\Bot::count();
        return view('mobile-admin.bots', compact('bots', 'total_bots'));
    }

    public function wallets()
    {
        $wallets = \Illuminate\Support\Facades\DB::table('admin_wallets')->orderBy('id', 'desc')->get();
        return view('mobile-admin.wallets', compact('wallets'));
    }
}
