<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutualFundInvestment extends Model
{
    protected $fillable = [
        'user_id', 'fund_id', 'amount', 'units',
        'nav_at_purchase', 'status', 'invested_at', 'redeemed_at', 'is_demo',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'units' => 'decimal:4',
        'nav_at_purchase' => 'decimal:4',
        'invested_at' => 'datetime',
        'redeemed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fund()
    {
        return $this->belongsTo(MutualFund::class, 'fund_id');
    }

    public function getCurrentValueAttribute()
    {
        return $this->units * $this->fund->nav_price;
    }

    public function getProfitLossAttribute()
    {
        return $this->current_value - $this->amount;
    }
}
