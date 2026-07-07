<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trade_id',
        'trades_id',
        'trader_name',
        'country',
        'amount',
        'commission',
        'win',
        'profit',
        'status',
        'types',
        'approved',
        'symbols',
        'is_auto_renew',
        'is_demo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
