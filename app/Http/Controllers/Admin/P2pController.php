<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\P2pChatMessage;
use App\Models\P2pListing;
use App\Models\P2pOrder;
use App\Models\User;
use App\Services\BinancePriceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class P2pController extends Controller
{
    public function listingsIndex()
    {
        $listings = P2pListing::with('user')->orderBy('id', 'desc')->get();
        $users = User::all();

        return view('admin.p2p_listings', compact('listings', 'users'));
    }

    public function syncFromBinance()
    {
        $prices = BinancePriceService::getPriceMap();
        if (! $prices) {
            return back()->with('error', 'Could not fetch prices from Binance.');
        }

        $assets = ['BTC', 'ETH', 'USDT'];
        $currencies = ['USD', 'EUR', 'GBP'];
        $sellers = User::inRandomOrder()->take(5)->get();

        if ($sellers->isEmpty()) {
            return back()->with('error', 'No users found to act as sellers.');
        }

        $count = 0;

        foreach ($assets as $asset) {
            foreach ($currencies as $currency) {
                $pair = $asset.$currency;
                $price = $prices[$pair] ?? null;

                if ($asset === 'USDT' && $currency === 'USD') {
                    $price = 1.0;
                } elseif ($asset === 'USDT' && $currency === 'EUR') {
                    $price = isset($prices['EURUSDT']) ? (1 / $prices['EURUSDT']) : 0.92;
                } elseif ($asset === 'USDT' && $currency === 'GBP') {
                    $price = isset($prices['GBPUSDT']) ? (1 / $prices['GBPUSDT']) : 0.78;
                }

                if (! $price) {
                    continue;
                }

                foreach (['buy', 'sell'] as $type) {
                    for ($i = 0; $i < 2; $i++) {
                        $seller = $sellers->random();

                        $variance = rand(-200, 200) / 10000;
                        $finalPrice = $price * (1 + $variance);

                        $listing = new P2pListing;
                        $listing->user_id = $seller->id;
                        $listing->type = $type;
                        $listing->asset = $asset;
                        $listing->currency = $currency;
                        $listing->price = round($finalPrice, 2);
                        $listing->amount = rand(10, 1000);
                        $listing->min_order = rand(10, 50);
                        $listing->max_order = rand(100, 10000);
                        $listing->payment_methods = ['Bank Transfer', 'PayPal', 'Zelle'];
                        $listing->terms = 'Fast release. No third party payments.';
                        $listing->completion_rate = rand(80, 100);
                        $listing->total_trades = rand(10, 500);
                        $listing->is_admin_listing = false;
                        $listing->status = 'active';
                        $listing->buffer_percent = 0;
                        $listing->per_withdrawal_percent = 0;
                        $listing->save();

                        $count++;
                    }
                }
            }
        }

        return back()->with('success', "Auto-populated $count listings from Binance prices.");
    }

    public function createListing(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:buy,sell',
            'asset' => 'required',
            'currency' => 'required',
            'price' => 'required|numeric',
            'amount' => 'required|numeric',
            'min_order' => 'required|numeric',
            'max_order' => 'required|numeric',
        ]);

        $listing = new P2pListing;
        $listing->user_id = $request->user_id;
        $listing->type = $request->type;
        $listing->asset = $request->asset;
        $listing->currency = $request->currency;
        $listing->price = $request->price;
        $listing->amount = $request->amount;
        $listing->min_order = $request->min_order;
        $listing->max_order = $request->max_order;
        $listing->payment_methods = json_encode($request->payment_methods ?? ['Bank Transfer']);
        $listing->terms = $request->terms;
        $listing->completion_rate = $request->completion_rate ?? 100.00;
        $listing->total_trades = $request->total_trades ?? 0;
        $listing->is_admin_listing = $request->has('is_admin_listing');
        $listing->status = $request->status ?? 'active';
        $listing->save();

        return back()->with('success', 'P2P Listing created successfully.');
    }

    public function updateListing(Request $request)
    {
        $listing = P2pListing::findOrFail($request->id);
        if ($request->has('price')) {
            $listing->price = $request->price;
        }
        if ($request->has('amount')) {
            $listing->amount = $request->amount;
        }
        if ($request->has('completion_rate')) {
            $listing->completion_rate = $request->completion_rate;
        }
        if ($request->has('total_trades')) {
            $listing->total_trades = $request->total_trades;
        }
        if ($request->has('status')) {
            $listing->status = $request->status;
        }
        $listing->save();

        return back()->with('success', 'P2P Listing updated.');
    }

    public function deleteListing($id)
    {
        P2pListing::findOrFail($id)->delete();

        return back()->with('success', 'Listing deleted.');
    }

    public function ordersIndex()
    {
        $orders = P2pOrder::with(['listing', 'buyer', 'seller'])->orderBy('id', 'desc')->get();

        return view('admin.p2p_orders', compact('orders'));
    }

    public function resolveOrder(Request $request)
    {
        $order = P2pOrder::findOrFail($request->id);
        $order->admin_resolution = $request->admin_resolution;
        $order->admin_notes = $request->admin_notes;

        if ($request->admin_resolution == 'release_to_buyer') {
            $order->status = 'completed';
            $order->escrow_status = 'released';
            $order->completed_at = Carbon::now();
        } elseif ($request->admin_resolution == 'release_to_seller') {
            $order->status = 'completed';
            $order->escrow_status = 'refunded';
            $order->completed_at = Carbon::now();
        } elseif ($request->admin_resolution == 'cancelled') {
            $order->status = 'cancelled';
            $order->escrow_status = 'refunded';
        }

        $order->save();

        return back()->with('success', 'Order resolved successfully.');
    }

    public function updateEscrow(Request $request)
    {
        $order = P2pOrder::findOrFail($request->id);
        $order->escrow_status = $request->escrow_status;
        $order->save();

        return back()->with('success', 'Escrow status updated.');
    }

    public function deleteOrder($id)
    {
        P2pOrder::findOrFail($id)->delete();

        return back()->with('success', 'Order deleted.');
    }

    public function adminChat($id)
    {
        $order = P2pOrder::with(['buyer', 'seller', 'listing'])->findOrFail($id);
        $messages = P2pChatMessage::with('sender')->where('p2p_order_id', $order->id)->orderBy('created_at', 'asc')->get();

        return view('admin.p2p_chat', compact('order', 'messages'));
    }

    public function adminChatSend(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:p2p_orders,id',
            'sender_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $chat = new P2pChatMessage;
        $chat->p2p_order_id = $request->order_id;
        $chat->sender_id = $request->sender_id;
        $chat->message = $request->message;
        $chat->save();

        return back()->with('success', 'Message sent successfully as the selected user.');
    }

    public function generateFakeTrader(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
        ]);

        $email = strtolower($request->first_name.'.'.$request->last_name.rand(100, 999).'@example.com');

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $email;
        $user->password = bcrypt(str_random(16));
        $user->country = $request->country ?? 'USA';
        $user->type = 'user'; // ordinary user
        $user->status = 'active';
        $user->email_verified = true;
        $user->save();

        return back()->with('success', 'Fake trader '.$user->first_name.' '.$user->last_name.' generated successfully with email '.$user->email);
    }
}
