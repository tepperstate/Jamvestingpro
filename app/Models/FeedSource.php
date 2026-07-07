<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedSource extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'filters' => 'array',
        'active' => 'boolean',
    ];

    public function logs()
    {
        return $this->hasMany(FeedLog::class);
    }
}
