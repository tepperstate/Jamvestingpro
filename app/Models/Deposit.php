<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'trx_id',
        'pay_currency',
        'name',
        'amount',
        'status',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
