<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'tier', 'min_amount', 'max_amount', 'interest_rate',
        'duration_months', 'features', 'status', 'image',
    ];

    protected $casts = ['features' => 'array'];

    public function savings()
    {
        return $this->hasMany(StudentSaving::class);
    }
}
