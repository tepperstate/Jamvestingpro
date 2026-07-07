<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StakingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'symbol', 'apy_percentage', 'min_amount', 'max_amount',
        'lock_days', 'status', 'image', 'description',
    ];

    public function positions()
    {
        return $this->hasMany(StakingPosition::class);
    }
}
