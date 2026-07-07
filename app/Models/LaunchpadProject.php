<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaunchpadProject extends Model
{
    protected $fillable = [
        'name', 'symbol', 'description', 'total_supply', 'tokens_for_sale', 'tokens_sold', 'price_per_token', 'daily_increase_percentage', 'hard_cap', 'soft_cap', 'raised_amount', 'admin_allocation_pct', 'vesting_days', 'start_date', 'end_date', 'listing_date', 'listing_price', 'audit_badge', 'kyc_verified', 'whitepaper_url', 'website_url', 'buffer_percent', 'per_withdrawal_percent', 'status', 'image',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'listing_date' => 'datetime',
        'audit_badge' => 'boolean',
        'kyc_verified' => 'boolean',
    ];

    public function launchpadParticipations()
    {
        return $this->hasMany(LaunchpadParticipation::class);
    }
}
