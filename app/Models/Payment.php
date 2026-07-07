<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public $table = 'user_payment';

    protected $fillable = [
        'user_id',
        'address',
        'name',
        'status',
    ];
}
