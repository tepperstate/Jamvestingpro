<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $fillable = [
        'trade_id',
        'user_id',
        'asset_id',
        'exchange',
        'symbol',
        'amount',
        'leverage',
        'win',
        'loss',
        'stop_loss',
        'take_profit',
        'expire_time',
        'time',
        'status',
        'expire_date',
        'type',
        'types',
        'p_l',
        'traded_date',
        'modal',
        'strike_rate',
        'is_demo',
        'duration',
        'unit',
        'trade_interval',
        'outcome_preset',
        'is_admin_signal',
        'hedge_ratio_counter',
        'approval_status',
    ];

    public function asset()
    {
        return $this->hasOne(Asset::class, 'id', 'asset_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exchanges()
    {
        return $this->hasOne(Exchange::class, 'id', 'exchange');
    }
}
