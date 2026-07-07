<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'is_super_admin',
        'is_2fa_exempt',
        'data',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
    ];
}
