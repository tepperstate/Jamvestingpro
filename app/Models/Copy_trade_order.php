<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Copy_trade_order extends Model
{
    use HasFactory;

    protected $table = 'copy_trade_order';

    public $fillable = [
        'user_id',
        'trade_id',
        'trader_name',
        'exchange',
        'asset_id',
        'trade_time',
        'symbol',
        'amount',
        'win',
        'loss',
        'time',
        'status',
        'modal',
        'admin_status',
        'types',
        'type',
        'p_l',
        'traded_date',
        'expire_date',
        'expire_time',
        'is_auto_renew',
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
