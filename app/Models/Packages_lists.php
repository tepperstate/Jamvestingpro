<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packages_lists extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'data',
    ];

    public function packages()
    {
        return $this->hasOne(Package::class);
    }
}
