<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiquidityPool extends Model
{
    protected $fillable = [
        'name', 'token_a', 'token_b', 'tvl', 'apy', 'fee_tier', 'admin_fee_share', 'volume_24h', 'token_a_reserve', 'token_b_reserve', 'pool_token_price', 'min_deposit', 'lock_days', 'buffer_percent', 'per_withdrawal_percent', 'status', 'image', 'description',
    ];

    public function liquidityPositions()
    {
        return $this->hasMany(LiquidityPosition::class);
    }
}
