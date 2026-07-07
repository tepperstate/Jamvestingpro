<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doc extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'indentity',
        'indentity_back',
        'residency',
        'residency_back',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
