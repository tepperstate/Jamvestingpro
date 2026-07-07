<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trader extends Model
{
    use HasFactory;

    public $fillable = [
        'image',
        'name',
        'country',
        'percentage',
        'amount',
        'win',
        'total_copier',
        'total_trade',
        'twitter',
        'facebook',
        'instagram',
        'linkedin',
        'equity',
        'days',
        'des',
        'min_loss',
        'max_loss',
        'min_win',
        'max_win',
        'action',
        'equity',
        'days',
        'des',
    ];
}
