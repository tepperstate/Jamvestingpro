<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiquidityPosition extends Model
{
    protected $fillable = [
        'user_id', 'liquidity_pool_id', 'amount_deposited', 'lp_tokens', 'earned_fees', 'earned_rewards', 'impermanent_loss', 'current_value', 'admin_status', 'status', 'start_date', 'unlock_date', 'is_demo', 'buffer_percent', 'per_withdrawal_percent', 'splits_paid',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'unlock_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function liquidityPool()
    {
        return $this->belongsTo(LiquidityPool::class);
    }
}
