<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DualInvestmentProduct extends Model
{
    protected $fillable = [
        'name', 'underlying_asset', 'deposit_asset', 'direction', 'strike_price', 'settlement_price', 'apy', 'duration_days', 'min_amount', 'max_amount', 'settlement_date', 'settlement_oracle', 'buffer_percent', 'per_withdrawal_percent', 'status', 'image',
    ];

    protected $casts = [
        'settlement_date' => 'datetime',
    ];

    public function dualInvestmentSubscriptions()
    {
        return $this->hasMany(DualInvestmentSubscription::class, 'dual_product_id');
    }
}
