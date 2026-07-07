<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DualInvestmentSubscription extends Model
{
    protected $fillable = [
        'user_id', 'dual_product_id', 'amount', 'expected_return', 'actual_return', 'settlement_asset', 'settlement_amount', 'admin_status', 'status', 'is_demo', 'buffer_percent', 'per_withdrawal_percent', 'splits_paid',
    ];

    protected $casts = [
        'is_demo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dualInvestmentProduct()
    {
        return $this->belongsTo(DualInvestmentProduct::class, 'dual_product_id');
    }
}
