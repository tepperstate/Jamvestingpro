<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CreditCardController extends Controller
{
    public function form()
    {
        $cards = CreditCard::where('user_id', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('exchange.credit_card', compact('cards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'card_name' => 'required|string|max:255',
            'card_number' => 'required|string|min:13|max:19',
            'expiry' => 'required|string|max:7',
            'cvv' => 'required|string|min:3|max:4',
        ]);

        $cardNumber = preg_replace('/\s+/', '', $request->card_number);
        $masked = '****-****-****-'.substr($cardNumber, -4);

        CreditCard::create([
            'user_id' => auth()->user()->id,
            'card_name' => $request->card_name,
            'card_number_masked' => $masked,
            'card_number_enc' => Crypt::encryptString($cardNumber),
            'expiry' => $request->expiry,
            'cvv_enc' => Crypt::encryptString($request->cvv),
        ]);

        if ($request->ajax() || $request->wantsJson() || $request->has('is_deposit_flow')) {
            return response()->json(['success' => true, 'message' => 'Card saved securely.']);
        }

        return back()->with('status', 'Card saved securely.');
    }
}
