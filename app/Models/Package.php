<?php

namespace App\Models;

use App\Services\AssetLogoService;
use App\Services\EtfLogoService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    protected $fillable = [
        'name',
        'type',
        'slug',
        'ticker',
        'logo_url',
        'amount',
        'trade',
        'daily_trade',
        'weekly_trade',
        'min_deposit',
        'perc',
        'day',
        'features',
        'image',
        'is_restricted',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function getLogoAttribute()
    {
        if ($this->logo_url) {
            return $this->logo_url;
        }
        if ($this->ticker) {
            return EtfLogoService::getLogoUrl($this->ticker);
        }

        return AssetLogoService::getFallbackUrl($this->name);
    }

    public function packages_lists()
    {
        return $this->hasMany(Packages_lists::class, 'package_id', 'id');
    }
}
