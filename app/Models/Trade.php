<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'pair_id',
        'pair',
        'base',
        'base_amount',
        'pair_coin',
        'pair_coin_amount',
        'amount',
        'type',
        'status',
        'rands',
        'color',
        'convert_to_btc',
    ];
}
