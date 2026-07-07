<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class P2pListing extends Model
{
    protected $fillable = [
        'user_id', 'type', 'asset', 'currency', 'price', 'amount', 'min_order', 'max_order', 'payment_methods', 'terms', 'completion_rate', 'total_trades', 'is_admin_listing', 'status', 'buffer_percent', 'per_withdrawal_percent',
    ];

    protected $casts = [
        'payment_methods' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function p2pOrders()
    {
        return $this->hasMany(P2pOrder::class, 'listing_id');
    }
}
