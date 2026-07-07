<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutualFund extends Model
{
    protected $fillable = [
        'name', 'symbol', 'description', 'category', 'min_investment', 'risk_level',
        'annual_return', 'status', 'total_aum', 'nav_price',
        'inception_date', 'image',
    ];

    protected $casts = [
        'min_investment' => 'decimal:2',
        'annual_return' => 'decimal:2',
        'total_aum' => 'decimal:2',
        'nav_price' => 'decimal:4',
        'inception_date' => 'date',
    ];

    public function investments()
    {
        return $this->hasMany(MutualFundInvestment::class, 'fund_id');
    }

    public function activeInvestments()
    {
        return $this->hasMany(MutualFundInvestment::class, 'fund_id')->where('status', 'active');
    }
}
