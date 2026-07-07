<?php

namespace App\Classes;

use App\Models\UserWallet;

class Wallets
{
    public function wallets($id, $name, $coin)
    {
        UserWallet::create([
            'user_id' => $id,
            'coin_symbol' => $coin,
            'balance' => 0,
            'is_enabled' => true,
        ]);

        return true;
    }
}
