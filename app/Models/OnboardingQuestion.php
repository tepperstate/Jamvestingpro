<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'input_type',
        'depends_on',
        'is_required',
        'sort_order',
        'section',
        'question_key',
    ];

    public function options()
    {
        return $this->hasMany(OnboardingOption::class, 'question_id');
    }

    public function responses()
    {
        return $this->hasMany(UserOnboardingResponse::class, 'question_id');
    }
}
