<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pair extends Model
{
    use HasFactory;

    public $fillable = [
        'coin_id',
        'base',
        'pair',
        'pair_coin',
        'last_price',
        'percentage',
        'status',
    ];
}
