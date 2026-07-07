<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'assets';

    public $fillable = [
        'exchanges_id',
        'symbols',
        'mirror_symbol',
        'image1',
        'image2',
        'percentage',
        'type',
        'profits',
        'buy',
        'sell',
        'changes',
    ];

    public function exchange()
    {
        return $this->hasOne(Exchange::class, 'id', 'exchanges_id');
    }
}
