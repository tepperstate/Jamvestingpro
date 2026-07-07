<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class P2pOrder extends Model
{
    protected $fillable = [
        'order_id', 'listing_id', 'buyer_id', 'seller_id', 'amount', 'price', 'total_fiat', 'escrow_status', 'payment_confirmed_by_buyer', 'payment_confirmed_by_seller', 'admin_resolution', 'dispute_reason', 'admin_notes', 'status', 'is_demo', 'expires_at', 'completed_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function listing()
    {
        return $this->belongsTo(P2pListing::class, 'listing_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
