<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'symbol', 'type', 'amount', 'price', 'total_usd',
        'status', 'admin_notes', 'admin_profit_override', 'admin_loss_override',
        'approved_by', 'approved_at', 'is_demo',
        'margin_mode', 'leverage', 'order_type', 'trigger_price',
        'stop_price', 'limit_price', 'trailing_delta', 'admin_hit_wick',
    ];

    protected $casts = ['approved_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}
