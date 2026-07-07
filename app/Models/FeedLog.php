<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function source()
    {
        return $this->belongsTo(FeedSource::class, 'feed_source_id');
    }
}
