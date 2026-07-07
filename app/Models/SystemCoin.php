<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemCoin extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'name',
        'network',
        'is_active',
        'min_swap_amount',
        'fee_percentage',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_swap_amount' => 'decimal:8',
        'fee_percentage' => 'decimal:4',
    ];
}
