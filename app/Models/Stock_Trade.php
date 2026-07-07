<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock_Trade extends Model
{
    use HasFactory;

    public $table = 'stock_trades';

    protected $fillable = [
        'name',
        'image',
        'price',
        'volume',
        'changes',
        'symbol',
        'buy',
        'sell',
        'is_vip',
    ];
}
