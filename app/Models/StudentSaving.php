<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSaving extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'student_plan_id', 'amount', 'earned',
        'start_date', 'maturity_date', 'status', 'is_demo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(StudentPlan::class, 'student_plan_id');
    }
}
