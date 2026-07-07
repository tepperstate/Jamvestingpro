<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingResponse extends Model
{
    protected $table = 'user_onboarding_responses';

    protected $fillable = [
        'user_id',
        'question_id',
        'response_value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(OnboardingQuestion::class, 'question_id');
    }
}
