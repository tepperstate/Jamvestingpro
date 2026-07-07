<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetirementPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'tier', 'employer_match_pct', 'vesting_schedule',
        'min_contribution', 'max_contribution', 'features', 'status', 'image', 'description',
    ];

    protected $casts = ['features' => 'array'];

    public function accounts()
    {
        return $this->hasMany(RetirementAccount::class);
    }
}
