<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'demo',
        'amount',
        'bitcoin',
        'bonus',
        'bonus_balance',
        'referral',
        'name',
        'symbol',
        'image',
    ];
}
