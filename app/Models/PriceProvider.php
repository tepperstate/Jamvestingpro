<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provider_type',
        'api_key',
        'api_secret',
        'base_url',
        'priority',
        'is_active',
        'last_used_at',
        'last_status',
        'last_error',
        'asset_type',
        'category',
        'spread_percentage',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];
}
