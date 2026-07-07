<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment_history extends Model
{
    use HasFactory;

    protected $table = 'investment_history';

    protected $with = ['user', 'package'];

    protected $fillable = [
        'user_id',
        'plan_id',
        'plan_name',
        'amount',
        'current_value',
        'perc',
        'day',
        'status',
        'start_date',
        'end_date',
        'last_credited_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
