<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment_Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'meta',
        'phone',
        'email',
        'address',
    ];
}
