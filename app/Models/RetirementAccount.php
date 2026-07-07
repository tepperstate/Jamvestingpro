<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetirementAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'retirement_plan_id', 'balance', 'employer_contributions',
        'employee_contributions', 'vested_amount', 'start_date', 'status', 'is_demo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(RetirementPlan::class, 'retirement_plan_id');
    }
}
