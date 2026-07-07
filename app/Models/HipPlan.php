<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HipPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_type',
        'tier_level',
        'min_investment',
        'smart_logic_description',
    ];
}
