<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signalresult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'signal_id',
        'name',
        'amount',
        'symbol',
        'type',
        'profit',
        'status',
        'win',
    ];
}
