<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StakingPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'staking_plan_id', 'amount', 'earned',
        'start_date', 'end_date', 'status', 'is_demo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(StakingPlan::class, 'staking_plan_id');
    }
}
